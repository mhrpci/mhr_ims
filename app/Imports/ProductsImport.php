<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $user;
    protected $categories;
    protected $branches;

    public function __construct($user)
    {
        $this->user = $user;
        $this->categories = Category::pluck('id', 'name');
        $this->branches = Branch::pluck('id', 'name');
    }

    public function model(array $row)
    {
        DB::beginTransaction();
        try {
            $category_id = $this->categories[$row['category']] ?? null;
            $branch_id = $this->branches[$row['branch']] ?? null;

            // Validate branch access
            if ($this->user->isBranchRestricted() && $branch_id != $this->user->branch_id) {
                throw new \Exception('You can only import products for your assigned branch.');
            }

            $product = Product::create([
                'name' => $row['name'],
                'barcode' => $row['barcode'],
                'description' => $row['description'],
                'category_id' => $category_id,
                'branch_id' => $branch_id,
                'created_by' => $this->user->id,
                'updated_by' => $this->user->id,
            ]);

            // Initialize inventory
            Inventory::create([
                'product_id' => $product->id,
                'branch_id' => $branch_id,
                'quantity' => 0,
            ]);

            DB::commit();
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'description' => 'nullable|string',
            'category' => 'required|string|exists:categories,name',
            'branch' => 'required|string|exists:branches,name',
        ];
    }
}
