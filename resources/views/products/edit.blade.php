@extends('layouts.app')

@section('content')
@if(!Auth::user()->canEditProduct() || !Auth::user()->canManageProduct($product))
    <div class="container-fluid">
        <div class="alert alert-danger">
            You don't have permission to edit this product.
        </div>
    </div>
@else
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Edit Product: {{ $product->name }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="barcode" class="form-label">Barcode <span class="text-danger">*</span></label>
                        <input type="text" name="barcode" id="barcode" class="form-control @error('barcode') is-invalid @enderror" value="{{ old('barcode', $product->barcode) }}" required>
                        @error('barcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" id="category_id" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                    <select class="form-select @error('branch_id') is-invalid @enderror" name="branch_id" id="branch_id" required>
                        <option value="">Select Branch</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $product->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection