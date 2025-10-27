<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barang;
use App\Models\Produksi;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== Summary Cards =====
        $totalBarang = Barang::count();
        $bahanBakuCount = Barang::where('Jenis', 'Bahan Baku')->count();
        $produkJadiCount = Barang::where('Jenis', 'Produk Jadi')->count();

        $producedThisMonth = Produksi::whereMonth('Tanggal_Produksi', now()->month)
            ->whereYear('Tanggal_Produksi', now()->year)
            ->sum('Jumlah_Berhasil');

        // ===== Top HPP Materials =====
        $topHpp = DB::table('barang')
            ->select('Nama_Bahan', 'HPP', 'Stok', 'Safety_Stock')
            ->whereNotNull('HPP')
            ->orderByDesc('HPP')
            ->limit(7)
            ->get();

        // ===== Low Stock Alerts =====
        $lowStock = DB::table('barang')
            ->select('Id_Bahan', 'Nama_Bahan', 'Stok', 'Safety_Stock')
            ->whereColumn('Stok', '<', 'Safety_Stock')
            ->orderBy('Stok')
            ->limit(10)
            ->get();

        // ===== Recent Procurements =====
        $recentPurchases = DB::table('detail_pembelian as dp')
            ->join('pembelian as p', 'dp.pembelian_Id_Pembelian', '=', 'p.Id_Pembelian')
            ->join('barang as b', 'dp.bahan_baku_Id_Bahan', '=', 'b.Id_Bahan')
            ->select(
                'p.Id_Pembelian',
                'p.Tanggal_Pemesanan',
                'b.Nama_Bahan',
                'dp.Jumlah',
                'dp.Harga_Keseluruhan'
            )
            ->orderByDesc('p.Tanggal_Pemesanan')
            ->limit(10)
            ->get();

        // ===== Kirim ke View =====
        return view('dashboard', compact(
            'totalBarang',
            'bahanBakuCount',
            'produkJadiCount',
            'producedThisMonth',
            'topHpp',
            'lowStock',
            'recentPurchases'
        ));
    }
}
