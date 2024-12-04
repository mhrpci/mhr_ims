@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Product Movement Analysis</h2>
        <a href="{{ route('home') }}" class="btn btn-secondary">
            <i class="bi bi-house-door"></i> Back to Home
        </a>
    </div>

    <ul class="nav nav-tabs mb-3" id="movementTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="fast-tab" data-bs-toggle="tab" href="#fast" role="tab">Fast Moving</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="moderate-tab" data-bs-toggle="tab" href="#moderate" role="tab">Moderate Moving</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="slow-tab" data-bs-toggle="tab" href="#slow" role="tab">Slow Moving</a>
        </li>
    </ul>

    <div class="tab-content" id="movementTabContent">
        @foreach(['fast' => 'Fast', 'moderate' => 'Moderate', 'slow' => 'Slow'] as $key => $label)
        <div class="tab-pane fade {{ $key === 'fast' ? 'show active' : '' }}" id="{{ $key }}" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-striped movement-table">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Total Quantity</th>
                            <th>Transactions</th>
                            <th>Average/Month</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productMovement[$key] as $item)
                        <tr>
                            <td>{{ $item['product_name'] }}</td>
                            <td>{{ number_format($item['total_quantity']) }}</td>
                            <td>{{ $item['transaction_count'] }}</td>
                            <td>{{ number_format($item['average_per_month'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No {{ strtolower($label) }} moving products found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
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
        $('.movement-table').each(function() {
            $(this).DataTable({
                "order": [[ 2, "desc" ]],
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
    });
</script>
@endpush 