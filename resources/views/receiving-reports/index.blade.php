@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Receiving Reports</h1>
        </div>
        <div class="col-auto">
            @if(Auth::user()->canAccessCreateReceivingReports())
            <a href="{{ route('receiving-reports.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create Report
            </a>
            @endif
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label for="startDateFilter" class="form-label">Start Date</label>
            <input type="date" id="startDateFilter" class="form-control" placeholder="Start Date" autocomplete="off">
        </div>
        <div class="col-md-3">
            <label for="endDateFilter" class="form-label">End Date</label>
            <input type="date" id="endDateFilter" class="form-control" placeholder="End Date" autocomplete="off">
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable" id="receivingReportsTable">
                    <thead>
                        <tr>
                            <th>Report Number</th>
                            <th>Date Received</th>
                            <th>Branch</th>
                            <th>Total Quantity</th>
                            <th>Entries</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receivingReports as $report)
                            <tr>
                                <td>{{ $report->receiving_report_number }}</td>
                                <td>{{ $report->date_received->format('M d, Y') }}</td>
                                <td>{{ $report->branch->name }}</td>
                                <td>{{ number_format($report->total_quantity) }}</td>
                                <td>{{ $report->entries_count }}</td>
                                <td>
                                    <a href="{{ route('receiving-reports.show', $report->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i> Preview
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No receiving reports found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .alert {
        border-left: 4px solid #28a745;
    }
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
    $(document).ready(function() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#receivingReportsTable')) {
            $('#receivingReportsTable').DataTable().destroy();
        }

        // Initialize DataTable with specific date sorting
        var table = $('#receivingReportsTable').DataTable({
            columnDefs: [
                {
                    targets: 1, // Date column index
                    type: 'date'
                }
            ],
            order: [[1, 'desc']], // Sort by date column descending by default
        });

        // Remove any existing search function before adding new one
        $.fn.dataTable.ext.search.pop();

        // Custom filtering function for date range
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var startDate = $('#startDateFilter').val();
                var endDate = $('#endDateFilter').val();
                var dateReceived = moment(data[1], 'MMM DD, YYYY').format('YYYY-MM-DD'); // Convert displayed date to YYYY-MM-DD

                if (startDate === '' && endDate === '') return true;
                if (startDate === '' && dateReceived <= endDate) return true;
                if (endDate === '' && dateReceived >= startDate) return true;
                if (dateReceived >= startDate && dateReceived <= endDate) return true;
                
                return false;
            }
        );

        // Event listener for date inputs
        $('#startDateFilter, #endDateFilter').on('change', function() {
            table.draw();
        });
    });
</script>
@endpush 