@extends('layouts.app')

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
            <h1 class="h3 mb-0 text-gray-800">Borrow Product</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('for-phss.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Create New Borrow
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>PHSS Name</th>
                            <th>Hospital Name</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($forPhsses as $phss)
                        <tr>
                            <td>{{ $phss->product->name }}</td>
                            <td>{{ $phss->qty }}</td>
                            <td class="phss-name" data-phss-id="{{ $phss->phss_id }}">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                Loading...
                            </td>
                            <td class="hospital-name" data-hospital-id="{{ $phss->hospital_id }}">
                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                Loading...
                            </td>
                            <td><span class="badge bg-{{ $phss->status === 'returned' ? 'success' : 'primary' }}">{{ str_replace('_', ' ', ucfirst($phss->status)) }}</span></td>
                            <td>
                                <a href="{{ route('for-phss.show', $phss) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                @if(Auth::user()->canUpdateForPhss())
                                <a href="{{ route('for-phss.edit', $phss) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                @endif
                                @if(Auth::user()->canDeleteForPhss())
                                <form action="{{ route('for-phss.destroy', $phss) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete" 
                                            onclick="return confirm('Are you sure you want to delete this record?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $forPhsses->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .alert {
        border-left: 4px solid #28a745;
    }
    .card {
        border: none;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        /**
         * Fetch and process PHSS data
         * Retrieves full_name for each PHSS ID and updates the corresponding elements
         */
        const fetchPHSSData = async () => {
            try {
                const response = await fetch('https://chuweyweb.site/api/phss');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const { data: phssData } = await response.json();
                
                // Create a map of PHSS IDs to full names
                const phssMap = new Map(
                    phssData.map(phss => [
                        String(phss.id), // Ensure ID is string for consistent comparison
                        phss.full_name || 'Name not available'
                    ])
                );
                
                // Update all PHSS name elements
                document.querySelectorAll('.phss-name').forEach(element => {
                    const phssId = element.dataset.phssId;
                    if (phssMap.has(phssId)) {
                        element.textContent = phssMap.get(phssId);
                        element.title = phssMap.get(phssId); // Add tooltip
                    } else {
                        element.textContent = 'PHSS not found';
                        element.classList.add('text-muted');
                    }
                });
            } catch (error) {
                console.error('Error fetching PHSS data:', error);
                document.querySelectorAll('.phss-name').forEach(element => {
                    element.textContent = 'Data unavailable';
                    element.classList.add('text-danger');
                });
            }
        };

        /**
         * Fetch and process Hospital data
         * Retrieves name for each Hospital ID and updates the corresponding elements
         */
        const fetchHospitalData = async () => {
            try {
                const response = await fetch('https://chuweyweb.site/api/hospitals');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const { data: hospitalData } = await response.json();
                
                // Create a map of Hospital IDs to names
                const hospitalMap = new Map(
                    hospitalData.map(hospital => [
                        String(hospital.id), // Ensure ID is string for consistent comparison
                        hospital.name || 'Name not available'
                    ])
                );
                
                // Update all hospital name elements
                document.querySelectorAll('.hospital-name').forEach(element => {
                    const hospitalId = element.dataset.hospitalId;
                    if (hospitalMap.has(hospitalId)) {
                        element.textContent = hospitalMap.get(hospitalId);
                        element.title = hospitalMap.get(hospitalId); // Add tooltip
                    } else {
                        element.textContent = 'Hospital not found';
                        element.classList.add('text-muted');
                    }
                });
            } catch (error) {
                console.error('Error fetching hospital data:', error);
                document.querySelectorAll('.hospital-name').forEach(element => {
                    element.textContent = 'Data unavailable';
                    element.classList.add('text-danger');
                });
            }
        };

        // Initialize data fetching
        Promise.all([
            fetchPHSSData(),
            fetchHospitalData()
        ]).catch(error => {
            console.error('Error initializing data:', error);
        });
    });
</script>
@endpush 