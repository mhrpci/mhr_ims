@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Create New Borrow Record</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('for-phss.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Borrow
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('for-phss.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                        <select class="form-select select2" name="product_id" id="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                @php
                                    $inventory = $product->inventories
                                        ->where('branch_id', auth()->user()->branch_id ?? $product->branch_id)
                                        ->first();
                                    
                                    $availableQty = $inventory ? $inventory->quantity : 0;
                                @endphp
                                <option value="{{ $product->id }}" data-inventory="{{ $availableQty }}">
                                    {{ $product->name }} 
                                    (Available: {{ $availableQty }})
                                    @if(!auth()->user()->branch_id)
                                        - Branch: {{ $product->branch->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="qty" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="qty" id="qty" class="form-control @error('qty') is-invalid @enderror" 
                               value="{{ old('qty') }}" required min="1">
                        <small class="text-muted" id="inventory-warning"></small>
                        @error('qty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                            <option value="for_demo">For Demo</option>
                            <option value="for_evaluation">For Evaluation</option>
                            <option value="returned">Returned</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="note" class="form-label">Note</label>
                        <textarea name="note" id="note" class="form-control @error('note') is-invalid @enderror" 
                                  rows="3">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="documents" class="form-label">Documents</label>
                        <input type="file" name="documents[]" id="documents" class="form-control" multiple 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">
                            You can upload multiple documents (PDF, Word, Images). Max size: 10MB each.
                        </small>
                        @error('documents.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Create Record
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

        // Fetch Hospitals
        fetch('http://192.168.1.11:8881/api/hospitals')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const hospitalSelect = document.getElementById('hospital_id');
                // Clear existing options except the first one
                hospitalSelect.innerHTML = '<option value="">Select Hospital</option>';
                
                data.data.forEach(hospital => {
                    const option = new Option(hospital.name, hospital.id);
                    hospitalSelect.add(option);
                });
                
                // If there's an old value, set it
                @if(old('hospital_id'))
                    hospitalSelect.value = "{{ old('hospital_id') }}";
                @endif
                
                $(hospitalSelect).trigger('change');
            })
            .catch(error => {
                console.error('Error fetching hospitals:', error);
                alert('Error loading hospitals. Please refresh the page.');
            });

        // Fetch PHSS
        fetch('http://192.168.1.11:8881/api/phss')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const phssSelect = document.getElementById('phss_id');
                // Clear existing options except the first one
                phssSelect.innerHTML = '<option value="">Select PHSS</option>';
                
                data.data.forEach(phss => {
                    const option = new Option(phss.full_name, phss.id);
                    phssSelect.add(option);
                });
                
                // If there's an old value, set it
                @if(old('phss_id'))
                    phssSelect.value = "{{ old('phss_id') }}";
                @endif
                
                $(phssSelect).trigger('change');
            })
            .catch(error => {
                console.error('Error fetching PHSS:', error);
                alert('Error loading PHSS data. Please refresh the page.');
            });

        // Handle product selection and quantity validation
        const productSelect = document.getElementById('product_id');
        const qtyInput = document.getElementById('qty');
        const statusSelect = document.getElementById('status');
        const inventoryWarning = document.getElementById('inventory-warning');

        function validateQuantity() {
            const selectedOption = productSelect.selectedOptions[0];
            const inventoryQty = selectedOption ? parseInt(selectedOption.dataset.inventory) : 0;
            const requestedQty = parseInt(qtyInput.value) || 0;
            const status = statusSelect.value;

            // Only validate quantity for non-returned status
            if (status !== 'returned' && requestedQty > inventoryQty) {
                inventoryWarning.textContent = `Warning: Requested quantity exceeds available inventory (${inventoryQty})`;
                inventoryWarning.classList.add('text-danger');
                return false;
            }

            inventoryWarning.textContent = '';
            return true;
        }

        productSelect.addEventListener('change', validateQuantity);
        qtyInput.addEventListener('input', validateQuantity);
        statusSelect.addEventListener('change', validateQuantity);

        // Update form submission handler
        document.querySelector('form').addEventListener('submit', function(e) {
            const phssId = document.getElementById('phss_id').value;
            const hospitalId = document.getElementById('hospital_id').value;
            
            if (!phssId || !hospitalId) {
                e.preventDefault();
                alert('Please select both PHSS and Hospital');
                return;
            }

            if (!validateQuantity()) {
                e.preventDefault();
                alert('Please check the quantity. It exceeds available inventory.');
                return;
            }
        });
    });
</script>
@endpush 