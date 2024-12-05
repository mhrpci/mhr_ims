@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Borrow Record Details</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('for-phss.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Borrow
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Borrow Record Information</h6>
            <div class="status-badge">
                <span class="badge {{ $forPhss->status === 'pending' ? 'bg-warning' : ($forPhss->status === 'completed' ? 'bg-success' : 'bg-secondary') }}">
                    {{ ucfirst(str_replace('_', ' ', $forPhss->status)) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="border-left-primary p-3 bg-light rounded">
                        <label class="form-label fw-bold text-primary">Product Details</label>
                        <p class="mb-1"><strong>Name:</strong> {{ $forPhss->product->name }}</p>
                        <p class="mb-0"><strong>Quantity:</strong> {{ $forPhss->qty }}</p>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="border-left-info p-3 bg-light rounded">
                        <label class="form-label fw-bold text-primary">Assignment Information</label>
                        <p class="mb-1">
                            <strong>PHSS Name:</strong>
                            <span class="phss-name" data-phss-id="{{ $forPhss->phss_id }}">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                <small class="text-muted">Loading...</small>
                            </span>
                        </p>
                        <p class="mb-0">
                            <strong>Hospital:</strong>
                            <span class="hospital-name" data-hospital-id="{{ $forPhss->hospital_id }}">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                                <small class="text-muted">Loading...</small>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="border-left-warning p-3 bg-light rounded">
                        <label class="form-label fw-bold text-primary">Additional Notes</label>
                        <p class="mb-0">
                            @if($forPhss->note)
                                {{ $forPhss->note }}
                            @else
                                <span class="text-muted">No notes available</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2">
                        @if(Auth::user()->canUpdateForPhss())
                        <a href="{{ route('for-phss.edit', $forPhss->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-2"></i>Edit Record
                        </a>
                        @endif
                        @if(Auth::user()->canDeleteForPhss())
                        <form action="{{ route('for-phss.destroy', $forPhss->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                <i class="bi bi-trash me-2"></i>Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        /**
         * Fetch and display PHSS data
         */
        const fetchPHSSData = async () => {
            try {
                const response = await fetch('https://chuweyweb.site/api/phss');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const { data: phssData } = await response.json();
                
                const phssElement = document.querySelector('.phss-name');
                const phssId = phssElement.dataset.phssId;
                
                const phss = phssData.find(p => String(p.id) === phssId);
                if (phss) {
                    phssElement.textContent = phss.full_name;
                } else {
                    phssElement.textContent = 'PHSS not found';
                    phssElement.classList.add('text-muted');
                }
            } catch (error) {
                console.error('Error fetching PHSS data:', error);
                document.querySelector('.phss-name').textContent = 'Data unavailable';
                document.querySelector('.phss-name').classList.add('text-danger');
            }
        };

        /**
         * Fetch and display Hospital data
         */
        const fetchHospitalData = async () => {
            try {
                const response = await fetch('https://chuweyweb.site/api/hospitals');
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const { data: hospitalData } = await response.json();
                
                const hospitalElement = document.querySelector('.hospital-name');
                const hospitalId = hospitalElement.dataset.hospitalId;
                
                const hospital = hospitalData.find(h => String(h.id) === hospitalId);
                if (hospital) {
                    hospitalElement.textContent = hospital.name;
                } else {
                    hospitalElement.textContent = 'Hospital not found';
                    hospitalElement.classList.add('text-muted');
                }
            } catch (error) {
                console.error('Error fetching hospital data:', error);
                document.querySelector('.hospital-name').textContent = 'Data unavailable';
                document.querySelector('.hospital-name').classList.add('text-danger');
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