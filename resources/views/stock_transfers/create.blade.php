@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Create New Stock Transfer</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock_transfers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Stock Transfers
            </a>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(auth()->user()->hasRole('Stock Manager'))
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        Note: As a Stock Manager, your transfer request will need approval from your Branch Manager.
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('stock_transfers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="inventory_id" class="form-label">Product Inventory <span class="text-danger">*</span></label>
                        <select class="form-select @error('inventory_id') is-invalid @enderror" name="inventory_id" id="inventory_id" required>
                            <option value="">Select Product Inventory</option>
                            @foreach($inventories as $inventory)
                                <option value="{{ $inventory->id }}" {{ old('inventory_id') == $inventory->id ? 'selected' : '' }}>
                                    {{ $inventory->product->name }} (Available: {{ $inventory->quantity }})
                                </option>
                            @endforeach
                        </select>
                        @error('inventory_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="from_branch_id" class="form-label">From Branch <span class="text-danger">*</span></label>
                        @if($fromBranch)
                            <input type="text" class="form-control" value="{{ $fromBranch->name }}" readonly>
                            <input type="hidden" name="from_branch_id" value="{{ $fromBranch->id }}">
                        @else
                            <select class="form-select @error('from_branch_id') is-invalid @enderror" name="from_branch_id" id="from_branch_id" required>
                                <option value="">Select Source Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('from_branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('from_branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="to_branch_id" class="form-label">To Branch <span class="text-danger">*</span></label>
                        <select class="form-select @error('to_branch_id') is-invalid @enderror" name="to_branch_id" id="to_branch_id" required>
                            <option value="">Select Destination Branch</option>
                            @foreach($toBranches as $branch)
                                <option value="{{ $branch->id }}" {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('to_branch_id')
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
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Transfer
                    </button>
                    <button type="submit" name="action" value="save_and_new" class="btn btn-secondary">
                        <i class="bi bi-plus-square me-2"></i>Save and Create New
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle me-2"></i>
        @if(auth()->user()->hasRole(['Admin', 'Super Admin', 'Branch Manager']))
            Note: Your stock transfers will be processed immediately without requiring additional approval.
        @else
            Note: Stock transfers require approval from a Branch Manager or Admin before they are processed.
        @endif
    </div>
</div>
@endsection
