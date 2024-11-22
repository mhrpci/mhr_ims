@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Inventory Details</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Inventories
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="h4 mb-3 font-weight-bold">Inventory #{{ $inventory->id }}</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-muted">Product:</th>
                                    <td>{{ $inventory->product->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Branch:</th>
                                    <td>{{ $inventory->branch->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Quantity:</th>
                                    <td>{{ $inventory->quantity }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Created at:</th>
                                    <td>{{ $inventory->created_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Last updated:</th>
                                    <td>{{ $inventory->updated_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if(auth()->user()->canEditInventory())
                        <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-primary me-2">
                            <i class="bi bi-pencil me-2"></i>Edit Inventory
                        </a>
                        @endif
                        @if(auth()->user()->canDeleteInventory())
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteInventoryModal">
                            <i class="bi bi-trash me-2"></i>Delete Inventory
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Inventory Modal -->
<div class="modal fade" id="deleteInventoryModal" tabindex="-1" aria-labelledby="deleteInventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteInventoryModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this inventory record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('inventories.destroy', $inventory) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
