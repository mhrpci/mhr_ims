<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivingReport extends Model
{
    protected $fillable = [
        'item_code',
        'receiving_report_number',
        'name',
        'barcode',
        'quantity',
        'unit',
        'date_received',
        'branch_id',
        'category_id',
        'vendor_id',
    ];

    protected $casts = [
        'date_received' => 'date',
    ];

    /**
     * Get the branch that owns the receiving report.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the category that owns the receiving report.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the vendor that owns the receiving report.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the stock in record associated with this receiving report.
     */
    public function stockIn()
    {
        return $this->hasOne(StockIn::class);
    }
}
