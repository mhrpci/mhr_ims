<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\ReceivingReport;
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
        $vendors = Vendor::all();

        $receivingReportsQuery = ReceivingReport::whereDoesntHave('stockIn');
        
        if ($user->isBranchRestricted()) {
            $branches = Branch::where('id', $user->branch_id)->get();
            $products = Product::whereHas('inventories', function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })->get();
            $receivingReportsQuery->where('branch_id', $user->branch_id);
        } else {
            $branches = Branch::all();
            $products = Product::all();
        }

        $receivingReports = $receivingReportsQuery->get();

        return view('stock_ins.create', compact('products', 'vendors', 'branches', 'receivingReports'));
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
            'receiving_report_id' => 'required|exists:receiving_reports,id',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'lot_number' => 'required|string|unique:stock_ins,lot_number',
            'expiration_date' => 'nullable|date',
            'note' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
            'date' => 'required|date',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
        ]);

        $receivingReport = ReceivingReport::findOrFail($validatedData['receiving_report_id']);
        
        if ($receivingReport->stockIn()->exists()) {
            return redirect()->back()
                ->withErrors(['receiving_report_id' => 'This receiving report has already been used'])
                ->withInput();
        }

        if ($receivingReport->branch_id != $validatedData['branch_id']) {
            return redirect()->back()
                ->withErrors(['branch_id' => 'Branch must match the receiving report'])
                ->withInput();
        }

        $product = Product::findOrFail($validatedData['product_id']);
        if ($product->name != $receivingReport->name || $product->barcode != $receivingReport->barcode) {
            return redirect()->back()
                ->withErrors(['product_id' => 'Product must match the receiving report details'])
                ->withInput();
        }

        if ($validatedData['quantity'] != $receivingReport->quantity) {
            return redirect()->back()
                ->withErrors(['quantity' => 'Quantity must match the receiving report'])
                ->withInput();
        }

        if ($validatedData['unit'] != $receivingReport->unit) {
            return redirect()->back()
                ->withErrors(['unit' => 'Unit must match the receiving report'])
                ->withInput();
        }

        if ($request->filled('unit_price')) {
            $validatedData['total_price'] = $validatedData['unit_price'] * $validatedData['quantity'];
        }

        $validatedData['created_by'] = $user->id;
        $validatedData['updated_by'] = $user->id;

        DB::transaction(function () use ($validatedData, $request, $user) {
            $stockIn = StockIn::create($validatedData);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $fileName = time() . '_' . $originalName;
                    $filePath = $file->storeAs('stock-in-attachments', $fileName, 'public');
                    
                    $stockIn->attachments()->create([
                        'file_name' => $fileName,
                        'original_name' => $originalName,
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'uploaded_by' => $user->id
                    ]);
                }
                
                $stockIn->update(['has_attachments' => true]);
            }

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

    public function destroy(StockIn $stockIn)
    {
        return redirect()->route('stock_ins.index')->with('error', 'Deleting Stock In entries is not allowed.');
    }
}
