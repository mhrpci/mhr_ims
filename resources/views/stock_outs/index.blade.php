@extends('layouts.app')

@section('title', 'Stock Out')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Stock Out Management</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock_outs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Stock Out
            </a>
        </div>
    </div>

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

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Stock Out #</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Branch</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockOuts as $stockOut)
                        <tr>
                            <td>{{ $stockOut->stock_out_number }}</td>
                            <td>{{ $stockOut->product->name }}</td>
                            <td>{{ $stockOut->customer->name ?? 'N/A' }}</td>
                            <td>{{ $stockOut->branch->name }}</td>
                            <td>{{ $stockOut->quantity }} {{ $stockOut->unit }}</td>
                            <td>{{ $stockOut->unit }}</td>
                            <td>{{ $stockOut->date instanceof \DateTime ? $stockOut->date->format('Y-m-d') : $stockOut->date }}</td>
                            <td>
                                <a href="{{ route('stock_outs.show', $stockOut) }}" class="btn btn-sm btn-info" title="Preview">
                                    <i class="bi bi-eye"></i> Preview
                                </a>
                            </td>
                        </tr>
                        @endforeach
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
        if ($.fn.DataTable.isDataTable('.datatable')) {
            $('.datatable').DataTable().destroy();
        }

        // Initialize DataTable with specific date sorting
        var table = $('.datatable').DataTable({
            columnDefs: [
                {
                    targets: 6, // Date column index (adjust this based on your table structure)
                    type: 'date'
                }
            ],
            order: [[6, 'desc']], // Sort by date column descending by default
        });

        // Remove any existing search function before adding new one
        $.fn.dataTable.ext.search.pop();

        // Custom filtering function for date range
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var startDate = $('#startDateFilter').val();
                var endDate = $('#endDateFilter').val();
                var date = moment(data[6], 'YYYY-MM-DD').format('YYYY-MM-DD'); // Convert displayed date to YYYY-MM-DD

                if (startDate === '' && endDate === '') return true;
                if (startDate === '' && date <= endDate) return true;
                if (endDate === '' && date >= startDate) return true;
                if (date >= startDate && date <= endDate) return true;
                
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
