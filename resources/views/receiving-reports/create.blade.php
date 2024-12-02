@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Create Receiving Report</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('receiving-reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('receiving-reports.store') }}" method="POST" id="receiving-form">
                @csrf
                
                <!-- Header Information -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="receiving_report_number" class="form-label">Report Number <span class="text-danger">*</span></label>
                        <input type="text" name="receiving_report_number" id="receiving_report_number" 
                               class="form-control @error('receiving_report_number') is-invalid @enderror" 
                               value="{{ old('receiving_report_number') }}" 
                               placeholder="Enter report number" required>
                    </div>
                    <div class="col-md-4">
                        <label for="date_received" class="form-label">Date Received <span class="text-danger">*</span></label>
                        <input type="date" name="date_received" id="date_received" 
                               class="form-control @error('date_received') is-invalid @enderror" 
                               value="{{ old('date_received', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                        <select name="branch_id" id="branch_id" class="form-select select2" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered" id="items-table">
                        <thead class="bg-light">
                            <tr>
                                <th>Item Code</th>
                                <th>Name</th>
                                <th>Barcode</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Vendor</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="item-row">
                                <td>
                                    <input type="text" name="items[0][item_code]" 
                                           class="form-control form-control-sm item-code" 
                                           placeholder="Enter item code" required>
                                </td>
                                <td>
                                    <input type="text" name="items[0][name]" 
                                           class="form-control form-control-sm" 
                                           placeholder="Enter item name" required>
                                </td>
                                <td>
                                    <input type="text" name="items[0][barcode]" 
                                           class="form-control form-control-sm" 
                                           placeholder="Enter barcode" required>
                                </td>
                                <td>
                                    <select name="items[0][category_id]" 
                                            class="form-select form-select-sm category-select" required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="items[0][quantity]" 
                                           class="form-control form-control-sm quantity-input" 
                                           min="0" step="0.01" placeholder="0.00" required>
                                </td>
                                <td>
                                    <input type="text" name="items[0][unit]" 
                                           class="form-control form-control-sm" 
                                           placeholder="e.g., pcs, kg" required>
                                </td>
                                <td>
                                    <select name="items[0][vendor_id]" 
                                            class="form-select form-select-sm vendor-select">
                                        <option value="">Select Vendor</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mb-4">
                    <div class="col">
                        <button type="button" class="btn btn-success" id="add-row">
                            <i class="bi bi-plus-circle me-2"></i>Add Item
                        </button>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" name="action" value="save" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create Report
                    </button>
                    <button type="submit" name="action" value="save_and_new" class="btn btn-secondary">
                        <i class="bi bi-plus-square me-2"></i>Save and Create New
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        initializeSelect2();
        
        // Initialize row counter
        let rowCounter = 0;
        
        // Template for new row
        function getNewRow(index) {
            return `
                <tr class="item-row">
                    <td>
                        <input type="text" name="items[${index}][item_code]" 
                               class="form-control form-control-sm item-code" 
                               placeholder="Enter item code" required>
                    </td>
                    <td>
                        <input type="text" name="items[${index}][name]" 
                               class="form-control form-control-sm" 
                               placeholder="Enter item name" required>
                    </td>
                    <td>
                        <input type="text" name="items[${index}][barcode]" 
                               class="form-control form-control-sm" 
                               placeholder="Enter barcode" required>
                    </td>
                    <td>
                        <select name="items[${index}][category_id]" 
                                class="form-select form-select-sm category-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[${index}][quantity]" 
                               class="form-control form-control-sm quantity-input" 
                               min="0" step="0.01" placeholder="0.00" required>
                    </td>
                    <td>
                        <input type="text" name="items[${index}][unit]" 
                               class="form-control form-control-sm" 
                               placeholder="e.g., pcs, kg" required>
                    </td>
                    <td>
                        <select name="items[${index}][vendor_id]" 
                                class="form-select form-select-sm vendor-select">
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        // Add new row
        $('#add-row').click(function() {
            rowCounter++;
            const newRow = $(getNewRow(rowCounter));
            $('#items-table tbody').append(newRow);
            
            // Initialize Select2 for the new row's selects
            newRow.find('.category-select').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            
            newRow.find('.vendor-select').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            // Focus on the name input instead of item code
            newRow.find('input[name$="[name]"]').focus();
        });

        // Remove row
        $(document).on('click', '.remove-row', function() {
            const row = $(this).closest('tr');
            
            // Destroy Select2 instances before removing the row
            row.find('.category-select, .vendor-select').select2('destroy');
            row.remove();
            
            reindexRows();
        });

        // Reindex rows after removal
        function reindexRows() {
            $('.item-row').each(function(index) {
                $(this).find('[name^="items["]').each(function() {
                    const name = $(this).attr('name');
                    const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
                    $(this).attr('name', newName);
                });
            });
        }

        // Initialize Select2 for existing selects
        function initializeSelect2() {
            $('.category-select, .vendor-select').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        // Form submission validation
        $('#receiving-form').on('submit', function(e) {
            // Check if there are any items
            if ($('.item-row').length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the receiving report.');
                return false;
            }

            // Check for duplicate item codes
            const itemCodes = new Set();
            let hasDuplicates = false;

            $('.item-code').each(function() {
                const code = $(this).val().trim();
                if (itemCodes.has(code)) {
                    hasDuplicates = true;
                    return false; // break the loop
                }
                itemCodes.add(code);
            });

            if (hasDuplicates) {
                e.preventDefault();
                alert('Please ensure all item codes are unique.');
                return false;
            }
        });

        // Auto-calculate totals (optional)
        $(document).on('input', '.quantity-input', function() {
            calculateTotals();
        });

        function calculateTotals() {
            let totalQuantity = 0;
            $('.quantity-input').each(function() {
                const qty = parseFloat($(this).val()) || 0;
                totalQuantity += qty;
            });
            
            // You can add a total row or display the total somewhere if needed
            // $('#total-quantity').text(totalQuantity.toFixed(2));
        }
    });
</script>
@endpush 