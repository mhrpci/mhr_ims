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
            <h1 class="h3 mb-0 text-gray-800">Users Management</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New User
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            @if(!$user->roles->contains('name', 'Super Admin'))
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->username }}"
                                             class="rounded-circle me-2" style="width: 32px; height: 32px;">
                                        {{ $user->username }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $user->branch->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i> Preview
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    @if($user->canDeleteUsers())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endif
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
    .badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }

    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        object-fit: cover;
    }
</style>
@endpush
