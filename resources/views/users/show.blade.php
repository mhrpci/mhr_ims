@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">User Profile</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->username }}" class="img-fluid rounded-circle mb-3" style="max-width: 200px;">
                    @else
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 200px; height: 200px; font-size: 72px;">
                            {{ strtoupper(substr($user->username, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <h2 class="h4 mb-3 font-weight-bold">{{ $user->username }}</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-muted">Email:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Roles:</th>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="badge bg-primary">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @if($user->branch)
                                <tr>
                                    <th scope="row" class="text-muted">Branch:</th>
                                    <td>{{ $user->branch->name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th scope="row" class="text-muted">Created at:</th>
                                    <td>{{ $user->created_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Last updated:</th>
                                    <td>{{ $user->updated_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary me-2">
                            <i class="bi bi-pencil me-2"></i>Edit Profile
                        </a>
                        @if($user->canDeleteUsers())
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                            <i class="bi bi-trash me-2"></i>Delete User
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this user? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
