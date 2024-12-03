@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Products Near Expiration</h2>
        <a href="{{ route('home') }}" class="btn btn-secondary">
            <i class="bi bi-house-door"></i> Back to Home
        </a>
    </div>
    <div class="table-responsive">
        <table id="near-expiry-table" class="table table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Lot Number</th>
                    <th>Quantity</th>
                    <th>Expiration Date</th>
                    <th>Days Until Expiry</th>
                    <th>Status</th>
                    <th>Branch</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nearExpiryProducts as $product)
                <tr>
                    <td>
                        <strong>{{ $product->product->name }}</strong>
                        <br>
                        <small class="text-muted">Code: {{ $product->product->code }}</small>
                    </td>
                    <td>{{ $product->lot_number }}</td>
                    <td>{{ number_format($product->quantity) }} {{ $product->unit }}</td>
                    <td>{{ $product->expiration_date->format('M d, Y') }}</td>
                    <td>{{ $product->days_until_expiry }} days</td>
                    <td>
                        <span class="badge bg-{{ $product->days_until_expiry <= 30 ? 'danger' : 'warning' }}">
                            {{ $product->days_until_expiry <= 30 ? 'Critical' : 'Warning' }}
                        </span>
                    </td>
                    <td>{{ $product->branch->name ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#near-expiry-table').DataTable({
            "order": [[ 4, "asc" ]],
            "pageLength": 25,
            "language": {
                "lengthMenu": "Show _MENU_ products per page",
                "zeroRecords": "No products found",
                "info": "Showing page _PAGE_ of _PAGES_",
                "infoEmpty": "No products available",
                "infoFiltered": "(filtered from _MAX_ total products)",
                "search": "Search products:"
            },
            "responsive": true,
            "autoWidth": false
        });
    });
</script>
@endpush 