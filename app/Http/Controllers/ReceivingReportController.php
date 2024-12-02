<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\ReceivingReport;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ReceivingReportController extends Controller
{
    public function index()
    {
        $query = ReceivingReport::with(['branch', 'category']);
        
        // Filter by user's branch_id if it exists
        if (auth()->user()->branch_id) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $receivingReports = $query->select([
                'receiving_report_number',
                'branch_id',
                'date_received',
                \DB::raw('SUM(quantity) as total_quantity'),
                \DB::raw('COUNT(*) as entries_count'),
                \DB::raw('MIN(id) as id')
            ])
            ->groupBy('receiving_report_number', 'branch_id', 'date_received')
            ->latest('date_received')
            ->paginate(10);

        return view('receiving-reports.index', compact('receivingReports'));
    }

    public function create()
    {
        if (auth()->user()->branch_id) {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            $branches = Branch::orderBy('name')->get();
        }
        
        $categories = Category::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('receiving-reports.create', compact('branches', 'categories', 'vendors'));
    }

    public function store(Request $request)
    {
        // Validate header information
        $request->validate([
            'receiving_report_number' => 'required|string|max:255|unique:receiving_reports,receiving_report_number',
            'date_received' => 'required|date',
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) {
                    if (auth()->user()->branch_id && auth()->user()->branch_id != $value) {
                        $fail('You can only create receiving reports for your assigned branch.');
                    }
                },
            ],
            // Validate each item in the array
            'items' => 'required|array|min:1',
            'items.*.item_code' => [
                'required',
                'string',
                'max:255',
                'distinct', // Ensures unique item codes within the request
            ],
            'items.*.name' => 'required|string|max:255',
            'items.*.barcode' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string|max:50',
            'items.*.category_id' => 'required|exists:categories,id',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
        ]);

        foreach ($request->items as $item) {
            // Check if product exists
            $product = Product::where('barcode', $item['barcode'])
                ->where('branch_id', $request->branch_id)
                ->first();

            // If product doesn't exist, create it
            if (!$product) {
                $product = Product::create([
                    'name' => $item['name'],
                    'barcode' => $item['barcode'],
                    'category_id' => $item['category_id'],
                    'branch_id' => $request->branch_id,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                // Initialize inventory for the new product
                Inventory::create([
                    'product_id' => $product->id,
                    'branch_id' => $request->branch_id,
                    'quantity' => 0,
                ]);
            }

            // Create receiving report for each item
            ReceivingReport::create([
                'receiving_report_number' => $request->receiving_report_number,
                'item_code' => $item['item_code'],
                'name' => $item['name'],
                'barcode' => $item['barcode'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'date_received' => $request->date_received,
                'branch_id' => $request->branch_id,
                'category_id' => $item['category_id'],
                'vendor_id' => $item['vendor_id'] ?? null,
            ]);
        }

        if ($request->input('action') === 'save_and_new') {
            return redirect()->route('receiving-reports.create')
                ->with('success', 'Receiving report created successfully. Create another one.');
        }

        return redirect()->route('receiving-reports.index')
            ->with('success', 'Receiving report created successfully.');
    }

    public function show(ReceivingReport $receivingReport)
    {
        // Check if user has branch_id and if it matches the receiving report's branch
        if (auth()->user()->branch_id && auth()->user()->branch_id !== $receivingReport->branch_id) {
            abort(403, 'You can only view receiving reports from your assigned branch.');
        }

        $groupedReports = ReceivingReport::where('receiving_report_number', $receivingReport->receiving_report_number)
            ->orderBy('date_received')
            ->get();

        return view('receiving-reports.show', compact('receivingReport', 'groupedReports'));
    }

    public function edit(ReceivingReport $receivingReport)
    {
        $branches = Branch::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $vendors = Vendor::orderBy('name')->get();

        return view('receiving-reports.edit', compact('receivingReport', 'branches', 'categories', 'vendors'));
    }

    public function update(Request $request, ReceivingReport $receivingReport)
    {
        $validated = $request->validate([
            'receiving_report_number' => 'required|string|max:255|unique:receiving_reports,receiving_report_number,' . $receivingReport->id,
            'item_code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'date_received' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'category_id' => 'required|exists:categories,id',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $receivingReport->update($validated);

        return redirect()->route('receiving-reports.show', $receivingReport)
            ->with('success', 'Receiving report updated successfully.');
    }

    public function destroy(ReceivingReport $receivingReport)
    {
        $receivingReport->delete();

        return redirect()->route('receiving-reports.index')
            ->with('success', 'Receiving report deleted successfully.');
    }
} 