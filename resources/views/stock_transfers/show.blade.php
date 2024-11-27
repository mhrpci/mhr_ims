@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Stock Transfer Details</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock_transfers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Stock Transfers
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="h4 mb-3 font-weight-bold">Transfer #{{ $stockTransfer->id }}</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-muted">Product:</th>
                                    <td>{{ $stockTransfer->inventory->product->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">From Branch:</th>
                                    <td>{{ $stockTransfer->fromBranch->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">To Branch:</th>
                                    <td>{{ $stockTransfer->toBranch->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Quantity:</th>
                                    <td>{{ $stockTransfer->quantity }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Date:</th>
                                    <td>{{ $stockTransfer->date instanceof \Carbon\Carbon ? $stockTransfer->date->format('F d, Y') : $stockTransfer->date }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Notes:</th>
                                    <td>{{ $stockTransfer->notes ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Created by:</th>
                                    <td>{{ $stockTransfer->createdBy->username ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Updated by:</th>
                                    <td>{{ $stockTransfer->updatedBy->username ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Status:</th>
                                    <td>
                                        @if($stockTransfer->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($stockTransfer->status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($stockTransfer->status === 'approved')
                                <tr>
                                    <th scope="row" class="text-muted">Approved by:</th>
                                    <td>
                                        {{ $stockTransfer->approvedBy->username ?? 'N/A' }}
                                        {{ $stockTransfer->approved_at ? ' on ' . $stockTransfer->approved_at->format('F d, Y H:i:s') : '' }}
                                    </td>
                                </tr>
                                @elseif($stockTransfer->status === 'rejected')
                                <tr>
                                    <th scope="row" class="text-muted">Rejected by:</th>
                                    <td>
                                        {{ $stockTransfer->approvedBy->username ?? 'N/A' }}
                                        {{ $stockTransfer->approved_at ? ' on ' . $stockTransfer->approved_at->format('F d, Y H:i:s') : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Rejection Reason:</th>
                                    <td>{{ $stockTransfer->rejection_reason ?? 'N/A' }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stockTransfer->status === 'pending' && (auth()->user()->hasRole(['Admin', 'Super Admin']) ||
        (auth()->user()->hasRole('Branch Manager') && auth()->user()->branch_id === $stockTransfer->from_branch_id)))
        <div class="card shadow mb-4">
            <div class="card-body">
                <h3 class="h5 mb-3">Actions</h3>
                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                    <i class="bi bi-check-circle me-2"></i>Approve Transfer
                </button>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle me-2"></i>Reject Transfer
                </button>
            </div>
        </div>

        <!-- Approve Modal -->
        <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Approve Stock Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to approve this stock transfer?</p>
                        <div class="alert alert-info">
                            <strong>Product:</strong> {{ $stockTransfer->inventory->product->name }}<br>
                            <strong>Quantity:</strong> {{ $stockTransfer->quantity }}<br>
                            <strong>From:</strong> {{ $stockTransfer->fromBranch->name }}<br>
                            <strong>To:</strong> {{ $stockTransfer->toBranch->name }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('stock_transfers.approve', $stockTransfer) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Approve Transfer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject Stock Transfer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('stock_transfers.reject', $stockTransfer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <strong>Product:</strong> {{ $stockTransfer->inventory->product->name }}<br>
                                <strong>Quantity:</strong> {{ $stockTransfer->quantity }}<br>
                                <strong>From:</strong> {{ $stockTransfer->fromBranch->name }}<br>
                                <strong>To:</strong> {{ $stockTransfer->toBranch->name }}
                            </div>
                            <div class="mb-3">
                                <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Transfer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
