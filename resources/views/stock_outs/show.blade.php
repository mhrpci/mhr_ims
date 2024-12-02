@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Stock Out Details</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock_outs.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Stock Out
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="h4 mb-3 font-weight-bold">Stock Out #{{ $stockOut->id }}</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-muted">Stock Out Number:</th>
                                    <td>{{ $stockOut->stock_out_number }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Product:</th>
                                    <td>{{ $stockOut->product->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Customer:</th>
                                    <td>{{ $stockOut->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Branch:</th>
                                    <td>{{ $stockOut->branch->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Quantity:</th>
                                    <td>{{ $stockOut->quantity }} {{ $stockOut->unit }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Note:</th>
                                    <td>{{ $stockOut->note ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Date:</th>
                                    <td>{{ $stockOut->date instanceof \DateTime ? $stockOut->date->format('F d, Y') : $stockOut->date }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Created by:</th>
                                    <td>{{ $stockOut->creator ? $stockOut->creator->username : 'System' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Updated by:</th>
                                    <td>{{ $stockOut->updater ? $stockOut->updater->username : 'System' }}</td>
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
