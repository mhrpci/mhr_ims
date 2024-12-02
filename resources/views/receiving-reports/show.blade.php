@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Receiving Report</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('receiving-reports.index') }}" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <!-- Card Header -->
        <div class="card-header py-3 bg-light">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-text me-2 fs-4"></i>
                        <h5 class="mb-0 fw-bold">Report #{{ $receivingReport->receiving_report_number }}</h5>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column">
                        <span class="text-muted mb-1">
                            <i class="bi bi-building me-2"></i>Branch: {{ $receivingReport->branch->name }}
                        </span>
                        @if($receivingReport->vendor)
                            <span class="text-muted">
                                <i class="bi bi-shop me-2"></i>Vendor: {{ $receivingReport->vendor->name }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th class="align-middle">Item Code</th>
                            <th class="align-middle">Date</th>
                            <th class="align-middle">Name</th>
                            <th class="align-middle">Barcode</th>
                            <th class="align-middle">Category</th>
                            <th class="align-middle">Vendor</th>
                            <th class="align-middle text-end">Quantity</th>
                            <th class="align-middle">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalQuantity = 0; @endphp
                        @foreach($groupedReports as $report)
                            @php $totalQuantity += $report->quantity; @endphp
                            <tr>
                                <td class="align-middle">{{ $report->item_code }}</td>
                                <td class="align-middle">{{ $report->date_received->format('M d, Y') }}</td>
                                <td class="align-middle">{{ $report->name }}</td>
                                <td class="align-middle">{{ $report->barcode }}</td>
                                <td class="align-middle">{{ $report->category->name ?? '—' }}</td>
                                <td class="align-middle">{{ $report->vendor->name ?? '—' }}</td>
                                <td class="align-middle text-end">{{ number_format($report->quantity) }}</td>
                                <td class="align-middle">{{ $report->unit }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-secondary fw-bold">
                            <td colspan="6" class="text-end">Total Quantity:</td>
                            <td class="text-end">{{ number_format($totalQuantity) }}</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 