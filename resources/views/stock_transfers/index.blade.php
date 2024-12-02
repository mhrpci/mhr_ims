@extends('layouts.app')

@section('title', 'Stock Transfers')

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
            <h1 class="h3 mb-0 text-gray-800">Stock Transfer Management</h1>
        </div>
        <div class="col-auto">
            @if(auth()->user()->canAccessStockTransfers())
            <a href="{{ route('stock_transfers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Transfer
            </a>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>From Branch</th>
                            <th>To Branch</th>
                            <th>Quantity</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockTransfers as $transfer)
                        <tr>
                            <td>{{ $transfer->id }}</td>
                            <td>{{ $transfer->inventory->product->name }}</td>
                            <td>{{ $transfer->fromBranch->name }}</td>
                            <td>{{ $transfer->toBranch->name }}</td>
                            <td>{{ $transfer->quantity }}</td>
                            <td>{{ $transfer->date instanceof \DateTime ? $transfer->date->format('Y-m-d') : $transfer->date }}</td>
                            <td>
                                @if($transfer->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($transfer->status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>{{ $transfer->createdBy->username ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('stock_transfers.show', $transfer) }}" class="btn btn-sm btn-info" title="Preview">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($transfer->status === 'pending' && (auth()->user()->hasRole(['Admin', 'Super Admin']) ||
                                    (auth()->user()->hasRole('Branch Manager') && auth()->user()->branch_id === $transfer->from_branch_id)))
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $transfer->id }}" title="Approve">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $transfer->id }}" title="Reject">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Approval/Rejection Modals -->
@foreach($stockTransfers as $transfer)
    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal{{ $transfer->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Approve Stock Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve this stock transfer?</p>
                    <div class="alert alert-info">
                        <strong>Product:</strong> {{ $transfer->inventory->product->name }}<br>
                        <strong>Quantity:</strong> {{ $transfer->quantity }}<br>
                        <strong>From:</strong> {{ $transfer->fromBranch->name }}<br>
                        <strong>To:</strong> {{ $transfer->toBranch->name }}
                    </div>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('stock_transfers.approve', $transfer) }}" method="POST">
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
    <div class="modal fade" id="rejectModal{{ $transfer->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Stock Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('stock_transfers.reject', $transfer) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <strong>Product:</strong> {{ $transfer->inventory->product->name }}<br>
                            <strong>Quantity:</strong> {{ $transfer->quantity }}<br>
                            <strong>From:</strong> {{ $transfer->fromBranch->name }}<br>
                            <strong>To:</strong> {{ $transfer->toBranch->name }}
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
@endforeach

@endsection
