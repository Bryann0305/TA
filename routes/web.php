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

/*
|--------------------------------------------------------------------------
| Guest Routes (Login & Register)
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
    

    // Inventory Section (Roles: inventory, admin)
    Route::middleware('Role:admin,gudang')->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/export-pdf', [InventoryController::class, 'exportPdf'])->name('inventory.exportPdf');
        Route::get('/inventory/{id}', [InventoryController::class, 'show'])->name('inventory.show');
        Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{id}', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });


    // Production Section (Roles: production, admin)
        Route::middleware('Role:admin,pembelian')->group(function () {
        Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    });

    
    Route::middleware('Role:admin,manajer_produksi')->group(function () {
        // Procurement main routes - supplier 
        Route::get('/procurement/supplier', [SupplierController::class, 'index'])->name('procurement.supplier');
        Route::get('/procurement/supplier/create', [SupplierController::class, 'create'])->name('procurement.create_supplier');
        Route::post('/procurement/supplier', [SupplierController::class, 'store'])->name('procurement.store_supplier');
        Route::get('/procurement/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('procurement.edit_supplier');
        Route::put('/procurement/supplier/{id}', [SupplierController::class, 'update'])->name('procurement.update_supplier');
        Route::delete('/procurement/supplier/{id}', [SupplierController::class, 'destroy'])->name('procurement.destroy_supplier');
        Route::get('/procurement/supplier/{id}', [SupplierController::class, 'show'])->name('procurement.show_supplier');

    
        // PO routes - lengkap CRUD
        Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
        Route::get('/procurement/create', [ProcurementController::class, 'create'])->name('procurement.create_purchaseOrder');
        Route::post('/procurement', [ProcurementController::class, 'store'])->name('procurement.store');
        Route::get('/procurement/{id}', [ProcurementController::class, 'show'])->name('procurement.show_po');
        Route::get('/procurement/{id}/edit', [ProcurementController::class, 'edit'])->name('procurement.edit_purchaseOrder');
        Route::put('/procurement/{id}', [ProcurementController::class, 'update'])->name('procurement.update_purchaseOrder');
        Route::delete('/procurement/{id}', [ProcurementController::class, 'destroy'])->name('procurement.destroy_purchaseOrder');

        //Pesanan Produksi Routes
    Route::prefix('pesanan-produksi')->name('pesanan-produksi.')->group(function () {
        Route::get('/', [PesananProduksiController::class, 'index'])->name('index');
        Route::get('/create', [PesananProduksiController::class, 'create'])->name('create');
        Route::post('/', [PesananProduksiController::class, 'store'])->name('store');
        Route::get('/{id}', [PesananProduksiController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PesananProduksiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PesananProduksiController::class, 'update'])->name('update');
        Route::delete('/{id}', [PesananProduksiController::class, 'destroy'])->name('destroy');
    });

    //Penjadwalan Produksi Routes
    Route::prefix('penjadwalan')->name('penjadwalan.')->group(function () {
        Route::get('/', [PenjadwalanController::class, 'index'])->name('index');
        Route::get('/create', [PenjadwalanController::class, 'create'])->name('create');
        Route::post('/', [PenjadwalanController::class, 'store'])->name('store');
        Route::get('/{id}', [PenjadwalanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PenjadwalanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PenjadwalanController::class, 'update'])->name('update');
        Route::delete('/{id}', [PenjadwalanController::class, 'destroy'])->name('destroy');
    });


    // Produksi Routes
    Route::prefix('production')->group(function () {
        Route::get('/', [ProductionController::class, 'index'])->name('production.index');
        Route::get('/create', [ProductionController::class, 'create'])->name('production.create');
        Route::post('/', [ProductionController::class, 'store'])->name('production.store');
        Route::get('/{id}/edit', [ProductionController::class, 'edit'])->name('production.edit');
        Route::put('/{id}', [ProductionController::class, 'update'])->name('production.update');
        Route::put('/{id}/status', [ProductionController::class, 'updateStatus'])->name('production.update-status');
        Route::get('/{id}', [ProductionController::class, 'show'])->name('production.show');
        Route::get('/bill-of-materials/{id}', [BillOfMaterialController::class, 'show'])->name('bill-of-materials.show');
    });

    Route::resource('bill-of-materials', BillOfMaterialController::class);

        Route::prefix('produksi-gagal')->name('produksi-gagal.')->group(function () {
        Route::get('/', [GagalProduksiController::class, 'index'])->name('index');
        Route::post('/', [GagalProduksiController::class, 'store'])->name('store');
        });
    });
    
        
    // Reports (accessible by all authenticated users)
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    // Settings (Admin only)
    Route::middleware('Role:admin')->group(function () {
        
        Route::post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');
    });

   
});

/*
|--------------------------------------------------------------------------
| Legacy Laravel Auth Route
|--------------------------------------------------------------------------
*/