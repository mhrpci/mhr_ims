<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForPhssDocument extends Model
{
    protected $fillable = [
        'for_phss_id',
        'file_path',
        'original_name',
        'file_type',
        'uploaded_by',
    ];

    /**
     * Get the PHSS record that owns the document
     */
    public function forPhss(): BelongsTo
    {
        return $this->belongsTo(ForPhss::class);
    }

    /**
     * Get the user who uploaded the document
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
} 