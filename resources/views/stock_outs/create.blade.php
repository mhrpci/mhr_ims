@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Create New Stock Out</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock_outs.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Stock Out
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('stock_outs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="stock_out_number" class="form-label">Stock Out Number / SI#</label>
                        <input type="text" name="stock_out_number" id="stock_out_number" class="form-control @error('stock_out_number') is-invalid @enderror" value="{{ old('stock_out_number') }}">
                        @error('stock_out_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select class="form-select @error('product_id') is-invalid @enderror" name="product_id" id="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="customer_id" class="form-label">Customer </label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" name="customer_id" id="customer_id">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                        <select class="form-select @error('branch_id') is-invalid @enderror" name="branch_id" id="branch_id" required>
                            <option value="">Select Branch</option>
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
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity') }}" required min="1">
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="unit" class="form-label">Unit <span class="text-danger">*</span></label>
                        <input type="text" name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit') }}" required>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="unit_price" class="form-label">Unit Price</label>
                        <input type="number" step="0.01" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror" value="{{ old('unit_price') }}">
                        @error('unit_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="attachments" class="form-label">Attachments</label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control @error('attachments.*') is-invalid @enderror" multiple>
                        <small class="text-muted">
                            Accepted files: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max: 10MB each)
                        </small>
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Stock Out
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
