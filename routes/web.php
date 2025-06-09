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
    // Inventory Section (Roles: inventory, admin)
Route::middleware('Role:admin,gudang')->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/export-pdf', [InventoryController::class, 'exportPdf'])->name('inventory.exportPdf');
});


    // Production Section (Roles: production, admin)
        Route::middleware('Role:admin,pembelian')->group(function () {
        Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    });

    
    Route::middleware('Role:admin,manajer_produksi')->group(function () {
        // Procurement main routes
        Route::get('/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
        Route::get('/procurement/create', [ProcurementController::class, 'create'])->name('procurement.create_purchaseOrder');
        Route::post('/procurement', [ProcurementController::class, 'store'])->name('procurement.store');
    
        // Supplier routes - lengkap CRUD
        Route::get('/procurement/supplier', [SupplierController::class, 'index'])->name('procurement.supplier');
        Route::get('/procurement/supplier/create', [SupplierController::class, 'create'])->name('procurement.create_supplier');
        Route::post('/procurement/supplier', [SupplierController::class, 'store'])->name('procurement.store_supplier');
        Route::get('/procurement/supplier/{id}/edit', [SupplierController::class, 'edit'])->name('procurement.edit_supplier');
        Route::put('/procurement/supplier/{id}', [SupplierController::class, 'update'])->name('procurement.update_supplier');
        Route::delete('/procurement/supplier/{id}', [SupplierController::class, 'destroy'])->name('procurement.destroy_supplier');
        Route::get('/procurement/supplier/{id}', [SupplierController::class, 'show'])->name('procurement.show');

        // Bill of Materials routes
        Route::get('/bill-of-materials', [BillOfMaterialController::class, 'index'])->name('bill-of-materials.index');
        Route::get('/bill-of-materials/create', [BillOfMaterialController::class, 'create'])->name('bill-of-materials.create');
        Route::post('/bill-of-materials', [BillOfMaterialController::class, 'store'])->name('bill-of-materials.store');
        Route::get('/bill-of-materials/{id}', [BillOfMaterialController::class, 'show'])->name('bill-of-materials.show');
        Route::get('/bill-of-materials/{id}/edit', [BillOfMaterialController::class, 'edit'])->name('bill-of-materials.edit');
        Route::put('/bill-of-materials/{id}', [BillOfMaterialController::class, 'update'])->name('bill-of-materials.update');
        Route::delete('/bill-of-materials/{id}', [BillOfMaterialController::class, 'destroy'])->name('bill-of-materials.destroy');
        Route::post('/bill-of-materials/{id}/status', [BillOfMaterialController::class, 'updateStatus'])->name('bill-of-materials.status');
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

