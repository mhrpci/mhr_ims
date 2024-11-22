<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'start_date',
        'end_date',
        'branch_id',
        'generated_by',
        'status',
        'file_path',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getReportData()
    {
        switch ($this->type) {
            case 'stock_in':
                return $this->getStockInReport();
            case 'stock_out':
                return $this->getStockOutReport();
            case 'inventory':
                return $this->getInventoryReport();
            case 'product_performance':
                return $this->getProductPerformanceReport();
            default:
                return null;
        }
    }

    private function getStockInReport()
    {
        return StockIn::with(['product', 'vendor', 'branch'])
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->when($this->branch_id, function ($query) {
                return $query->where('branch_id', $this->branch_id);
            })
            ->get();
    }

    private function getStockOutReport()
    {
        return StockOut::with(['product', 'customer', 'branch'])
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->when($this->branch_id, function ($query) {
                return $query->where('branch_id', $this->branch_id);
            })
            ->get();
    }

    private function getInventoryReport()
    {
        return Inventory::with(['product', 'branch'])
            ->when($this->branch_id, function ($query) {
                return $query->where('branch_id', $this->branch_id);
            })
            ->get();
    }

    private function getProductPerformanceReport()
    {
        $stockOut = DB::table('stock_outs')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total_price) as total_revenue'))
            ->whereBetween('date', [$this->start_date, $this->end_date])
            ->when($this->branch_id, function ($query) {
                return $query->where('branch_id', $this->branch_id);
            })
            ->groupBy('product_id');

        return Product::leftJoinSub($stockOut, 'stock_out', function ($join) {
            $join->on('products.id', '=', 'stock_out.product_id');
        })
        ->select('products.*', 'stock_out.total_sold', 'stock_out.total_revenue')
        ->orderByDesc('stock_out.total_revenue')
        ->get();
    }

    public function getSummary()
    {
        switch ($this->type) {
            case 'stock_in':
                return $this->getStockInSummary();
            case 'stock_out':
                return $this->getStockOutSummary();
            case 'inventory':
                return $this->getInventorySummary();
            case 'product_performance':
                return $this->getProductPerformanceSummary();
            default:
                return null;
        }
    }

    private function getStockInSummary()
    {
        return StockIn::whereBetween('date', [$this->start_date, $this->end_date])
            ->when($this->branch_id, function ($query) {
                return $query->where('branch_id', $this->branch_id);
            })
            ->selectRaw('COUNT(*) as total_transactions, SUM(quantity) as total_quantity, SUM(total_price) as total_value')
            ->first();
    }

    private function getStockOutSummary()
    {
        return StockOut::whereBetween('date', [$this->start_date, $this->end_date])
            ->when($this->branch_id, function ($query) {
                return $query->where('branch_id', $this->branch_id);
            })
            ->selectRaw('COUNT(*) as total_transactions, SUM(quantity) as total_quantity, SUM(total_price) as total_value')
            ->first();
    }

    private function getInventorySummary()
    {
        return Inventory::when($this->branch_id, function ($query) {
                return $query->where('branch_id', $this->branch_id);
            })
            ->selectRaw('COUNT(DISTINCT product_id) as total_products, SUM(quantity) as total_quantity')
            ->first();
    }

    private function getProductPerformanceSummary()
    {
        return StockOut::whereBetween('date', [$this->start_date, $this->end_date])
            ->when($this->branch_id, function ($query) {
                return $query->where('branch_id', $this->branch_id);
            })
            ->selectRaw('COUNT(DISTINCT product_id) as total_products, SUM(quantity) as total_quantity_sold, SUM(total_price) as total_revenue')
            ->first();
    }
}
