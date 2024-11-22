@extends('layouts.app')

@section('content')
@if(!Auth::user()->canCreateTool())
    @php abort(403, 'Unauthorized action.') @endphp
@endif

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Create New Tool</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('tools.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Tools
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('tools.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="tool_name" class="form-label">Tool Name <span class="text-danger">*</span></label>
                    <input type="text" name="tool_name" id="tool_name" class="form-control @error('tool_name') is-invalid @enderror"
                        value="{{ old('tool_name') }}" required autofocus>
                    @error('tool_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="barcode" class="form-label">Barcode <span class="text-danger">*</span></label>
                    <input type="text" name="barcode" id="barcode" class="form-control @error('barcode') is-invalid @enderror"
                        value="{{ old('barcode') }}" required>
                    @error('barcode')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                    <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                        <option value="">Select a branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Tool
                    </button>
                    <button type="submit" name="action" value="save_and_new" class="btn btn-secondary">
                        <i class="bi bi-plus-square me-2"></i>Save and Create New
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
