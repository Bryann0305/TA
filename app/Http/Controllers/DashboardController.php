<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Produksi;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Summary Cards =====
        $stokBahanBaku  = Barang::where('Jenis', 'Bahan_Baku')->sum('Stok');
        $stokProdukJadi = Barang::where('Jenis', 'Produk')->sum('Stok');
        $totalItems     = $stokBahanBaku + $stokProdukJadi;

        $produksiCurrent = Produksi::whereMonth('Tanggal_Produksi', now()->month)
            ->whereYear('Tanggal_Produksi', now()->year)
            ->sum('Jumlah_Berhasil');

        // ===== Production Trend (Line Chart) =====
        $produksiTrend = Produksi::selectRaw('DATE(Tanggal_Produksi) as tgl, SUM(Jumlah_Berhasil) as total')
            ->where('Status', 'Selesai') // optional, hanya produksi selesai
            ->whereMonth('Tanggal_Produksi', now()->month)
            ->whereYear('Tanggal_Produksi', now()->year)
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();

        $chartLabels = $produksiTrend->isEmpty() 
            ? [now()->format('Y-m-d')] 
            : $produksiTrend->pluck('tgl');
        $chartData = $produksiTrend->isEmpty() 
            ? [0] 
            : $produksiTrend->pluck('total');

        // ===== Inventory Levels (Bar Chart) =====
        $barangLevels = Barang::all();
        $invLabels = $barangLevels->isEmpty() 
            ? ['No Data'] 
            : $barangLevels->pluck('Nama_Bahan');
        $invData = $barangLevels->isEmpty() 
            ? [0] 
            : $barangLevels->pluck('Stok');

        // ===== Reorder Alerts =====
        $barangReorder = Barang::whereNotNull('ROP')->get();

        return view('dashboard', compact(
            'stokBahanBaku',
            'stokProdukJadi',
            'totalItems',
            'produksiCurrent',
            'chartLabels',
            'chartData',
            'invLabels',
            'invData',
            'barangReorder'
        ));
    }
}
