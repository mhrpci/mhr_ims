<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with(['category', 'branch'])->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Barcode',
            'Description',
            'Category',
            'Branch',
            'Created At'
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->barcode,
            $product->description,
            $product->category->name,
            $product->branch->name,
            $product->created_at->format('Y-m-d H:i:s')
        ];
    }
}
