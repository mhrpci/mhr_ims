<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function query()
    {
        $query = Product::query()
            ->with(['category', 'branch', 'inventories'])
            ->orderBy('name');

        if ($this->user->isBranchRestricted()) {
            $query->where('branch_id', $this->user->branch_id);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Name',
            'Barcode',
            'Description',
            'Category',
            'Branch',
            'Current Stock',
            'Created At',
            'Last Updated'
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->name,
            $product->barcode,
            $product->description,
            $product->category->name ?? 'N/A',
            $product->branch->name ?? 'N/A',
            $product->inventories->sum('quantity'),
            $product->created_at->format('Y-m-d H:i:s'),
            $product->updated_at->format('Y-m-d H:i:s')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }
}
