@extends('layouts.app')

@section('title', 'Stock In')

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
            <h1 class="h3 mb-0 text-gray-800">Stock In Management</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock_ins.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Stock In
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
                            <th>Product</th>
                            <th>Vendor</th>
                            <th>Branch</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Price</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockIns as $stockIn)
                        <tr>
                            <td>{{ $stockIn->id }}</td>
                            <td>{{ $stockIn->product->name }}</td>
                            <td>{{ $stockIn->vendor->name ?? 'N/A' }}</td>
                            <td>{{ $stockIn->branch->name }}</td>
                            <td>{{ $stockIn->quantity }}</td>
                            <td>{{ number_format($stockIn->unit_price, 2) }}</td>
                            <td>{{ number_format($stockIn->total_price, 2) }}</td>
                            <td>{{ $stockIn->date instanceof \DateTime ? $stockIn->date->format('Y-m-d') : $stockIn->date }}</td>
                            <td>
                                <a href="{{ route('stock_ins.show', $stockIn) }}" class="btn btn-sm btn-info" title="Preview">
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
