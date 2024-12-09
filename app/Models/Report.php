<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'report_number',
        'report_type',
        'branch_id',
        'date_from',
        'date_to',
        'generated_by',
        'data',
        'total_records',
        'total_amount',
        'status',
        'parameters'
    ];

    protected $casts = [
        'date_from' => 'datetime',
        'date_to' => 'datetime',
        'data' => 'array',
        'parameters' => 'array',
        'total_amount' => 'decimal:2',
        'total_records' => 'integer'
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getFormattedTypeAttribute(): string
    {
        return match($this->report_type) {
            'inventory' => 'Inventory Status Report',
            'stock_in' => 'Stock In Report',
            'stock_out' => 'Stock Out Report',
            'stock_transfer' => 'Stock Transfer Report',
            'receiving' => 'Receiving Report',
            'phss' => 'PHSS Report',
            default => ucwords(str_replace('_', ' ', $this->report_type))
        };
    }

    public function getDateRangeAttribute(): string
    {
        return $this->date_from->format('M d, Y') . ' - ' . $this->date_to->format('M d, Y');
    }
}
