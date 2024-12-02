<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Branch;
use App\Models\Tool;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Inventory;
use App\Models\ReceivingReport;
use Illuminate\Support\Facades\Log;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');
        $results = [];

        // Get user's branch_id
        $userBranchId = auth()->user()->branch_id;

        if (strlen($query) >= 1) {
            $results = array_merge(
                $this->searchProducts($query),
                $this->searchCategories($query),
                $this->searchCustomers($query),
                $this->searchVendors($query),
                $this->searchBranches($query),
                $this->searchTools($query),
                $this->searchStockIns($query),
                $this->searchStockOuts($query),
                $this->searchInventories($query),
                $this->searchReceivingReports($query)
            );

            usort($results, function($a, $b) use ($query) {
                $aExact = stripos($a['text'], $query) === 0;
                $bExact = stripos($b['text'], $query) === 0;

                if ($aExact && !$bExact) return -1;
                if (!$aExact && $bExact) return 1;

                return strcmp($a['text'], $b['text']);
            });

            $results = array_slice($results, 0, 20);
        }

        return response()->json($results);
    }

    private function searchProducts($query)
    {
        $products = Product::where(function($q) use ($query) {
            $q->where('name', 'like', "%$query%")
              ->orWhere('description', 'like', "%$query%")
              ->orWhere('barcode', 'like', "%$query%");
        })
        ->orWhereHas('category', function($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        })
        ->limit(5)
        ->get();

        Log::info('Query: ' . $query);
        Log::info('Products found: ' . $products->count());
        Log::info('Products: ' . $products->toJson());

        return $products->map(function($product) use ($query) {
            $nameHighlight = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<strong>$1</strong>', $product->name);
            return [
                'id' => 'product_' . $product->id,
                'text' => $nameHighlight,
                'subtext' => "Barcode: {$product->barcode} | Category: {$product->category->name} | " .
                             "Stock: {$product->inventories->sum('quantity')} | " .
                             "Description: " . substr($product->description, 0, 50) . '...',
                'model' => 'Product',
                'url' => route('products.show', $product->id)
            ];
        })->toArray();
    }

    private function searchCategories($query)
    {
        return Category::where('name', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->limit(5)
            ->get()
            ->map(function($category) {
                return [
                    'id' => 'category_' . $category->id,
                    'text' => $category->name,
                    'subtext' => "Products: {$category->products->count()} | Description: " . substr($category->description, 0, 50) . '...',
                    'model' => 'Category',
                    'url' => route('categories.show', $category->id)
                ];
            })
            ->toArray();
    }

    private function searchCustomers($query)
    {
        return Customer::where('name', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->orWhere('phone', 'like', "%$query%")
            ->limit(5)
            ->get()
            ->map(function($customer) {
                return [
                    'id' => 'customer_' . $customer->id,
                    'text' => $customer->name,
                    'subtext' => "Email: {$customer->email} | Phone: {$customer->phone}",
                    'model' => 'Customer',
                    'url' => route('customers.show', $customer->id)
                ];
            })
            ->toArray();
    }

    private function searchVendors($query)
    {
        return Vendor::where('name', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->orWhere('phone', 'like', "%$query%")
            ->limit(5)
            ->get()
            ->map(function($vendor) {
                return [
                    'id' => 'vendor_' . $vendor->id,
                    'text' => $vendor->name,
                    'subtext' => "Email: {$vendor->email}",
                    'model' => 'Vendor',
                    'url' => route('vendors.show', $vendor->id)
                ];
            })
            ->toArray();
    }

    private function searchBranches($query)
    {
        return Branch::where('name', 'like', "%$query%")
            ->orWhere('address', 'like', "%$query%")
            ->orWhere('phone', 'like', "%$query%")
            ->limit(5)
            ->get()
            ->map(function($branch) {
                return [
                    'id' => 'branch_' . $branch->id,
                    'text' => $branch->name,
                    'subtext' => "Phone: {$branch->phone}",
                    'model' => 'Branch',
                    'url' => route('branches.show', $branch->id)
                ];
            })
            ->toArray();
    }

    private function searchTools($query)
    {
        return Tool::where('tool_name', 'like', "%$query%")
            ->orWhere('assigned_to', 'like', "%$query%")
            ->orWhereHas('branch', function($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->limit(5)
            ->get()
            ->map(function($tool) {
                return [
                    'id' => 'tool_' . $tool->id,
                    'text' => $tool->tool_name,
                    'subtext' => "Assigned to: {$tool->assigned_to} | Branch: {$tool->branch->name}",
                    'model' => 'Tool',
                    'url' => route('tools.show', $tool->id)
                ];
            })
            ->toArray();
    }

    private function searchStockIns($query)
    {
        return StockIn::where('lot_number', 'like', "%$query%")
                ->orWhereHas('product', function($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                })
                ->orWhereHas('vendor', function($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                })
                ->orWhereHas('branch', function($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                })
                ->limit(5)
                ->get()
                ->map(function($stockIn) {
                    return [
                        'id' => 'stockin_' . $stockIn->id,
                        'text' => "Stock In: {$stockIn->product->name}",
                        'subtext' => "Lot #: {$stockIn->lot_number}, Quantity: {$stockIn->quantity}, Date: {$stockIn->date->format('Y-m-d')}, Branch: {$stockIn->branch->name}",
                        'model' => 'Stock In',
                        'url' => route('stock_ins.show', $stockIn->id)
                    ];
                })
                ->toArray();
    }

    private function searchStockOuts($query)
    {
        return StockOut::where('stock_out_number', 'like', "%$query%")
                ->orWhereHas('product', function($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                })
                ->orWhereHas('customer', function($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                })
                ->orWhereHas('branch', function($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                })
                ->limit(5)
                ->get()
                ->map(function($stockOut) {
                    return [
                        'id' => 'stockout_' . $stockOut->id,
                        'text' => "Stock Out: {$stockOut->product->name}",
                        'subtext' => "Stock Out #: {$stockOut->stock_out_number}, Quantity: {$stockOut->quantity}, Date: {$stockOut->date->format('Y-m-d')}, Branch: {$stockOut->branch->name}",
                        'model' => 'Stock Out',
                        'url' => route('stock_outs.show', $stockOut->id)
                    ];
                })
                ->toArray();
    }

    private function searchInventories($query)
    {
        return Inventory::whereHas('product', function($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->orWhereHas('branch', function($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->limit(5)
            ->get()
            ->map(function($inventory) {
                return [
                    'id' => 'inventory_' . $inventory->id,
                    'text' => "{$inventory->product->name} at {$inventory->branch->name}",
                    'subtext' => "Quantity: {$inventory->quantity} | Last updated: {$inventory->updated_at->format('Y-m-d H:i')}",
                    'model' => 'Inventory',
                    'url' => route('inventories.show', $inventory->id)
                ];
            })
            ->toArray();
    }

    private function searchReceivingReports($query)
    {
        $receivingReports = ReceivingReport::where(function($q) use ($query) {
            $q->where('receiving_report_number', 'like', "%$query%")
              ->orWhere('item_code', 'like', "%$query%")
              ->orWhere('name', 'like', "%$query%")
              ->orWhere('barcode', 'like', "%$query%");
        })
        ->orWhereHas('vendor', function($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        })
        ->orWhereHas('branch', function($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        })
        ->orWhereHas('category', function($q) use ($query) {
            $q->where('name', 'like', "%$query%");
        });

        // Filter by user's branch if they have one assigned
        if (auth()->user()->branch_id) {
            $receivingReports->where('branch_id', auth()->user()->branch_id);
        }

        return $receivingReports->limit(5)
            ->get()
            ->map(function($report) {
                return [
                    'id' => 'receiving_report_' . $report->id,
                    'text' => "RR#{$report->receiving_report_number}: {$report->name}",
                    'subtext' => "Item Code: {$report->item_code} | " .
                                "Quantity: {$report->quantity} {$report->unit} | " .
                                "Date: {$report->date_received->format('Y-m-d')} | " .
                                "Branch: {$report->branch->name}",
                    'model' => 'Receiving Report',
                    'url' => route('receiving-reports.show', $report->id)
                ];
            })
            ->toArray();
    }
}
