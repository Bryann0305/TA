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

    // Pelanggan Section (Roles: admin, pembelian)
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

    // Supplier Section (Roles: admin, manajer_produksi)
    Route::middleware('Role:admin,manajer_produksi')
        ->prefix('supplier')
        ->name('supplier.')
        ->group(function () {

            // CRUD dasar
            Route::get('/', [SupplierController::class, 'index'])->name('index');     // List supplier
            Route::get('/create', [SupplierController::class, 'create'])->name('create'); // Form tambah
            Route::post('/', [SupplierController::class, 'store'])->name('store');        // Simpan data baru
            Route::get('/{id}', [SupplierController::class, 'show'])->name('show');       // Detail supplier
            Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');  // Form edit
            Route::put('/{id}', [SupplierController::class, 'update'])->name('update');   // Update supplier
            Route::delete('/{id}', [SupplierController::class, 'destroy'])->name('destroy'); // Hapus supplier

            // Status actions
            Route::patch('/{id}/approve', [SupplierController::class, 'approve'])->name('approve');      // dari Pending → Aktif
            Route::patch('/{id}/deactivate', [SupplierController::class, 'deactivate'])->name('deactivate'); // dari Aktif → Non Aktif (pakai alasan)
    });

   // Procurement Section (Roles: admin, manajer_produksi)
      Route::middleware('Role:admin,manajer_produksi')->prefix('procurement')->name('procurement.')->group(function () {
        Route::resource('/', ProcurementController::class)->parameters([
        '' => 'id',
    ])->names([
        'index'   => 'index',
        'create'  => 'create',
        'store'   => 'store',
        'show'    => 'show',
        'edit'    => 'edit',
        'update'  => 'update',
        'destroy' => 'destroy',
    ]);
    Route::get('{pembelian}/detail/create', [DetailPembelianController::class, 'create'])
        ->name('detail-pembelian.create');
    Route::post('{pembelian}/detail', [DetailPembelianController::class, 'store'])
        ->name('detail-pembelian.store');
    Route::patch('{id}/toggle-status', [ProcurementController::class, 'toggleStatus'])->name('toggle_status');
    Route::patch('{id}/toggle-payment', [ProcurementController::class, 'togglePayment'])->name('toggle_payment');
});


    // Gudang Section (Roles: admin, pembelian)
    Route::middleware('Role:admin,pembelian')
    ->prefix('gudang')
    ->name('gudang.')
    ->group(function () {
        Route::get('/', [GudangController::class, 'index'])->name('index');
        Route::get('/create', [GudangController::class, 'create'])->name('create');
        Route::post('/', [GudangController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [GudangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [GudangController::class, 'update'])->name('update');
        Route::delete('/{id}', [GudangController::class, 'destroy'])->name('destroy');
    });

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

        // Pelanggan Section (Roles: admin, pembelian)
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



        // BOM (accessible by admin only)
        Route::middleware('Role:admin')->resource('bill-of-materials', BillOfMaterialController::class);

        // Reports & Settings
        Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::middleware('Role:admin')->post('/settings/update', [SettingsController::class, 'update'])->name('settings.update');

    });