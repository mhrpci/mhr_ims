<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Branch;
use App\Models\Inventory;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockTransfer;
use App\Models\ReceivingReport;
use App\Models\ForPhss;
use App\Exports\ReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get branches based on user access
        $branches = $user->branch_id 
            ? Branch::where('id', $user->branch_id)->get()
            : Branch::orderBy('name')->get();
        
        // Get reports based on user's branch access
        $reports = Report::with(['branch', 'generatedBy'])
            ->when($user->branch_id, fn($q) => $q->where('branch_id', $user->branch_id))
            ->latest()
            ->paginate(10);
        
        return view('reports.index', compact('reports', 'branches'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:inventory,stock_in,stock_out,stock_transfer,receiving,phss',
            'branch_id' => 'nullable|exists:branches,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'format' => 'required|in:pdf,excel',
            'scope' => 'required|in:specific,all'
        ]);

        $user = Auth::user();
        $branchId = null;

        // Handle branch access
        if ($user->branch_id) {
            if ($request->scope === 'all' || ($request->branch_id && $request->branch_id != $user->branch_id)) {
                return back()->with('error', 'You can only generate reports for your assigned branch.');
            }
            $branchId = $user->branch_id;
        } else {
            $branchId = $request->scope === 'specific' ? $request->branch_id : null;
        }

        try {
            DB::beginTransaction();

            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $dateTo = Carbon::parse($request->date_to)->endOfDay();

            // Generate report data based on type
            $data = match($request->report_type) {
                'inventory' => $this->getInventoryReport($branchId),
                'stock_in' => $this->getStockInReport($branchId, $dateFrom, $dateTo),
                'stock_out' => $this->getStockOutReport($branchId, $dateFrom, $dateTo),
                'stock_transfer' => $this->getTransferReport($branchId, $dateFrom, $dateTo),
                'receiving' => $this->getReceivingReport($branchId, $dateFrom, $dateTo),
                'phss' => $this->getPhssReport($branchId, $dateFrom, $dateTo),
            };

            // Create report record
            $report = Report::create([
                'report_number' => 'REP' . time(),
                'report_type' => $request->report_type,
                'branch_id' => $branchId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'generated_by' => $user->id,
                'data' => $data,
                'total_records' => count($data),
                'total_amount' => collect($data)->sum('total_amount') ?? 0,
                'parameters' => $request->only(['scope', 'report_type', 'branch_id'])
            ]);

            DB::commit();

            // Generate report in requested format
            return $request->format === 'pdf' 
                ? $this->downloadPdf($report)
                : $this->downloadExcel($report);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }

    private function getInventoryReport($branchId)
    {
        return Inventory::with(['product.category', 'branch'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get()
            ->map(fn($item) => [
                'product_code' => $item->product->barcode,
                'product_name' => $item->product->name,
                'category' => $item->product->category->name,
                'quantity' => $item->quantity,
                'branch' => $item->branch->name,
                'last_updated' => $item->updated_at->format('Y-m-d H:i:s')
            ])
            ->toArray();
    }

    private function getStockInReport($branchId, $dateFrom, $dateTo)
    {
        return StockIn::with(['product', 'branch', 'vendor', 'creator'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get()
            ->map(fn($item) => [
                'date' => $item->date->format('Y-m-d'),
                'reference' => $item->stock_in_number,
                'product' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_amount' => $item->total_price,
                'vendor' => $item->vendor?->name ?? 'N/A',
                'branch' => $item->branch->name,
                'created_by' => $item->creator->username
            ])
            ->toArray();
    }

    private function getStockOutReport($branchId, $dateFrom, $dateTo)
    {
        return StockOut::with(['product', 'branch', 'customer', 'creator'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get()
            ->map(fn($item) => [
                'date' => $item->date->format('Y-m-d'),
                'reference' => $item->stock_out_number,
                'product' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_amount' => $item->total_price,
                'customer' => $item->customer?->name ?? 'N/A',
                'branch' => $item->branch->name,
                'created_by' => $item->creator->username
            ])
            ->toArray();
    }

    private function getTransferReport($branchId, $dateFrom, $dateTo)
    {
        return StockTransfer::with(['inventory.product', 'fromBranch', 'toBranch', 'createdBy'])
            ->when($branchId, function($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                  ->orWhere('to_branch_id', $branchId);
            })
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->get()
            ->map(fn($item) => [
                'date' => $item->date->format('Y-m-d'),
                'product' => $item->inventory->product->name,
                'quantity' => $item->quantity,
                'from_branch' => $item->fromBranch->name,
                'to_branch' => $item->toBranch->name,
                'status' => $item->status,
                'created_by' => $item->createdBy->name,
                'notes' => $item->notes
            ])
            ->toArray();
    }

    private function getReceivingReport($branchId, $dateFrom, $dateTo)
    {
        return ReceivingReport::with(['branch', 'category', 'vendor'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->whereBetween('date_received', [$dateFrom, $dateTo])
            ->get()
            ->map(fn($item) => [
                'date' => $item->date_received->format('Y-m-d'),
                'reference' => $item->receiving_report_number,
                'item_code' => $item->item_code,
                'name' => $item->name,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'category' => $item->category->name,
                'vendor' => $item->vendor?->name ?? 'N/A',
                'branch' => $item->branch->name
            ])
            ->toArray();
    }

    private function getPhssReport($branchId, $dateFrom, $dateTo)
    {
        return ForPhss::with(['product', 'inventory.branch'])
            ->when($branchId, function($q) use ($branchId) {
                $q->whereHas('inventory', fn($query) => 
                    $query->where('branch_id', $branchId)
                );
            })
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get()
            ->map(fn($item) => [
                'date' => $item->created_at->format('Y-m-d'),
                'product' => $item->product->name,
                'quantity' => $item->qty,
                'branch' => $item->inventory->branch->name,
                'status' => $item->status,
                'notes' => $item->note
            ])
            ->toArray();
    }

    public function downloadPdf(Report $report)
    {
        // Check authorization
        if (Auth::user()->branch_id && $report->branch_id !== Auth::user()->branch_id) {
            return back()->with('error', 'You do not have permission to download this report.');
        }

        try {
            $data = [
                'report' => $report->load(['branch', 'generatedBy']),
                'generated_at' => now()->format('Y-m-d H:i:s'),
            ];

            $pdf = PDF::loadView('reports.pdf', $data);
            
            return $pdf->download("report-{$report->report_number}.pdf");
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF. Please try again.');
        }
    }

    public function downloadExcel(Report $report)
    {
        // Check authorization
        if (Auth::user()->branch_id && $report->branch_id !== Auth::user()->branch_id) {
            return back()->with('error', 'You do not have permission to download this report.');
        }

        try {
            return Excel::download(
                new ReportExport($report),
                "report-{$report->report_number}.xlsx"
            );
        } catch (\Exception $e) {
            \Log::error('Excel Generation Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate Excel file. Please try again.');
        }
    }

    /**
     * Display a specific report
     */
    public function show(Report $report)
    {
        // Check if user has access to this report
        $user = Auth::user();
        if ($user->branch_id && $report->branch_id !== $user->branch_id) {
            return back()->with('error', 'You do not have permission to view this report.');
        }

        // Load necessary relationships
        $report->load(['branch', 'generatedBy']);

        return view('reports.show', compact('report'));
    }
}