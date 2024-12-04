<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReceivingReportController;
use App\Http\Controllers\ForPhssController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::resource('users', UserController::class);

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('inventories', InventoryController::class);
    Route::resource('stock-ins', StockInController::class);
    Route::resource('stock-outs', StockOutController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('branches', BranchController::class);
    Route::resource('tools', ToolController::class);
    Route::post('/products/scan', [ProductController::class, 'scanBarcode'])->name('products.scan');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
    Route::get('/reports/{report}/pdf', [ReportController::class, 'generatePdf'])->name('reports.pdf');

    // Stock In Routes
    Route::prefix('stock-ins')->group(function () {
        Route::get('/', [StockInController::class, 'index'])->name('stock_ins.index');
        Route::get('/create', [StockInController::class, 'create'])->name('stock_ins.create');
        Route::post('/', [StockInController::class, 'store'])->name('stock_ins.store');
        Route::get('/{stockIn}', [StockInController::class, 'show'])->name('stock_ins.show');
    });

    // Stock Out Routes
    Route::prefix('stock-outs')->group(function () {
        Route::get('/', [StockOutController::class, 'index'])->name('stock_outs.index');
        Route::get('/create', [StockOutController::class, 'create'])->name('stock_outs.create');
        Route::post('/', [StockOutController::class, 'store'])->name('stock_outs.store');
        Route::get('/{stockOut}', [StockOutController::class, 'show'])->name('stock_outs.show');
    });

    Route::get('/global-search', [GlobalSearchController::class, 'search'])->name('global.search');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/inventory-movements', [App\Http\Controllers\HomeController::class, 'allInventoryMovements'])->name('inventory.movements');

    Route::get('/barcodes', [BarcodeController::class, 'index'])->name('barcodes.index');

        // List all transfers
        Route::get('/stock_transfers', [StockTransferController::class, 'index'])
            ->name('stock_transfers.index');

        // Show create form
        Route::get('/stock_transfers/create', [StockTransferController::class, 'create'])
            ->name('stock_transfers.create');

        // Store new transfer
        Route::post('/stock_transfers', [StockTransferController::class, 'store'])
            ->name('stock_transfers.store');

        // Show transfer details
        Route::get('/stock_transfers/{stockTransfer}', [StockTransferController::class, 'show'])
            ->name('stock_transfers.show');

        // Delete transfer (though you might want to remove this if transfers should be permanent)
        Route::delete('/stock_transfers/{stockTransfer}', [StockTransferController::class, 'destroy'])
            ->name('stock_transfers.destroy');

    Route::put('/stock-transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])->name('stock_transfers.approve');
    Route::put('/stock-transfers/{stockTransfer}/reject', [StockTransferController::class, 'reject'])->name('stock_transfers.reject');

    // Fallback route
    Route::fallback(function () {
            return view('errors.404');
        });

    Route::get('/register', function () {
        abort(500, 'Registration is currently disabled.');
    })->name('register');

    // Route::post('notifications/{notification}/mark-as-read', function ($notification) {
    //     auth()->user()->notifications()->findOrFail($notification)->markAsRead();
    //     return response()->json(['success' => true]);
    // })->name('notifications.mark-as-read');

    // Route::post('notifications/mark-as-read/{notification}', 'NotificationController@markAsRead')
    //     ->name('notifications.mark-as-read');
    // Route::post('notifications/mark-all-read', 'NotificationController@markAllAsRead')
    //     ->name('notifications.mark-all-read');

    // Notification routes
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.mark-as-read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])
        ->name('notifications.mark-all-read');
    Route::get('notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');

    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');

    Route::resource('receiving-reports', ReceivingReportController::class);

    Route::get('/near-expiry-products', [HomeController::class, 'allNearExpiryProducts'])->name('near.expiry.products');
    Route::get('/product-movement-analysis', [HomeController::class, 'allProductMovementAnalysis'])->name('product.movement.analysis');

    // PHSS Routes
    Route::resource('for-phss', ForPhssController::class);

});

Auth::routes();
