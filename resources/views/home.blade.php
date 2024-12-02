@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @php
        $hour = date('H');
        $greeting = match(true) {
            $hour < 12 => 'Good Morning',
            $hour < 15 => 'Good Afternoon',
            $hour < 18 => 'Good Evening',
            default => 'Good Night'
        };
    @endphp

    <div class="d-flex align-items-center mb-4">
        <h2 class="text-muted mb-0">{{ $greeting }}, {{ Auth::user()->username }}!</h2>
    </div>

    <h1 class="mb-4 text-primary fw-bold">Dashboard</h1>

    <div class="row g-4">
        <div class="col-md-3 col-sm-6">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Total Products</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-seam fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $totalProducts }}</h2>
                    </div>
                </div>
            </div>
        </div>
        @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
        <div class="col-md-3 col-sm-6">
            <div class="card bg-info text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Total Branches</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-building fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $totalBranches }}</h2>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-3 col-sm-6">
            <div class="card bg-success text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Available Products</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $availableProducts }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-warning text-dark h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Stock In (This Month)</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-arrow-in-down fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $stockInThisMonth }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card bg-danger text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Stock Out (This Month)</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-box-arrow-right fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $stockOutThisMonth }}</h2>
                    </div>
                </div>
            </div>
        </div>
        @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
        <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Total Users</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $totalUsers }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card bg-info text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Total Categories</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-grid fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $totalCategories }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Total Vendors</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-shop fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $totalVendors }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card bg-success text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Total Customers</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-check fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $totalCustomers }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card bg-dark text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Total Tools</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-tools fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $totalTools }}</h2>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-3 col-sm-6">
            <div class="card bg-warning text-dark h-100 shadow-sm">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title fw-light">Near Expiry Products</h5>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle fs-1 me-3"></i>
                        <h2 class="card-text mb-0 fw-bold">{{ $nearExpiryCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-lg-{{ Auth::user()->hasRole(['Admin', 'Super Admin']) ? '8' : '12' }}">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Inventory Trends</h5>
                </div>
                <div class="card-body">
                    <canvas id="inventoryTrendsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">User Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="userDistributionChart" width="200" height="200"></canvas>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row mt-4 g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Inventory Movements</h5>
                    <a href="{{ route('inventory.movements') }}" class="btn btn-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Branch Details</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentInventoryMovements as $movement)
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
                                            <span class="badge bg-info">
                                                Stock Transfer
                                            </span>
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Products Near Expiration</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Lot Number</th>
                                    <th>Quantity</th>
                                    <th>Expiration Date</th>
                                    <th>Days Until Expiry</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nearExpiryProducts as $product)
                                <tr>
                                    <td>{{ $product->product->name }}</td>
                                    <td>{{ $product->lot_number }}</td>
                                    <td>{{ number_format($product->quantity) }} {{ $product->unit }}</td>
                                    <td>{{ $product->expiration_date->format('M d, Y') }}</td>
                                    <td>{{ $product->days_until_expiry }} days</td>
                                    <td>
                                        <span class="badge bg-{{ $product->days_until_expiry <= 30 ? 'danger' : 'warning' }}">
                                            {{ $product->days_until_expiry <= 30 ? 'Critical' : 'Warning' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
<script>
    // Inventory Trends Chart
    var ctx = document.getElementById('inventoryTrendsChart').getContext('2d');
    var inventoryTrendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($inventoryTrends['labels']),
            datasets: [{
                label: 'Stock In',
                data: @json($inventoryTrends['stockIn']),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            },
            {
                label: 'Stock Out',
                data: @json($inventoryTrends['stockOut']),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    @if(Auth::user()->hasRole(['Admin', 'Super Admin']))
    // User Distribution Chart
    var ctx2 = document.getElementById('userDistributionChart').getContext('2d');
    var userDistributionChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Admins', 'Stock Managers', 'Branch Managers'],
            datasets: [{
                data: [
                    {{ $userDistribution['admins'] }},
                    {{ $userDistribution['stock_managers'] }},
                    {{ $userDistribution['branch_managers'] }}
                ],
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif

    // Initialize Laravel Echo
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config('broadcasting.connections.pusher.key') }}',
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
        encrypted: true
    });

    // Listen for stock transfer notifications
    Echo.private('stock-transfers')
        .listen('StockTransferRequested', (e) => {
            // Refresh the notifications list
            location.reload();
        });

    // Mark notification as read
    document.querySelectorAll('.mark-as-read').forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            fetch(`/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Remove the notification item from the list
                const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                notificationItem.remove();

                // Update the counter
                const counter = document.querySelector('.pending-count');
                counter.textContent = parseInt(counter.textContent) - 1;
            });
        });
    });
</script>
@endpush
