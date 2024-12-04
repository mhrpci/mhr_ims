<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'vendor_id', 
        'branch_id', 
        'quantity',
        'unit',
        'lot_number',
        'expiration_date', 
        'note',
        'unit_price', 
        'total_price', 
        'date', 
        'transfer_request_id', 
        'created_by', 
        'updated_by',
        'has_attachments'
    ];

    protected $casts = [
        'date' => 'date',
        'expiration_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transferRequest()
    {
        return $this->belongsTo(TransferRequest::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by')->select(['id', 'username']);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by')->select(['id', 'username']);
    }

    public function attachments()
    {
        return $this->hasMany(StockInAttachment::class);
    }
}
