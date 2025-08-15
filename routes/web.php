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
/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', fn () => redirect()->route('login'));
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Inventory Section (Roles: admin, gudang)
    Route::middleware('Role:admin,gudang')->group(function () {
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

    // Produksi Section (Roles: admin, pembelian)
    Route::middleware('Role:admin,pembelian')->prefix('production')->name('production.')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])->name('index');
        Route::get('/create', [ProductionController::class, 'create'])->name('create');
        Route::post('/', [ProductionController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProductionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProductionController::class, 'update'])->name('update');
        Route::put('/{id}/status', [ProductionController::class, 'updateStatus'])->name('update-status');
        Route::get('/{id}', [ProductionController::class, 'show'])->name('show');
        Route::get('/bill-of-materials/{id}', [BillOfMaterialController::class, 'show'])->name('bill-of-materials.show');
    });

    Route::middleware('Role:admin,pembelian')
    ->prefix('pelanggan')
    ->name('pelanggan.')
    ->group(function () {
        Route::get('/', [PelangganController::class, 'index'])->name('index');
        Route::get('/create', [PelangganController::class, 'create'])->name('create');
        Route::post('/', [PelangganController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PelangganController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PelangganController::class, 'update'])->name('update');
        Route::put('/{id}/status', [PelangganController::class, 'updateStatus'])->name('update-status');
        Route::get('/{id}', [PelangganController::class, 'show'])->name('show');
    });


    // Produksi Gagal Routes
    Route::middleware('Role:admin,pembelian')->prefix('produksi-gagal')->name('produksi-gagal.')->group(function () {
        Route::get('/', [GagalProduksiController::class, 'index'])->name('index');
        Route::post('/', [GagalProduksiController::class, 'store'])->name('store');
    });

    // Procurement Section (Roles: admin, manajer_produksi)
    Route::middleware('Role:admin,manajer_produksi')->group(function () {
        // Supplier routes
        Route::resource('procurement/supplier', SupplierController::class)->parameters([
            'supplier' => 'id',
        ])->names([
            'index' => 'procurement.supplier',
            'create' => 'procurement.create_supplier',
            'store' => 'procurement.store_supplier',
            'edit' => 'procurement.edit_supplier',
            'update' => 'procurement.update_supplier',
            'destroy' => 'procurement.destroy_supplier',
            'show' => 'procurement.show_supplier',
        ]);
        // Tambahkan route toggle status supplier
        Route::patch('procurement/supplier/{id}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('procurement.toggle_status_supplier');

        // PO routes
        Route::resource('procurement', ProcurementController::class)->names([
            'index' => 'procurement.index',
            'create' => 'procurement.create_purchaseOrder',
            'store' => 'procurement.store',
            'show' => 'procurement.show_po',
            'edit' => 'procurement.edit_purchaseOrder',
            'update' => 'procurement.update_purchaseOrder',
            'destroy' => 'procurement.destroy_purchaseOrder',
        ]);
        // Tambahkan route PATCH untuk toggle status dan pembayaran
        Route::patch('procurement/{id}/toggle-status', [ProcurementController::class, 'toggleStatus'])->name('procurement.toggle_status');
        Route::patch('procurement/{id}/toggle-payment', [ProcurementController::class, 'togglePayment'])->name('procurement.toggle_payment');

        // Pesanan Produksi
        Route::prefix('pesanan_produksi')->name('pesanan_produksi.')->group(function () {
            Route::get('/', [PesananProduksiController::class, 'index'])->name('index');
            Route::get('/create', [PesananProduksiController::class, 'create'])->name('create');
            Route::post('/', [PesananProduksiController::class, 'store'])->name('store');
            Route::get('/{id}', [PesananProduksiController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [PesananProduksiController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PesananProduksiController::class, 'update'])->name('update');
            Route::delete('/{id}', [PesananProduksiController::class, 'destroy'])->name('destroy');
            // Tambahkan route PATCH untuk toggle status
            Route::patch('/{id}/toggle-status', [PesananProduksiController::class, 'toggleStatus'])->name('toggle_status');
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

        Route::middleware('Role:admin,pembelian')
        ->prefix('pelanggan')
        ->name('pelanggan.')
        ->group(function () {
            Route::get('/', [PelangganController::class, 'index'])->name('index');
            Route::get('/create', [PelangganController::class, 'create'])->name('create');
            Route::post('/', [PelangganController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [PelangganController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PelangganController::class, 'update'])->name('update');
            Route::delete('/{id}', [PelangganController::class, 'destroy'])->name('destroy');
            // Route PATCH untuk toggle status pelanggan
            Route::patch('/{id}/toggle-status', [PelangganController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/{id}', [PelangganController::class, 'show'])->name('show');
        });

    });

    // BOM (accessible by admin only)
    Route::middleware('Role:admin')->resource('bill-of-materials', BillOfMaterialController::class);

    // Reports & Settings
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::middleware('Role:admin')->post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');
});