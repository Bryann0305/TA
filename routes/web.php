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

    // Inventory Section (Roles: admin, gudang)
    Route::middleware('Role:admin,gudang,pembelian,manajer_produksi')->group(function () {
        Route::resource('inventory', InventoryController::class)->names([
            'index' => 'inventory.index',
            'create' => 'inventory.create',
            'store' => 'inventory.store',
            'show' => 'inventory.show',
            'edit' => 'inventory.edit',
            'update' => 'inventory.update',
            'destroy' => 'inventory.destroy',
        ]);
        Route::get('/inventory/export-pdf', [InventoryController::class, 'exportPdf'])->name('inventory.exportPdf');
    });

    // Production Section (Roles: admin, pembelian)
Route::prefix('production')->group(function(){
    Route::get('/', [ProductionController::class,'index'])->name('production.index');
    Route::get('/create', [ProductionController::class,'create'])->name('production.create');
    Route::post('/store', [ProductionController::class,'store'])->name('production.store');
    Route::get('/{id}', [ProductionController::class,'show'])->name('production.show');
    Route::post('/{id}/complete', [ProductionController::class,'complete'])->name('production.complete');
    Route::get('production/{id}/approve', [ProductionController::class, 'approve'])->name('production.approve');
    Route::post('/{id}/update-hasil', [ProductionController::class, 'updateHasil'])->name('produksi.updateHasil');
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
        
        // Tambahkan route untuk toggle payment
        Route::patch('/{id}/toggle-payment', [ProcurementController::class, 'togglePayment'])->name('toggle_payment');
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

    /// Production Order (Surat Perintah Produksi)
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

    // ✅ cukup 'approve'
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

    // Reports & Settings
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::middleware('Role:admin,pembelian,gudang,manajer_produksi')->post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');
});
