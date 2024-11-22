@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Product Barcodes</h1>

        <div class="mb-4">
            <input type="text" id="search" class="form-control" placeholder="Search products...">
        </div>

        <div id="emptyState" class="text-center d-none">
            <p class="lead">No products found.</p>
        </div>

        <div class="row" id="barcodeList">
            @forelse($products as $product)
                <div class="col-md-4 mb-4 barcode-item" data-name="{{ strtolower($product->name) }}">
                    <div class="card">
                        <div class="card-body text-center">
                            <img src="data:image/png;base64,{{ base64_encode($product->generateBarcode()) }}" alt="{{ $product->name }} Barcode" class="img-fluid mb-2 barcode-image">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">{{ $product->barcode }}</p>
                            <button class="btn btn-primary print-barcode" data-barcode="{{ $product->barcode }}">Print Barcode</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="lead">No products available.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        @media print {
            body * {
                visibility: hidden;
                margin: 0;
                padding: 0;
            }
            .print-content, .print-content * {
                visibility: visible;
            }
            .print-content {
                position: absolute;
                left: 0;
                top: 0;
            }
            .print-barcode {
                width: 2in;
                height: 1in;
                float: left;
                text-align: center;
                margin: 0;
                padding: 0.125in;
                box-sizing: border-box;
                page-break-inside: avoid;
            }
            .print-barcode img {
                max-width: 1.75in;
                max-height: 0.5in;
                width: auto;
                height: auto;
                display: block;
                margin: 0 auto;
            }
            .print-barcode .product-name {
                margin: 0.05in 0;
                font-size: 8pt;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .print-barcode .barcode-number {
                margin: 0;
                font-size: 7pt;
            }
            @page {
                size: 8.5in 11in;
                margin: 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const search = document.getElementById('search');
            const barcodeItems = document.querySelectorAll('.barcode-item');
            const emptyState = document.getElementById('emptyState');
            const barcodeList = document.getElementById('barcodeList');

            search.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                let visibleItems = 0;

                barcodeItems.forEach(item => {
                    const productName = item.dataset.name;
                    if (productName.includes(searchTerm)) {
                        item.style.display = 'block';
                        visibleItems++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                if (visibleItems === 0) {
                    emptyState.classList.remove('d-none');
                    barcodeList.classList.add('d-none');
                } else {
                    emptyState.classList.add('d-none');
                    barcodeList.classList.remove('d-none');
                }
            });

            const printButtons = document.querySelectorAll('.print-barcode');
            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const barcode = this.dataset.barcode;
                    const cardBody = this.closest('.card-body');
                    const img = cardBody.querySelector('img').cloneNode(true);

                    const printWindow = window.open('', '_blank');
                    printWindow.document.write('<html><head><title>Print Barcodes</title>');
                    printWindow.document.write('<style>');
                    printWindow.document.write(`
                        body {
                            font-family: Arial, sans-serif;
                            margin: 0;
                            padding: 0;
                        }
                        .print-content {
                            width: 8.5in;
                            margin: 0;
                            padding: 0;
                        }
                        .print-barcode {
                            width: 2in;
                            height: 1in;
                            float: left;
                            text-align: center;
                            margin: 0;
                            padding: 0.125in;
                            box-sizing: border-box;
                            page-break-inside: avoid;
                        }
                        .print-barcode img {
                            max-width: 1.75in;
                            max-height: 0.5in;
                            width: auto;
                            height: auto;
                            display: block;
                            margin: 0 auto;
                        }
                        .print-barcode .product-name {
                            margin: 0.05in 0;
                            font-size: 8pt;
                            overflow: hidden;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                        }
                        .print-barcode .barcode-number {
                            margin: 0;
                            font-size: 7pt;
                        }
                        @page {
                            size: 8.5in 11in;
                            margin: 0;
                        }
                    `);
                    printWindow.document.write('</style></head><body>');
                    printWindow.document.write('<div class="print-content">');

                    // Calculate labels per page based on page and label dimensions
                    const pageWidth = 8.5; // inches
                    const pageHeight = 11; // inches
                    const labelWidth = 2; // inches
                    const labelHeight = 1; // inches
                    const labelsPerRow = Math.floor(pageWidth / labelWidth);
                    const labelsPerColumn = Math.floor(pageHeight / labelHeight);
                    const labelsPerPage = labelsPerRow * labelsPerColumn;

                    // Fill the entire page with labels
                    for (let i = 0; i < labelsPerPage; i++) {
                        printWindow.document.write(`
                            <div class="print-barcode">
                                ${img.outerHTML}
                                <div class="barcode-number">${barcode}</div>
                            </div>
                        `);
                    }

                    printWindow.document.write('</div></body></html>');
                    printWindow.document.close();

                    printWindow.onload = function() {
                        printWindow.focus();
                        printWindow.print();
                    };
                });
            });
        });
    </script>
@endpush
