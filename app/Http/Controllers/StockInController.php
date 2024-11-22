<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Branch;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockInController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isBranchRestricted()) {
            $stockIns = StockIn::with(['product', 'vendor', 'branch'])
                ->where('branch_id', $user->branch_id)
                ->get();
        } else {
            $stockIns = StockIn::with(['product', 'vendor', 'branch'])->get();
        }

        return view('stock_ins.index', compact('stockIns'));
    }

    public function create()
    {
        $user = Auth::user();
        $products = Product::all();
        $vendors = Vendor::all();

        if ($user->isBranchRestricted()) {
            $branches = Branch::where('id', $user->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('stock_ins.create', compact('products', 'vendors', 'branches'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'vendor_id' => 'nullable',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->isBranchRestricted() && $value != $user->branch_id) {
                        $fail('You can only add stock to your assigned branch.');
                    }
                },
            ],
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
        ]);


        $validatedData['created_by'] = $user->id;
        $validatedData['updated_by'] = $user->id;

        DB::transaction(function () use ($validatedData) {
            StockIn::create($validatedData);

            $inventory = Inventory::firstOrCreate(
                [
                    'product_id' => $validatedData['product_id'],
                    'branch_id' => $validatedData['branch_id']
                ],
                ['quantity' => 0]
            );

            $inventory->quantity += $validatedData['quantity'];
            $inventory->save();
        });

        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('stock_ins.create')
                ->with('success', 'Stock In recorded successfully. Create another one.');
        }

        return redirect()->route('stock_ins.index')
            ->with('success', 'Stock In recorded successfully.');
    }

    public function show(StockIn $stockIn)
    {
        $user = Auth::user();

        if ($user->isBranchRestricted() && $stockIn->branch_id !== $user->branch_id) {
            abort(403, 'Unauthorized access to this stock in record.');
        }

        return view('stock_ins.show', compact('stockIn'));
    }

    // Edit and Update methods are typically not needed for StockIn as it's usually a one-time transaction

    public function destroy(StockIn $stockIn)
    {
        // Deleting StockIn entries is usually not recommended.
        // Instead, consider adding a 'void' or 'cancelled' status if needed.
        return redirect()->route('stock_ins.index')->with('error', 'Deleting Stock In entries is not allowed.');
    }
}
