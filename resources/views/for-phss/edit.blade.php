@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Edit Borrow Record</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('for-phss.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Borrow
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('for-phss.update', $forPhss->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">Product</label>
                        <input type="text" class="form-control" value="{{ $forPhss->product->name }}" readonly>
                        <small class="text-muted">
                            Current Inventory: {{ $forPhss->inventory->qty ?? 'N/A' }}
                        </small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="qty" class="form-label">Quantity</label>
                        <input type="text" class="form-control" value="{{ $forPhss->qty }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phss_id" class="form-label">PHSS ID <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="phss_id" id="phss_id" required>
                            <option value="">Select PHSS</option>
                        </select>
                        @error('phss_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="hospital_id" class="form-label">Hospital ID <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="hospital_id" id="hospital_id" required>
                            <option value="">Select Hospital</option>
                        </select>
                        @error('hospital_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="for_demo" {{ $forPhss->status === 'for_demo' ? 'selected' : '' }}>For Demo</option>
                            <option value="for_evaluation" {{ $forPhss->status === 'for_evaluation' ? 'selected' : '' }}>For Evaluation</option>
                            <option value="returned" {{ $forPhss->status === 'returned' ? 'selected' : '' }}>Returned</option>
                        </select>
                        @if(in_array($forPhss->status, ['for_demo', 'for_evaluation']))
                            <small class="text-info">
                                Note: Changing to 'Returned' will add {{ $forPhss->qty }} items back to inventory
                            </small>
                        @elseif($forPhss->status === 'returned')
                            <small class="text-warning">
                                Note: Changing from 'Returned' will deduct {{ $forPhss->qty }} items from inventory
                            </small>
                        @endif
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" 
                                  rows="3">{{ old('note', $forPhss->note) }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        // Store current values for comparison
        const currentPhssId = '{{ $forPhss->phss_id }}';
        const currentHospitalId = '{{ $forPhss->hospital_id }}';

        // API Base URL
        const apiBaseUrl = 'http://192.168.1.30:8881/api';

        // Fetch Hospitals with error handling and retry mechanism
        async function fetchHospitals(retryCount = 0) {
            try {
                const response = await fetch(`${apiBaseUrl}/hospitals`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                
                const hospitalSelect = document.getElementById('hospital_id');
                hospitalSelect.innerHTML = '<option value="">Select Hospital</option>';
                
                data.data.forEach(hospital => {
                    const option = new Option(hospital.name, hospital.id);
                    if (hospital.id == currentHospitalId) {
                        option.selected = true;
                    }
                    hospitalSelect.add(option);
                });
                
                $(hospitalSelect).trigger('change');
            } catch (error) {
                console.error('Error fetching hospitals:', error);
                if (retryCount < 3) {
                    console.log(`Retrying hospital fetch... Attempt ${retryCount + 1}`);
                    setTimeout(() => fetchHospitals(retryCount + 1), 1000 * (retryCount + 1));
                } else {
                    alert('Error loading hospitals. Please refresh the page.');
                }
            }
        }

        // Fetch PHSS with error handling and retry mechanism
        async function fetchPhss(retryCount = 0) {
            try {
                const response = await fetch(`${apiBaseUrl}/phss`);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const data = await response.json();
                
                const phssSelect = document.getElementById('phss_id');
                phssSelect.innerHTML = '<option value="">Select PHSS</option>';
                
                data.data.forEach(phss => {
                    const option = new Option(phss.full_name, phss.id);
                    if (phss.id == currentPhssId) {
                        option.selected = true;
                    }
                    phssSelect.add(option);
                });
                
                $(phssSelect).trigger('change');
            } catch (error) {
                console.error('Error fetching PHSS:', error);
                if (retryCount < 3) {
                    console.log(`Retrying PHSS fetch... Attempt ${retryCount + 1}`);
                    setTimeout(() => fetchPhss(retryCount + 1), 1000 * (retryCount + 1));
                } else {
                    alert('Error loading PHSS data. Please refresh the page.');
                }
            }
        }

        // Initialize fetching
        fetchHospitals();
        fetchPhss();

        // Handle status change warning
        const statusSelect = document.getElementById('status');
        const originalStatus = '{{ $forPhss->status }}';
        
        statusSelect.addEventListener('change', function(e) {
            const newStatus = e.target.value;
            
            if (originalStatus !== newStatus) {
                if (originalStatus === 'returned' && ['for_demo', 'for_evaluation'].includes(newStatus)) {
                    if (!confirm('This will deduct {{ $forPhss->qty }} items from inventory. Continue?')) {
                        e.target.value = originalStatus;
                        return;
                    }
                } else if (['for_demo', 'for_evaluation'].includes(originalStatus) && newStatus === 'returned') {
                    if (!confirm('This will return {{ $forPhss->qty }} items to inventory. Continue?')) {
                        e.target.value = originalStatus;
                        return;
                    }
                }
            }
        });

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const phssId = document.getElementById('phss_id').value;
            const hospitalId = document.getElementById('hospital_id').value;
            const qty = document.getElementById('qty').value;
            const productId = document.getElementById('product_id').value;
            
            if (!phssId || !hospitalId || !qty || !productId) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return;
            }

            // Additional validation can be added here
            if (parseInt(qty) <= 0) {
                e.preventDefault();
                alert('Quantity must be greater than 0');
                return;
            }
        });

        // Handle product change
        document.getElementById('product_id').addEventListener('change', function(e) {
            const selectedOption = e.target.selectedOptions[0];
            if (selectedOption) {
                const stockText = selectedOption.text.match(/Stock: (\d+)/);
                if (stockText) {
                    const stockQty = parseInt(stockText[1]);
                    const qtyInput = document.getElementById('qty');
                    qtyInput.max = stockQty;
                }
            }
        });
    });
</script>
@endpush
