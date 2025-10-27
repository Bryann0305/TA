<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BillOfMaterialController;
use App\Http\Controllers\PesananProduksiController;
use App\Http\Controllers\PenjadwalanController;
use App\Http\Controllers\GagalProduksiController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\DetailPembelianController;
use App\Http\Controllers\DetailPesananProduksiController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BiayaGudangController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Inventory Section (Roles: admin, gudang, pembelian, manajer_produksi)
Route::middleware('Role:admin,gudang,pembelian,manajer_produksi')->group(function () {

    // Resource dengan create/store
    Route::resource('inventory', InventoryController::class)->names([
        'index' => 'inventory.index',
        'create' => 'inventory.create',
        'store' => 'inventory.store',
        'show' => 'inventory.show',
        'edit' => 'inventory.edit',
        'update' => 'inventory.update',
        'destroy' => 'inventory.destroy',
    ]);

    // Custom route untuk gudang
    Route::get('/inventory/gudang/{id}', [InventoryController::class, 'showGudang'])->name('inventory.showGudang');

    // Export PDF
    Route::get('/inventory/export-pdf', [InventoryController::class, 'exportPdf'])->name('inventory.exportPdf');

});

        // Production Section (Roles: admin, pembelian)
        Route::prefix('production')->name('production.')->group(function() {
            Route::get('/', [ProductionController::class, 'index'])->name('index');
            Route::get('/create', [ProductionController::class, 'create'])->name('create');
            Route::post('/store', [ProductionController::class, 'store'])->name('store');
            Route::get('/{id}', [ProductionController::class, 'show'])->name('show');
            Route::post('/{id}/complete', [ProductionController::class, 'complete'])->name('complete');
            Route::post('/{id}/approve', [ProductionController::class, 'approve'])->name('approve');
            Route::post('/{id}/update-hasil', [ProductionController::class, 'updateHasil'])->name('updateHasil');
            Route::get('/{id}/edit', [ProductionController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductionController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductionController::class, 'destroy'])->name('destroy');
            Route::get('/ajax-order/{order}', [ProductionController::class,'ajaxOrderDetails'])->name('ajaxOrder');
            Route::get('/{id}/bom-details', [ProductionController::class,'bomDetails'])->name('bomDetails');
            Route::post('/{id}/move-to-completed', [ProductionController::class,'moveToCompleted'])->name('moveToCompleted');
            Route::get('/bom-data', [ProductionController::class,'getBomData'])->name('getBomData');
            Route::get('/stock-data', [ProductionController::class,'getStockData'])->name('getStockData');
        });

        // Pelanggan Section (Roles: admin, pembelian)
        Route::middleware('Role:admin,manajer_produksi')->prefix('pelanggan')->name('pelanggan.')->group(function () {
            Route::get('/', [PelangganController::class, 'index'])->name('index');
            Route::get('/create', [PelangganController::class, 'create'])->name('create');
            Route::post('/', [PelangganController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PelangganController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PelangganController::class, 'update'])->name('update');
            Route::delete('/{id}', [PelangganController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [PelangganController::class, 'show'])->name('show');
            Route::patch('/{id}/toggle-status', [PelangganController::class, 'toggleStatus'])->name('toggle-status');
            Route::patch('/{id}/deactivate', [PelangganController::class, 'deactivate'])->name('deactivate');
        });

        // Procurement Section (Roles: admin, manajer_produksi)
        Route::prefix('procurement')->name('procurement.')->group(function () {
            Route::get('/', [ProcurementController::class, 'index'])->name('index');
            Route::get('/create', [ProcurementController::class, 'create'])->name('create');
            Route::post('/', [ProcurementController::class, 'store'])->name('store');
            Route::get('/{id}', [ProcurementController::class, 'show'])->name('show'); // Show
            Route::get('/{id}/edit', [ProcurementController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProcurementController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProcurementController::class, 'destroy'])->name('destroy');
            
            // Toggle Payment
            Route::patch('/{id}/toggle-payment', [ProcurementController::class, 'togglePayment'])->name('toggle_payment');
            
            // Update Receiving Status
            Route::patch('/{id}/update-receiving-status', [ProcurementController::class, 'updateReceivingStatus'])->name('updateReceivingStatus');
            
            // Download PDF
            Route::get('/{id}/pdf', [ProcurementController::class, 'downloadPdf'])->name('pdf');

            // Nested route untuk detail pembelian
            Route::prefix('{purchaseId}/details')->name('details.')->group(function () {
                Route::post('/', [DetailPembelianController::class, 'store'])->name('store');
                Route::patch('/{detailId}/receive', [DetailPembelianController::class, 'receive'])->name('receive');
                Route::patch('/{detailId}/toggle', [DetailPembelianController::class, 'toggleReceiving'])->name('toggle');
            });
        });

        // Supplier Section (Roles: admin, pembelian)
        Route::middleware('Role:admin,pembelian')->prefix('supplier')->name('supplier.')->group(function () {
            Route::get('/', [SupplierController::class, 'index'])->name('index');
            Route::get('/create', [SupplierController::class, 'create'])->name('create');
            Route::post('/', [SupplierController::class, 'store'])->name('store');
            Route::get('/{id}', [SupplierController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');
            Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
            Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/approve', [SupplierController::class, 'approve'])->name('approve');
            Route::patch('/{id}/deactivate', [SupplierController::class, 'deactivate'])->name('deactivate');
        });

        // Gudang Section (Roles: admin, gudang)
        Route::middleware('Role:admin,gudang')->prefix('gudang')->name('gudang.')->group(function () {
            Route::get('/', [GudangController::class, 'index'])->name('index');
            Route::get('/create', [GudangController::class, 'create'])->name('create');
            Route::post('/', [GudangController::class, 'store'])->name('store');
            Route::get('/{id}', [GudangController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [GudangController::class, 'edit'])->name('edit');
            Route::put('/{id}', [GudangController::class, 'update'])->name('update');
            Route::delete('/{id}', [GudangController::class, 'destroy'])->name('destroy');
        });

        // Category Section (Roles: admin, gudang, pembelian, manajer_produksi)
        Route::middleware('Role:admin,gudang,pembelian,manajer_produksi')->prefix('category')->name('category.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::get('/{id}', [CategoryController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        // Pesanan Produksi
        Route::prefix('pesanan-produksi')->name('pesanan_produksi.')->group(function () {
            Route::get('/', [PesananProduksiController::class, 'index'])->name('index');
            Route::get('/create', [PesananProduksiController::class, 'create'])->name('create');
            Route::post('/', [PesananProduksiController::class, 'store'])->name('store');
            Route::get('/{id}', [PesananProduksiController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [PesananProduksiController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PesananProduksiController::class, 'update'])->name('update');
            Route::delete('/{id}', [PesananProduksiController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [PesananProduksiController::class, 'toggleStatus'])->name('toggle_status');
        });

        // Production Order (Surat Perintah Produksi)
        Route::middleware('auth', 'Role:admin,manajer_produksi')
        ->prefix('production_order')
        ->name('production_order.')
        ->group(function () {
            Route::get('/', [ProductionOrderController::class, 'index'])->name('index');
            Route::get('/create', [ProductionOrderController::class, 'create'])->name('create');
            Route::post('/', [ProductionOrderController::class, 'store'])->name('store');
            Route::get('/{id}', [ProductionOrderController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ProductionOrderController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ProductionOrderController::class, 'update'])->name('update');
            Route::delete('/{id}', [ProductionOrderController::class, 'destroy'])->name('destroy');

            // Approve / ubah status
            Route::patch('/{id}/approve', [ProductionOrderController::class, 'approve'])->name('approve');
            Route::patch('/{id}/update-status', [ProductionOrderController::class, 'updateStatus'])->name('update_status');
        });

        Route::prefix('bom')->name('bom.')->group(function() {
        Route::get('/', [BillOfMaterialController::class, 'index'])->name('index');
        Route::get('/create', [BillOfMaterialController::class, 'create'])->name('create');
        Route::post('/', [BillOfMaterialController::class, 'store'])->name('store');
        Route::get('/{id}', [BillOfMaterialController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BillOfMaterialController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BillOfMaterialController::class, 'update'])->name('update');
        Route::delete('/{id}', [BillOfMaterialController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/approve', [BillOfMaterialController::class, 'approve'])->name('approve');
        });

        // Penjadwalan Produksi
        Route::prefix('penjadwalan')->name('penjadwalan.')->group(function () {
            Route::get('/', [PenjadwalanController::class, 'index'])->name('index');
            Route::get('/create', [PenjadwalanController::class, 'create'])->name('create');
            Route::post('/', [PenjadwalanController::class, 'store'])->name('store');
            Route::get('/{id}', [PenjadwalanController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [PenjadwalanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PenjadwalanController::class, 'update'])->name('update');
            Route::delete('/{id}', [PenjadwalanController::class, 'destroy'])->name('destroy');
        });

        // Biaya Gudang
        Route::middleware('Role:admin,gudang,manajer_produksi')->group(function () {
            Route::get('/biaya-gudang', [BiayaGudangController::class, 'index'])->name('biaya-gudang.index');
            Route::get('/biaya-gudang/create', [BiayaGudangController::class, 'create'])->name('biaya-gudang.create');
            Route::post('/biaya-gudang', [BiayaGudangController::class, 'store'])->name('biaya-gudang.store');
            Route::get('/biaya-gudang/{id}', [BiayaGudangController::class, 'show'])->name('biaya-gudang.show');
            Route::get('/biaya-gudang/{id}/edit', [BiayaGudangController::class, 'edit'])->name('biaya-gudang.edit');
            Route::put('/biaya-gudang/{id}', [BiayaGudangController::class, 'update'])->name('biaya-gudang.update');
            Route::delete('/biaya-gudang/{id}', [BiayaGudangController::class, 'destroy'])->name('biaya-gudang.destroy');
        });

        // Reports & Settings
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/{type}', [ReportsController::class, 'export'])->name('reports.export');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::middleware('Role:admin,pembelian,gudang,manajer_produksi')->post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');
    });