@extends('layouts.app')

@section('title', 'Suppliers')

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
            <h1 class="h3 mb-0 text-gray-800">Suppliers Management</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('vendors.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Supplier
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendors as $vendor)
                            <tr>
                                <td>{{ $vendor->id }}</td>
                                <td>{{ $vendor->name }}</td>
                                <td>{{ $vendor->email }}</td>
                                <td>{{ $vendor->phone }}</td>
                                <td>
                                    <a href="{{ route('vendors.show', $vendor) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i> Preview
                                    </a>
                                    <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this vendor?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
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
