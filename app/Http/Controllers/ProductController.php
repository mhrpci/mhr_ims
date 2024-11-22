<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Customer;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductExport;
use App\Imports\ProductImport;

class ProductController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Product::with('category');

        if ($user->isBranchRestricted()) {
            $query->where('branch_id', $user->branch_id);
        }

        $products = $query->get();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->canCreateProduct()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        $branches = $user->isBranchRestricted()
            ? Branch::where('id', $user->branch_id)->get()
            : Branch::all();

        return view('products.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->canCreateProduct()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'barcode' => 'required|unique:products',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only create products for your assigned branch.');
                    }
                },
            ],
        ]);

        $validatedData['created_by'] = Auth::id();
        $validatedData['updated_by'] = Auth::id();

        $product = Product::create($validatedData);

        // Initialize inventory for the product's branch
        Inventory::create([
            'product_id' => $product->id,
            'branch_id' => $product->branch_id,
            'quantity' => 0,
        ]);

        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('products.create')
                ->with('success', 'Product created successfully. Create another one.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $user = Auth::user();

        if (!$user->canManageProduct($product)) {
            abort(403, 'Unauthorized access to this product.');
        }

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $user = Auth::user();

        if (!$user->canEditProduct() || !$user->canManageProduct($product)) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::all();
        $branches = $user->isBranchRestricted()
            ? Branch::where('id', $user->branch_id)->get()
            : Branch::all();

        return view('products.edit', compact('product', 'categories', 'branches'));
    }

    public function update(Request $request, Product $product)
    {
        $user = Auth::user();

        if (!$user->canEditProduct() || !$user->canManageProduct($product)) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only update products for your assigned branch.');
                    }
                },
            ],
        ]);

        $validatedData['updated_by'] = Auth::id();

        $product->update($validatedData);
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $user = Auth::user();

        if (!$user->canDeleteProduct() || !$user->canManageProduct($product)) {
            abort(403, 'Unauthorized action.');
        }

        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function scanBarcode(Request $request)
    {
        $user = Auth::user();
        $barcode = $request->input('barcode');

        $query = Product::where('barcode', $barcode)
            ->with(['category', 'inventories.branch', 'stockIns.vendor', 'stockOuts.customer']);

        if ($user->isBranchRestricted()) {
            $query->where('branch_id', $user->branch_id);
        }

        $product = $query->first();

        if ($product) {
            // Get branches based on user restrictions
            if ($user->isBranchRestricted()) {
                $branches = Branch::where('id', $user->branch_id)->select('id', 'name')->get();
            } else {
                $branches = Branch::select('id', 'name')->get();
            }

            $customers = Customer::select('id', 'name')->get();
            $vendors = Vendor::select('id', 'name')->get();

            // Calculate current inventory for each branch
            $inventories = $product->inventories->map(function ($inventory) {
                return [
                    'branch_id' => $inventory->branch_id,
                    'quantity' => $inventory->quantity,
                ];
            });

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'description' => $product->description,
                    'category' => [
                        'id' => $product->category->id,
                        'name' => $product->category->name,
                    ],
                ],
                'inventories' => $inventories,
                'branches' => $branches,
                'customers' => $customers,
                'vendors' => $vendors,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function apiStockIn(Request $request)
    {
        $validatedData = $request->validate([
            'barcode' => 'required|exists:products,barcode',
            'vendor_id' => 'required|exists:vendors,id',
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('barcode', $validatedData['barcode'])->firstOrFail();

        $stockIn = StockIn::create([
            'product_id' => $product->id,
            'vendor_id' => $validatedData['vendor_id'],
            'branch_id' => $validatedData['branch_id'],
            'quantity' => $validatedData['quantity'],
            'date' => now(),
        ]);

        $inventory = Inventory::firstOrCreate(
            [
                'product_id' => $product->id,
                'branch_id' => $validatedData['branch_id']
            ],
            ['quantity' => 0]
        );

        $inventory->quantity += $validatedData['quantity'];
        $inventory->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock In recorded successfully',
            'stock_in' => $stockIn
        ]);
    }

    public function apiStockOut(Request $request)
    {
        $validatedData = $request->validate([
            'barcode' => 'required|exists:products,barcode',
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::where('barcode', $validatedData['barcode'])->firstOrFail();

        $inventory = Inventory::where('product_id', $product->id)
                              ->where('branch_id', $validatedData['branch_id'])
                              ->first();

        if (!$inventory || $inventory->quantity < $validatedData['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock for this product in the selected branch.'
            ], 400);
        }

        $stockOut = StockOut::create([
            'product_id' => $product->id,
            'customer_id' => $validatedData['customer_id'],
            'branch_id' => $validatedData['branch_id'],
            'quantity' => $validatedData['quantity'],
            'date' => now(),
        ]);

        $inventory->quantity -= $validatedData['quantity'];
        $inventory->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock Out recorded successfully',
            'stock_out' => $stockOut
        ]);
    }

    public function export()
    {
        return Excel::download(new ProductExport, 'products-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240', // max 10MB
        ]);

        try {
            Excel::import(new ProductImport, $request->file('file'));

            return redirect()->route('products.index')
                ->with('success', 'Products imported successfully.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = collect($failures)->map(function ($failure) {
                return "Row {$failure->row()}: {$failure->errors()[0]}";
            })->join('<br>');

            return redirect()->back()
                ->with('error', 'Import failed.<br>' . $errors)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred during import: ' . $e->getMessage())
                ->withInput();
        }
    }
}
