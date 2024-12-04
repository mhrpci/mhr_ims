<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'barcode', 'category_id', 'branch_id', 'created_by', 'updated_by'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventories(): HasMany
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

    public function generateBarcode($width = 2, $height = 100)
    {
        $generator = new BarcodeGeneratorPNG();
        return $generator->getBarcode($this->barcode, $generator::TYPE_CODE_128, $width, $height);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->select(['id', 'username']);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')->select(['id', 'username']);
    }

    public function forPhsses(): HasMany
    {
        return $this->hasMany(ForPhss::class);
    }

    public function deductInventory($qty, $branchId)
    {
        $inventory = $this->inventories()
            ->where('branch_id', $branchId)
            ->firstOrFail();

        if ($inventory->qty < $qty) {
            throw new \Exception('Insufficient inventory quantity');
        }

        $inventory->qty -= $qty;
        $inventory->save();

        return $inventory;
    }
}
