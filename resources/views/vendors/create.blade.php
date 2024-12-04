@extends('layouts.app')

@section('title', 'Create Vendor')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Create New Supplier</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Suppliers
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
            <form action="{{ route('vendors.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Supplier
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
