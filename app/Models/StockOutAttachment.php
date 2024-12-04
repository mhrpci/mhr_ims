<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOutAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_out_id',
        'file_name',
        'original_name',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by'
    ];

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
} 