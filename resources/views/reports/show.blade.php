@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Reports
            </a>
            <h1 class="display-4 mb-0">{{ ucfirst($report->type) }} Report</h1>
            <p class="text-muted">{{ $report->start_date->format('F j, Y') }} - {{ $report->end_date->format('F j, Y') }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-{{ $report->status === 'completed' ? 'success' : 'warning' }} p-2">{{ ucfirst($report->status) }}</span>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Report Details</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Branch:</strong> {{ $report->branch ? $report->branch->name : 'All Branches' }}</li>
                        <li class="mb-2"><strong>Generated on:</strong> {{ now()->format('F j, Y g:i A') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        @if($summary)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-uppercase text-muted mb-3">Summary</h6>
                    <ul class="list-unstyled">
                        @foreach($summary->toArray() as $key => $value)
                        <li class="mb-2"><strong>{{ ucwords(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h4 mb-3">Detailed Report</h2>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            @if($report->type == 'stock_in')
                                <th>Product</th>
                                <th>Vendor</th>
                                <th>Branch</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                                <th>Date</th>
                            @elseif($report->type == 'stock_out')
                                <th>Product</th>
                                <th>Customer</th>
                                <th>Branch</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Price</th>
                                <th>Date</th>
                            @elseif($report->type == 'inventory')
                                <th>Product</th>
                                <th>Branch</th>
                                <th>Quantity</th>
                            @else
                                <th>Product</th>
                                <th>Total Sold</th>
                                <th>Total Revenue</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $item)
                            <tr>
                                @if($report->type == 'stock_in')
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->vendor->name }}</td>
                                    <td>{{ $item->branch->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total_price, 2) }}</td>
                                    <td>{{ $item->date instanceof \DateTime ? $item->date->format('F j, Y') : $item->date }}</td>
                                @elseif($report->type == 'stock_out')
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $item->branch->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ number_format($item->total_price, 2) }}</td>
                                    <td>{{ $item->date instanceof \DateTime ? $item->date->format('F j, Y') : $item->date }}</td>
                                @elseif($report->type == 'inventory')
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->branch->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                @else
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->total_sold ?? 0 }}</td>
                                    <td>{{ number_format($item->total_revenue ?? 0, 2) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($report->status === 'completed')
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('reports.download', $report) }}" class="btn btn-outline-primary">
            <i class="fas fa-download me-1"></i> Download CSV
        </a>
        <a href="{{ route('reports.pdf', $report) }}" class="btn btn-primary">
            <i class="fas fa-file-pdf me-1"></i> Generate PDF
        </a>
    </div>
    @endif
</div>
@endsection
