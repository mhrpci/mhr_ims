<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index()
    {
        $query = Report::with('branch', 'generatedBy');

        // If user has branch_id, only show reports for their branch
        if (auth()->user()->branch_id) {
            $query->where('branch_id', auth()->user()->branch_id);
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function create()
    {
        // If user has branch_id, only show their branch
        if (auth()->user()->branch_id) {
            $branches = Branch::where('id', auth()->user()->branch_id)->get();
        } else {
            $branches = Branch::all();
        }

        return view('reports.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:stock_in,stock_out,inventory,product_performance',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ];

        // If user has branch_id, force their branch_id
        if (auth()->user()->branch_id) {
            $rules['branch_id'] = 'prohibited'; // Prevent manual branch selection
        } else {
            $rules['branch_id'] = 'nullable|exists:branches,id';
        }

        $validatedData = $request->validate($rules);

        // Set branch_id based on user's branch
        $branchId = auth()->user()->branch_id ?? $request->input('branch_id');

        $report = Report::create([
            'type' => $validatedData['type'],
            'start_date' => $validatedData['start_date'],
            'end_date' => $validatedData['end_date'],
            'branch_id' => $branchId,
            'generated_by' => auth()->id(),
            'status' => 'pending',
        ]);

        GenerateReportJob::dispatch($report);

        return redirect()->route('reports.index')
            ->with('success', 'Report generation has been queued. You will be notified when it\'s ready.');
    }

    public function show(Report $report)
    {
        // Ensure user can only view reports for their branch
        if (auth()->user()->branch_id && $report->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to this report.');
        }

        $reportData = $report->getReportData();
        $summary = $report->getSummary();
        return view('reports.show', compact('report', 'reportData', 'summary'));
    }

    public function download(Report $report)
    {
        // Ensure user can only download reports for their branch
        if (auth()->user()->branch_id && $report->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to this report.');
        }

        if ($report->status !== 'completed') {
            return back()->with('error', 'Report is not ready for download.');
        }

        return Storage::download($report->file_path);
    }

    public function generatePdf(Report $report)
    {
        // Ensure user can only generate PDFs for their branch
        if (auth()->user()->branch_id && $report->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized access to this report.');
        }

        if ($report->status !== 'completed') {
            return back()->with('error', 'Report is not ready for PDF generation.');
        }

        try {
            $reportData = $report->getReportData();
            $summary = $report->getSummary();

            // Convert StockIn model to array if necessary
            if ($reportData instanceof \App\Models\StockIn) {
                $reportData = $reportData->toArray();
            }

            if ($summary instanceof \App\Models\StockIn) {
                $summary = $summary->toArray();
            }

            // Ensure $reportData and $summary are arrays
            $reportData = is_array($reportData) ? $reportData : [];
            $summary = is_array($summary) ? $summary : [];

            // Debug information
            Log::info('Generating PDF for report:', [
                'report_id' => $report->id,
                'type' => $report->type,
                'data_type' => gettype($reportData),
                'data_count' => is_array($reportData) ? count($reportData) : 'N/A',
                'summary_type' => gettype($summary),
                'summary_count' => is_array($summary) ? count($summary) : 'N/A'
            ]);

            $pdf = Pdf::loadView('reports.pdf', compact('report', 'reportData', 'summary'));

            $fileName = sprintf('%s_report_%s_%s.pdf',
                $report->type,
                $report->id,
                $report->end_date->format('Y-m-d')
            );

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage(), [
                'report_id' => $report->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while generating the PDF. Please check the logs for more information.');
        }
    }
}
