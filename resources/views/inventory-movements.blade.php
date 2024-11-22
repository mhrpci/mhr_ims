@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Recent Inventory Movements</h2>
        <a href="{{ route('home') }}" class="btn btn-secondary">
            <i class="bi bi-house-door"></i> Back to Home
        </a>
    </div>
    <div class="table-responsive">
        <table id="inventory-movements-table" class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Branch Details</th>
                    <th>Status</th>
                    <th>Created By</th>
                    <th>Updated By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventoryMovements as $movement)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($movement->date)->format('M d, Y') }}</td>
                        <td>
                            <strong>{{ $movement->product->name ?? 'N/A' }}</strong>
                            @if($movement->product->code ?? false)
                                <br>
                                <small class="text-muted">Code: {{ $movement->product->code }}</small>
                            @endif
                        </td>
                        <td>
                            @if($movement->action === 'Transfer')
                                <span class="badge bg-info">Stock Transfer</span>
                            @else
                                <span class="badge bg-{{ $movement->action == 'Stock In' ? 'success' : 'danger' }}">
                                    {{ $movement->action }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ number_format($movement->quantity) }}</strong>
                            @if($movement->product->unit ?? false)
                                {{ $movement->product->unit }}
                            @endif
                        </td>
                        <td>
                            @if($movement->action === 'Transfer')
                                <div class="d-flex align-items-center">
                                    <div class="text-end me-2">
                                        <strong>From:</strong> {{ $movement->fromBranch->name ?? 'N/A' }}
                                        <br>
                                        <strong>To:</strong> {{ $movement->toBranch->name ?? 'N/A' }}
                                    </div>
                                    <i class="bi bi-arrow-right text-info"></i>
                                </div>
                                @if(Auth::user()->isBranchRestricted())
                                    <small class="text-muted">
                                        ({{ $movement->from_branch_id == Auth::user()->branch_id ? 'Outgoing' : 'Incoming' }} Transfer)
                                    </small>
                                @endif
                            @else
                                {{ $movement->branch->name ?? 'N/A' }}
                            @endif
                        </td>
                        <td>
                            @if($movement->action === 'Transfer')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-success">Completed</span>
                            @endif
                        </td>
                        <td>
                            {{ $movement->creator->username ?? 'N/A' }}
                            <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($movement->created_at)->format('M d, Y H:i') }}
                            </small>
                        </td>
                        <td>
                            {{ $movement->updater->username ?? 'N/A' }}
                            <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($movement->updated_at)->format('M d, Y H:i') }}
                            </small>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<style>
    .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.875rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#inventory-movements-table').DataTable({
            "order": [[ 0, "desc" ]],
            "pageLength": 25,
            "columnDefs": [
                {
                    "targets": 0,
                    "type": "date-eu"
                }
            ],
            "language": {
                "lengthMenu": "Show _MENU_ movements per page",
                "zeroRecords": "No movements found",
                "info": "Showing page _PAGE_ of _PAGES_",
                "infoEmpty": "No movements available",
                "infoFiltered": "(filtered from _MAX_ total movements)",
                "search": "Search movements:"
            },
            "responsive": true,
            "autoWidth": false
        });
    });
</script>
@endpush
