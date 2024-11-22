<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'branch_product')->withPivot('stock_quantity');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function transferRequestsFrom()
    {
        return $this->hasMany(TransferRequest::class, 'source_branch_id');
    }

    public function transferRequestsTo()
    {
        return $this->hasMany(TransferRequest::class, 'destination_branch_id');
    }

    public function getProductStock($productId)
    {
        return $this->products()
            ->where('product_id', $productId)
            ->value('stock_quantity') ?? 0;
    }

    public function updateProductStock($productId, $quantity)
    {
        return $this->products()
            ->updateOrCreate(
                ['product_id' => $productId],
                ['stock_quantity' => $quantity]
            );
    }
}
