@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Stock In Details</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock_ins.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Stock In
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="h4 mb-3 font-weight-bold">Stock In #{{ $stockIn->id }}</h2>
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th scope="row" class="text-muted">Product:</th>
                                    <td>{{ $stockIn->product->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Vendor:</th>
                                    <td>{{ $stockIn->vendor->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Branch:</th>
                                    <td>{{ $stockIn->branch->name }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Quantity:</th>
                                    <td>{{ $stockIn->quantity }} {{ $stockIn->unit }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Lot Number:</th>
                                    <td>{{ $stockIn->lot_number }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Expiration Date:</th>
                                    <td>{{ $stockIn->expiration_date ? $stockIn->expiration_date->format('F d, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Note:</th>
                                    <td>{{ $stockIn->note ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Date:</th>
                                    <td>{{ $stockIn->date instanceof \Carbon\Carbon ? $stockIn->date->format('F d, Y') : $stockIn->date }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Created by:</th>
                                    <td>{{ $stockIn->creator ? $stockIn->creator->username : 'System' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Updated by:</th>
                                    <td>{{ $stockIn->updater ? $stockIn->updater->username : 'System' }}</td>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-muted">Receiving Report:</th>
                                    <td>
                                        @if($stockIn->receivingReport)
                                            {{ $stockIn->receivingReport->receiving_report_number }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stockIn->has_attachments)
        <div class="mt-4">
            <h3 class="h5 mb-3">Attachments</h3>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Uploaded By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockIn->attachments as $attachment)
                        <tr>
                            <td>{{ Str::limit($attachment->original_name, 50) }}</td>
                            <td>{{ Str::upper(pathinfo($attachment->file_name, PATHINFO_EXTENSION)) }}</td>
                            <td>{{ number_format($attachment->file_size / 1024, 2) }} KB</td>
                            <td>{{ $attachment->uploader->username }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ Storage::url($attachment->file_path) }}" 
                                       class="btn btn-sm btn-primary" 
                                       target="_blank">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                    @php
                                        $extension = strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION));
                                        $isPreviewable = in_array($extension, ['pdf', 'jpg', 'jpeg', 'png']);
                                    @endphp
                                    
                                    @if($isPreviewable)
                                        <button type="button" 
                                                class="btn btn-sm btn-info preview-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#previewModal"
                                                data-file-url="{{ Storage::url($attachment->file_path) }}"
                                                data-file-type="{{ $extension }}"
                                                data-file-name="{{ $attachment->original_name }}">
                                            <i class="bi bi-eye"></i> Preview
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Preview Modal -->
        <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel">File Preview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="previewContainer" class="text-center">
                            <!-- Preview content will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const previewButtons = document.querySelectorAll('.preview-btn');
    const previewContainer = document.getElementById('previewContainer');
    const modalTitle = document.getElementById('previewModalLabel');

    previewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const fileUrl = this.dataset.fileUrl;
            const fileType = this.dataset.fileType;
            const fileName = this.dataset.fileName;
            
            modalTitle.textContent = `Preview: ${fileName}`;
            previewContainer.innerHTML = ''; // Clear previous content

            if (fileType === 'pdf') {
                // PDF Preview
                const embed = document.createElement('embed');
                embed.src = fileUrl;
                embed.type = 'application/pdf';
                embed.style.width = '100%';
                embed.style.height = '600px';
                previewContainer.appendChild(embed);
            } else if (['jpg', 'jpeg', 'png'].includes(fileType)) {
                // Image Preview
                const img = document.createElement('img');
                img.src = fileUrl;
                img.classList.add('img-fluid');
                img.style.maxHeight = '600px';
                previewContainer.appendChild(img);
            }
        });
    });

    // Clear preview content when modal is closed
    const previewModal = document.getElementById('previewModal');
    previewModal.addEventListener('hidden.bs.modal', function () {
        previewContainer.innerHTML = '';
    });
});
</script>
@endpush

@push('styles')
<style>
.modal-lg {
    max-width: 90%;
}

#previewContainer {
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

#previewContainer img {
    max-width: 100%;
    height: auto;
    object-fit: contain;
}

.btn-group {
    gap: 0.25rem;
}
</style>
@endpush
