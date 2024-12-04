<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_in_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
} 