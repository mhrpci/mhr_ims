@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Product Details</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="h4 mb-3 font-weight-bold">{{ $product->name }}</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-muted">Barcode:</th>
                                    <td>{{ $product->barcode }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Description:</th>
                                    <td>{{ $product->description }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Category:</th>
                                    <td>{{ $product->category->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Branch:</th>
                                    <td>{{ $product->branch->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Created at:</th>
                                    <td>{{ $product->created_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Last updated:</th>
                                    <td>{{ $product->updated_at->format('F d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Created by:</th>
                                    <td>{{ $product->creator ? $product->creator->username : 'System' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Updated by:</th>
                                    <td>{{ $product->updater ? $product->updater->username : 'System' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if(Auth::user()->canEditProduct() && Auth::user()->canManageProduct($product))
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-primary me-2">
                            <i class="bi bi-pencil me-2"></i>Edit Product
                        </a>
                        @endif

                        @if(Auth::user()->canDeleteProduct() && Auth::user()->canManageProduct($product))
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal">
                            <i class="bi bi-trash me-2"></i>Delete Product
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Only include the delete modal if user has delete permission -->
@if(Auth::user()->canDeleteProduct() && Auth::user()->canManageProduct($product))
<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
