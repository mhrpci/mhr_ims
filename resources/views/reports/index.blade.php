@extends('layouts.app')

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
            <h1 class="h3 mb-0 text-gray-800">Reports Management</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('reports.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Generate New Report
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Date Range</th>
                            <th>Branch</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td>{{ $report->id }}</td>
                            <td>{{ ucfirst($report->type) }}</td>
                            <td>
                                {{ $report->start_date->format('F j, Y') }} to {{ $report->end_date->format('F j, Y') }}
                            </td>
                            <td>{{ $report->branch ? $report->branch->name : 'All Branches' }}</td>
                            <td>{{ ucfirst($report->status) }}</td>
                            <td>
                                <a href="{{ route('reports.show', $report) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i> Preview
                                </a>
                                @if($report->status === 'completed')
                                <a href="{{ route('reports.download', $report) }}" class="btn btn-sm btn-success" title="Download">
                                    <i class="bi bi-download"></i> Download
                                </a>
                                <a href="{{ route('reports.pdf', $report) }}" class="btn btn-sm btn-warning" title="Generate PDF">
                                    <i class="bi bi-file-pdf"></i> PDF
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $reports->links() }}
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
