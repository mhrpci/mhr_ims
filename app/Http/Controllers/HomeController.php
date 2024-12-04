<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\User;
use App\Models\StockTransfer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        try {
            // Cache key unique to each user
            $cacheKey = "dashboard_data_user_{$user->id}";
            
            // Cache for 5 minutes (adjust duration as needed)
            $data = Cache::remember($cacheKey, 300, function () use ($user) {
                return [
                    'totalProducts' => $this->getTotalProducts($user),
                    'totalBranches' => $this->getTotalBranches($user),
                    'totalUsers' => $this->getTotalUsers($user),
                    'totalCategories' => $this->getTotalCategories($user),
                    'totalVendors' => $this->getTotalVendors($user),
                    'totalCustomers' => $this->getTotalCustomers($user),
                    'availableProducts' => $this->getAvailableProducts($user),
                    'stockInThisMonth' => $this->getStockInThisMonth($user),
                    'stockOutThisMonth' => $this->getStockOutThisMonth($user),
                    'stockTransferThisMonth' => $this->getStockTransferThisMonth($user),
                    'inventoryTrends' => $this->getInventoryTrends($user),
                    'userDistribution' => $this->getUserDistribution($user),
                    'recentInventoryMovements' => $this->getRecentInventoryMovements($user),
                    'totalTools' => $this->getTotalTools($user),
                    'nearExpiryProducts' => $this->getNearExpiryProducts($user),
                    'nearExpiryCount' => $this->getNearExpiryCount($user),
                    'productMovement' => $this->getProductMovementAnalysis($user),
                ];
            });

            return view('home', $data);
        } catch (\Exception $e) {
            return view('home', [
                'error' => 'An error occurred while loading the dashboard data.'
            ]);
        }
    }

    private function getTotalProducts($user)
    {
        if (!$user) return 0;

        try {
            $query = Product::query();

            if ($user->isBranchRestricted()) {
                $query->where('branch_id', $user->branch_id);
            }

            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getAvailableProducts($user)
    {
        if (!$user) return 0;

        try {
            $query = Product::whereHas('inventories', function ($query) use ($user) {
                $query->where('quantity', '>', 0);
                if ($user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                }
            });

            if ($user->isBranchRestricted()) {
                $query->where('branch_id', $user->branch_id);
            }

            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getStockInThisMonth($user)
    {
        if (!$user) return 0;

        try {
            $currentMonth = Carbon::now()->startOfMonth();
            $query = StockIn::where('date', '>=', $currentMonth);

            if ($user->isBranchRestricted()) {
                $query->where('branch_id', $user->branch_id);
            }

            return $query->sum('quantity') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getStockOutThisMonth($user)
    {
        if (!$user) return 0;

        try {
            $currentMonth = Carbon::now()->startOfMonth();
            $query = StockOut::where('date', '>=', $currentMonth);

            if ($user->isBranchRestricted()) {
                $query->where('branch_id', $user->branch_id);
            }

            return $query->sum('quantity') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getStockTransferThisMonth($user)
    {
        if (!$user) return 0;

        try {
            $currentMonth = Carbon::now()->startOfMonth();
            $query = StockTransfer::where('date', '>=', $currentMonth)
                ->where('status', 'approved');

            if ($user->isBranchRestricted()) {
                $query->where(function ($query) use ($user) {
                    $query->where('from_branch_id', $user->branch_id)
                        ->orWhere('to_branch_id', $user->branch_id);
                });
            }

            return $query->sum('quantity') ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getInventoryTrends($user)
    {
        if (!$user) {
            return [
                'labels' => [],
                'stockIn' => [],
                'stockOut' => [],
                'stockTransfer' => [],
            ];
        }

        try {
            $endDate = Carbon::now()->endOfMonth();
            $startDate = Carbon::now()->subMonths(5)->startOfMonth();

            // Stock In Query
            $stockInQuery = StockIn::selectRaw('DATE_FORMAT(date, "%Y-%m") as month, COALESCE(SUM(quantity), 0) as total')
                ->whereBetween('date', [$startDate, $endDate]);

            if ($user->isBranchRestricted()) {
                $stockInQuery->where('branch_id', $user->branch_id);
            }

            $stockInData = $stockInQuery->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            // Stock Out Query
            $stockOutQuery = StockOut::selectRaw('DATE_FORMAT(date, "%Y-%m") as month, COALESCE(SUM(quantity), 0) as total')
                ->whereBetween('date', [$startDate, $endDate]);

            if ($user->isBranchRestricted()) {
                $stockOutQuery->where('branch_id', $user->branch_id);
            }

            $stockOutData = $stockOutQuery->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            // Stock Transfer Query
            $stockTransferQuery = StockTransfer::selectRaw('DATE_FORMAT(date, "%Y-%m") as month, COALESCE(SUM(quantity), 0) as total')
                ->where('status', 'approved')
                ->whereBetween('date', [$startDate, $endDate]);

            if ($user->isBranchRestricted()) {
                $stockTransferQuery->where(function ($query) use ($user) {
                    $query->where('from_branch_id', $user->branch_id)
                        ->orWhere('to_branch_id', $user->branch_id);
                });
            }

            $stockTransferData = $stockTransferQuery->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month')
                ->toArray();

            $labels = [];
            $stockIn = [];
            $stockOut = [];
            $stockTransfer = [];

            for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
                $monthKey = $date->format('Y-m');
                $labels[] = $date->format('M Y');
                $stockIn[] = $stockInData[$monthKey] ?? 0;
                $stockOut[] = $stockOutData[$monthKey] ?? 0;
                $stockTransfer[] = $stockTransferData[$monthKey] ?? 0;
            }

            return [
                'labels' => $labels,
                'stockIn' => $stockIn,
                'stockOut' => $stockOut,
                'stockTransfer' => $stockTransfer,
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'stockIn' => [],
                'stockOut' => [],
                'stockTransfer' => [],
            ];
        }
    }

    private function getUserDistribution($user)
    {
        if (!$user || !$user->hasRole(['Admin', 'Super Admin'])) {
            return [
                'admins' => 0,
                'stock_managers' => 0,
                'branch_managers' => 0,
            ];
        }

        try {
            return [
                'admins' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'Admin');
                })->count(),
                'stock_managers' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'Stock Manager');
                })->count(),
                'branch_managers' => User::whereHas('roles', function ($query) {
                    $query->where('name', 'Branch Manager');
                })->count(),
            ];
        } catch (\Exception $e) {
            return [
                'admins' => 0,
                'stock_managers' => 0,
                'branch_managers' => 0,
            ];
        }
    }

    private function getRecentInventoryMovements($user)
    {
        if (!$user || !$user->hasRole(['Branch Manager', 'Admin', 'Super Admin'])) {
            return collect([]);
        }

        try {
            // Stock In Query
            $stockInsQuery = StockIn::with(['product', 'branch', 'creator', 'updater'])
                ->select([
                    'product_id',
                    'branch_id',
                    'quantity',
                    'date',
                    'created_by',
                    'updated_by',
                    DB::raw("'Stock In' as action"),
                    DB::raw('NULL as to_branch_id'),
                    DB::raw('NULL as from_branch_id'),
                    DB::raw('NULL as transfer_id')
                ]);

            if ($user->isBranchRestricted()) {
                $stockInsQuery->where('branch_id', $user->branch_id);
            }

            // Stock Out Query
            $stockOutsQuery = StockOut::with(['product', 'branch', 'creator', 'updater'])
                ->select([
                    'product_id',
                    'branch_id',
                    'quantity',
                    'date',
                    'created_by',
                    'updated_by',
                    DB::raw("'Stock Out' as action"),
                    DB::raw('NULL as to_branch_id'),
                    DB::raw('NULL as from_branch_id'),
                    DB::raw('NULL as transfer_id')
                ]);

            if ($user->isBranchRestricted()) {
                $stockOutsQuery->where('branch_id', $user->branch_id);
            }

            // Stock Transfer Query
            $stockTransfersQuery = StockTransfer::with([
                    'inventory.product',
                    'fromBranch',
                    'toBranch',
                    'createdBy',
                    'updatedBy'
                ])
                ->where('status', 'approved')
                ->select([
                    'inventory_id as product_id',
                    'from_branch_id as branch_id',
                    'quantity',
                    'date',
                    'created_by',
                    'updated_by',
                    DB::raw("'Transfer' as action"),
                    'to_branch_id',
                    'from_branch_id',
                    'id as transfer_id'
                ]);

            if ($user->isBranchRestricted()) {
                $stockTransfersQuery->where(function ($query) use ($user) {
                    $query->where('from_branch_id', $user->branch_id)
                        ->orWhere('to_branch_id', $user->branch_id);
                });
            }

            $movements = $stockInsQuery
                ->union($stockOutsQuery)
                ->union($stockTransfersQuery)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();

            // Transform the collection to include proper product and branch relationships
            return $movements->map(function ($movement) {
                if ($movement->action === 'Transfer') {
                    $transfer = StockTransfer::with([
                            'inventory.product',
                            'fromBranch',
                            'toBranch',
                            'createdBy',
                            'updatedBy'
                        ])
                        ->where([
                            'inventory_id' => $movement->product_id,
                            'date' => $movement->date,
                            'quantity' => $movement->quantity,
                            'id' => $movement->transfer_id
                        ])->first();

                    if ($transfer) {
                        $movement->product = $transfer->inventory->product;
                        $movement->fromBranch = $transfer->fromBranch;
                        $movement->toBranch = $transfer->toBranch;
                        $movement->transfer = $transfer;
                    }
                }
                return $movement;
            });

        } catch (\Exception $e) {
            \Log::error('Error in getRecentInventoryMovements: ' . $e->getMessage());
            return collect([]);
        }
    }

    public function allInventoryMovements()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        try {
            $inventoryMovements = $this->getAllInventoryMovements($user);
            return view('inventory-movements', compact('inventoryMovements'));
        } catch (\Exception $e) {
            return view('inventory-movements', [
                'error' => 'An error occurred while loading the inventory movements.'
            ]);
        }
    }

    private function getAllInventoryMovements($user)
    {
        if (!$user) return collect([]);

        try {
            // Stock In Query
            $stockInsQuery = StockIn::with(['product', 'branch', 'creator', 'updater'])
                ->select([
                    'product_id',
                    'branch_id',
                    'quantity',
                    'date',
                    'created_by',
                    'updated_by',
                    DB::raw("'Stock In' as action"),
                    DB::raw('NULL as to_branch_id'),
                    DB::raw('NULL as from_branch_id'),
                    DB::raw('NULL as transfer_id')
                ]);

            if ($user->isBranchRestricted()) {
                $stockInsQuery->where('branch_id', $user->branch_id);
            }

            // Stock Out Query
            $stockOutsQuery = StockOut::with(['product', 'branch', 'creator', 'updater'])
                ->select([
                    'product_id',
                    'branch_id',
                    'quantity',
                    'date',
                    'created_by',
                    'updated_by',
                    DB::raw("'Stock Out' as action"),
                    DB::raw('NULL as to_branch_id'),
                    DB::raw('NULL as from_branch_id'),
                    DB::raw('NULL as transfer_id')
                ]);

            if ($user->isBranchRestricted()) {
                $stockOutsQuery->where('branch_id', $user->branch_id);
            }

            // Stock Transfer Query
            $stockTransfersQuery = StockTransfer::with([
                    'inventory.product',
                    'fromBranch',
                    'toBranch',
                    'createdBy',
                    'updatedBy'
                ])
                ->where('status', 'approved')
                ->select([
                    'inventory_id as product_id',
                    'from_branch_id as branch_id',
                    'quantity',
                    'date',
                    'created_by',
                    'updated_by',
                    DB::raw("'Transfer' as action"),
                    'to_branch_id',
                    'from_branch_id',
                    'id as transfer_id'
                ]);

            if ($user->isBranchRestricted()) {
                $stockTransfersQuery->where(function ($query) use ($user) {
                    $query->where('from_branch_id', $user->branch_id)
                        ->orWhere('to_branch_id', $user->branch_id);
                });
            }

            $movements = $stockInsQuery
                ->union($stockOutsQuery)
                ->union($stockTransfersQuery)
                ->orderBy('date', 'desc')
                ->get();

            return $movements->map(function ($movement) {
                if ($movement->action === 'Transfer') {
                    $transfer = StockTransfer::with([
                            'inventory.product',
                            'fromBranch',
                            'toBranch',
                            'createdBy',
                            'updatedBy'
                        ])
                        ->where([
                            'inventory_id' => $movement->product_id,
                            'date' => $movement->date,
                            'quantity' => $movement->quantity,
                            'id' => $movement->transfer_id
                        ])->first();

                    if ($transfer) {
                        $movement->product = $transfer->inventory->product;
                        $movement->fromBranch = $transfer->fromBranch;
                        $movement->toBranch = $transfer->toBranch;
                        $movement->transfer = $transfer;
                    }
                }
                return $movement;
            });

        } catch (\Exception $e) {
            \Log::error('Error in getAllInventoryMovements: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getTotalBranches($user)
    {
        if (!$user || !$user->hasRole(['Admin', 'Super Admin'])) {
            return 0;
        }

        try {
            return \App\Models\Branch::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalUsers($user)
    {
        if (!$user || !$user->hasRole(['Admin', 'Super Admin'])) {
            return 0;
        }

        try {
            return \App\Models\User::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalCategories($user)
    {
        if (!$user || !$user->hasRole(['Admin', 'Super Admin'])) {
            return 0;
        }

        try {
            return \App\Models\Category::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalVendors($user)
    {
        if (!$user || !$user->hasRole(['Admin', 'Super Admin'])) {
            return 0;
        }

        try {
            return \App\Models\Vendor::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalCustomers($user)
    {
        if (!$user || !$user->hasRole(['Admin', 'Super Admin'])) {
            return 0;
        }

        try {
            return \App\Models\Customer::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getTotalTools($user)
    {
        if (!$user || !$user->hasRole(['Admin', 'Super Admin'])) {
            return 0;
        }

        try {
            return \App\Models\Tool::count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getNearExpiryProducts($user)
    {
        if (!$user || !$user->hasRole(['Branch Manager', 'Admin', 'Super Admin'])) {
            return collect([]);
        }

        try {
            $warningDays = 90; // Configure how many days in advance to show warning
            $today = Carbon::now();
            $notificationThresholds = [90, 60, 30, 15, 7]; // Days before expiry to send notifications

            $query = StockIn::with(['product'])
                ->whereNotNull('expiration_date')
                ->whereNotNull('lot_number')
                ->whereRaw('quantity > 0')
                ->where('expiration_date', '>', $today)
                ->where('expiration_date', '<=', $today->copy()->addDays($warningDays))
                ->orderBy('expiration_date');

            if ($user->isBranchRestricted()) {
                $query->where('branch_id', $user->branch_id);
            }

            $nearExpiryProducts = $query->get();

            // Send notifications for products reaching threshold days
            foreach ($nearExpiryProducts as $stockIn) {
                $daysUntilExpiry = $today->diffInDays($stockIn->expiration_date);
                
                if (in_array($daysUntilExpiry, $notificationThresholds)) {
                    app(NotificationController::class)->sendNearExpiryNotification($stockIn);
                }
            }

            return $nearExpiryProducts->map(function ($item) use ($today) {
                $item->days_until_expiry = $today->diffInDays($item->expiration_date);
                return $item;
            });
        } catch (\Exception $e) {
            \Log::error('Error in getNearExpiryProducts: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getNearExpiryCount($user)
    {
        if (!$user || !$user->hasRole(['Branch Manager', 'Admin', 'Super Admin'])) {
            return 0;
        }

        try {
            $warningDays = 90;
            $today = Carbon::now();

            $query = StockIn::whereNotNull('expiration_date')
                ->whereNotNull('lot_number')
                ->whereRaw('quantity > 0')
                ->where('expiration_date', '>', $today)
                ->where('expiration_date', '<=', $today->copy()->addDays($warningDays));

            if ($user->isBranchRestricted()) {
                $query->where('branch_id', $user->branch_id);
            }

            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getProductMovementAnalysis($user)
    {
        if (!$user || !$user->hasRole(['Branch Manager', 'Admin', 'Super Admin'])) {
            return [
                'fast' => collect([]),
                'moderate' => collect([]),
                'slow' => collect([])
            ];
        }

        try {
            $endDate = Carbon::now();
            $startDate = Carbon::now()->subMonths(3); // Analysis period: last 3 months

            // Get stock out data grouped by product
            $query = StockOut::with(['product'])
                ->select('product_id', 
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('COUNT(*) as transaction_count'))
                ->whereBetween('date', [$startDate, $endDate])
                ->groupBy('product_id')
                ->having('total_quantity', '>', 0);

            if ($user->isBranchRestricted()) {
                $query->where('branch_id', $user->branch_id);
            }

            $stockOutData = $query->get();

            // Calculate movement thresholds
            $totalProducts = $stockOutData->count();
            if ($totalProducts === 0) {
                return [
                    'fast' => collect([]),
                    'moderate' => collect([]),
                    'slow' => collect([])
                ];
            }

            // Sort by total quantity in descending order
            $sortedData = $stockOutData->sortByDesc('total_quantity');

            // Calculate percentiles for classification
            $fastThreshold = (int)($totalProducts * 0.2); // Top 20%
            $moderateThreshold = (int)($totalProducts * 0.5); // Next 30%

            // Classify products
            $fastMoving = collect();
            $moderateMoving = collect();
            $slowMoving = collect();

            foreach ($sortedData as $index => $item) {
                $movementData = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_code' => $item->product->code,
                    'total_quantity' => $item->total_quantity,
                    'transaction_count' => $item->transaction_count,
                    'average_per_month' => round($item->total_quantity / 3, 2) // 3 months period
                ];

                if ($index < $fastThreshold) {
                    $fastMoving->push($movementData);
                } elseif ($index < $moderateThreshold) {
                    $moderateMoving->push($movementData);
                } else {
                    $slowMoving->push($movementData);
                }
            }

            return [
                'fast' => $fastMoving,
                'moderate' => $moderateMoving,
                'slow' => $slowMoving
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getProductMovementAnalysis: ' . $e->getMessage());
            return [
                'fast' => collect([]),
                'moderate' => collect([]),
                'slow' => collect([])
            ];
        }
    }

    public function allNearExpiryProducts()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $nearExpiryProducts = $this->getNearExpiryProducts($user);
        return view('near-expiry-products', compact('nearExpiryProducts'));
    }

    public function allProductMovementAnalysis()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $productMovement = $this->getProductMovementAnalysis($user);
        return view('product-movement-analysis', compact('productMovement'));
    }
}
