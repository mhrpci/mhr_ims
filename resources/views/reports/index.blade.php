@extends('layouts.app')

@section('styles')
<style>
    .dataTables_wrapper .dataTables_length select {
        width: 60px;
        display: inline-block;
    }
    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 4px 8px;
    }
    .dataTables_wrapper .dataTables_info {
        padding-top: 0.85em;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 0.5em;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.3em 0.8em;
        margin-left: 2px;
        border-radius: 4px;
    }
    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #344767;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .badge {
        font-size: 85%;
        padding: 0.35em 0.65em;
    }
    .table td {
        vertical-align: middle;
    }
    .action-buttons {
        white-space: nowrap;
    }
    .status-badge {
        min-width: 80px;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reports</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <!-- Report Generation Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Generate New Report</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('reports.generate') }}" method="POST" id="reportForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="report_type" class="form-label">Report Type</label>
                                <select name="report_type" id="report_type" class="form-select" required>
                                    <option value="">Select Report Type</option>
                                    <option value="inventory">Inventory Status Report</option>
                                    <option value="stock_in">Stock In Report</option>
                                    <option value="stock_out">Stock Out Report</option>
                                    <option value="stock_transfer">Stock Transfer Report</option>
                                    <option value="receiving">Receiving Report</option>
                                    <option value="phss">Borrow Product Report</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="scope" class="form-label">Scope</label>
                                <select name="scope" id="scope" class="form-select" required>
                                    @if(!Auth::user()->branch_id)
                                        <option value="all">All Branches</option>
                                    @endif
                                    <option value="specific">Specific Branch</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="branch_id" class="form-label">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-select" {{ Auth::user()->branch_id ? 'disabled' : '' }}>
                                    @if(!Auth::user()->branch_id)
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="{{ Auth::user()->branch_id }}">{{ Auth::user()->branch->name }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" required>
                            </div>

                            <div class="col-md-4">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" required>
                            </div>

                            <div class="col-md-4">
                                <label for="format" class="form-label">Format</label>
                                <select name="format" id="format" class="form-select" required>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-file-earmark-text me-2"></i>Generate Report
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Previous Reports Table -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Previous Reports</h6>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="reportsTable" width="100%">
                            <thead>
                                <tr>
                                    <th>Report Number</th>
                                    <th>Type</th>
                                    <th>Branch</th>
                                    <th>Date Range</th>
                                    <th>Records</th>
                                    <th>Total Amount</th>
                                    <th>Generated By</th>
                                    <th width="120px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ $report->report_number }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $report->formatted_type }}</span>
                                        </td>
                                        <td>{{ $report->branch?->name ?? 'All Branches' }}</td>
                                        <td>{{ $report->date_range }}</td>
                                        <td class="text-end">{{ number_format($report->total_records) }}</td>
                                        <td class="text-end">{{ number_format($report->total_amount, 2) }}</td>
                                        <td>{{ $report->generatedBy->name }}</td>
                                        <td class="action-buttons text-center">
                                            <div class="btn-group">
                                                <a href="{{ route('reports.show', $report) }}" 
                                                   class="btn btn-info btn-sm" 
                                                   data-bs-toggle="tooltip" 
                                                   title="View Report">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No reports generated yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const table = $('#reportsTable').DataTable({
        pageLength: 10,
        ordering: true,
        responsive: true,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search reports...",
            lengthMenu: "_MENU_ reports per page",
            info: "Showing _START_ to _END_ of _TOTAL_ reports",
            infoEmpty: "No reports available",
            infoFiltered: "(filtered from _MAX_ total reports)",
            zeroRecords: "No matching reports found",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                previous: '<i class="fas fa-angle-left"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                last: '<i class="fas fa-angle-double-right"></i>'
            }
        },
        columnDefs: [
            { 
                targets: [4, 5], // Total Records and Amount columns
                className: 'text-end'
            },
            {
                targets: [7], // Actions column
                orderable: false,
                className: 'text-center'
            }
        ],
        order: [[0, 'desc']], // Sort by Report Number descending
        drawCallback: function(settings) {
            // Initialize tooltips after table draw
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Add custom search input styling
    $('.dataTables_filter input').addClass('form-control form-control-sm');
    $('.dataTables_length select').addClass('form-select form-select-sm');

    // Add export buttons if needed
    new $.fn.dataTable.Buttons(table, {
        buttons: [
            {
                extend: 'collection',
                text: '<i class="fas fa-download"></i> Export',
                className: 'btn btn-secondary btn-sm',
                buttons: [
                    'copy',
                    'excel',
                    'pdf'
                ]
            }
        ]
    });

    table.buttons().container()
        .appendTo('#reportsTable_wrapper .col-md-6:eq(0)');
});
</script>
@endpush