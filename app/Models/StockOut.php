<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_out_number',
        'product_id',
        'stock_in_id',
        'customer_id',
        'branch_id',
        'quantity',
        'unit',
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
        'date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class);
    }

    public function attachments()
    {
        return $this->hasMany(StockOutAttachment::class);
    }
}
