<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForPhss extends Model
{
    protected $fillable = [
        'product_id',
        'qty',
        'phss_id',
        'hospital_id',
        'status',
        'note',
        'created_by',
        'inventory_id',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $table = 'for_phsses';

    /**
     * Get the product that owns the ForPhss
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
