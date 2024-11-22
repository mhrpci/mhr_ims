<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class ProductImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $category = Category::firstOrCreate(['name' => $row['category']]);
        $branch = Branch::firstOrCreate(['name' => $row['branch']]);

        $product = Product::create([
            'name' => $row['name'],
            'barcode' => $row['barcode'],
            'description' => $row['description'],
            'category_id' => $category->id,
            'branch_id' => $branch->id,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Initialize inventory
        $product->inventories()->create([
            'branch_id' => $branch->id,
            'quantity' => 0,
        ]);

        return $product;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'barcode' => 'required|unique:products,barcode',
            'category' => 'required|string',
            'branch' => 'required|string',
        ];
    }
}
