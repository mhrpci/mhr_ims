@extends('layouts.app')

@section('title', 'View Vendor')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">Vendor Details</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Vendors
                </a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="h4 mb-3 font-weight-bold">{{ $vendor->name }}</h2>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="text-muted">Email:</th>
                                        <td>{{ $vendor->email }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Phone:</th>
                                        <td>{{ $vendor->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Address:</th>
                                        <td>{{ $vendor->address }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Created at:</th>
                                        <td>{{ $vendor->created_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Last updated:</th>
                                        <td>{{ $vendor->updated_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-primary me-2">
                                <i class="bi bi-pencil me-2"></i>Edit Vendor
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteVendorModal">
                                <i class="bi bi-trash me-2"></i>Delete Vendor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
