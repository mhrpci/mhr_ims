@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Stock In Details</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock_ins.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Stock In
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="h4 mb-3 font-weight-bold">Stock In #{{ $stockIn->id }}</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-muted">Product:</th>
                                    <td>{{ $stockIn->product->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Vendor:</th>
                                    <td>{{ $stockIn->vendor->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Branch:</th>
                                    <td>{{ $stockIn->branch->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Quantity:</th>
                                    <td>{{ $stockIn->quantity }} {{ $stockIn->unit }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Lot Number:</th>
                                    <td>{{ $stockIn->lot_number }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Expiration Date:</th>
                                    <td>{{ $stockIn->expiration_date ? $stockIn->expiration_date->format('F d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Note:</th>
                                    <td>{{ $stockIn->note ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Date:</th>
                                    <td>{{ $stockIn->date instanceof \Carbon\Carbon ? $stockIn->date->format('F d, Y') : $stockIn->date }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Created by:</th>
                                    <td>{{ $stockIn->creator ? $stockIn->creator->username : 'System' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Updated by:</th>
                                    <td>{{ $stockIn->updater ? $stockIn->updater->username : 'System' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
