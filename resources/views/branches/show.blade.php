@extends('layouts.app')

@section('title', 'View Branch')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h3 mb-0 text-gray-800">Branch Details</h1>
            </div>
            <div class="col-auto">
                <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Branches
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="h4 mb-3 font-weight-bold">{{ $branch->name }}</h2>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="text-muted">Address:</th>
                                        <td>{{ $branch->address }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Phone:</th>
                                        <td>{{ $branch->phone }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Created at:</th>
                                        <td>{{ $branch->created_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="text-muted">Last updated:</th>
                                        <td>{{ $branch->updated_at->format('F d, Y H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('branches.edit', $branch) }}" class="btn btn-primary me-2">
                                <i class="bi bi-pencil me-2"></i>Edit Branch
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBranchModal">
                                <i class="bi bi-trash me-2"></i>Delete Branch
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Branch Modal -->
    <div class="modal fade" id="deleteBranchModal" tabindex="-1" aria-labelledby="deleteBranchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteBranchModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this branch?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this branch?')">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
