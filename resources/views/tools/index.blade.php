@extends('layouts.app')

@section('title', 'Tools')

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
            <h1 class="h3 mb-0 text-gray-800">Tools Management</h1>
        </div>
        <div class="col-auto">
            @if(Auth::user()->canCreateTool())
            <a href="{{ route('tools.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Tool
            </a>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tool Name</th>
                            <th>Barcode</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tools as $tool)
                        <tr>
                            <td>{{ $tool->id }}</td>
                            <td>{{ $tool->tool_name }}</td>
                            <td>{{ $tool->barcode }}</td>
                            <td>{{ $tool->branch->name ?? 'Not Assigned' }}</td>
                            <td>
                                @if(Auth::user()->canManageTool($tool))
                                    <a href="{{ route('tools.show', $tool) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i> Preview
                                    </a>
                                    @if(Auth::user()->canEditTool())
                                    <a href="{{ route('tools.edit', $tool) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    @endif
                                    @if(Auth::user()->canDeleteTool())
                                    <form action="{{ route('tools.destroy', $tool) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete"
                                            onclick="return confirm('Are you sure you want to delete this tool?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                @endif
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
