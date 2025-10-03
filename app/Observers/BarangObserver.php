<?php

namespace App\Observers;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class BarangObserver
{
    public function saved(Barang $barang)
    {
        $this->calculateEOQandROP($barang);
    }

    private function calculateEOQandROP(Barang $b)
    {
        $tahun = date('Y');
        // Permintaan dari produksi (hanya tahun berjalan)
        $permintaanProduksi = DB::table('detail_pesanan_produksi')
            ->where('barang_Id_Bahan', $b->Id_Bahan)
            ->whereYear('created_at', $tahun)
            ->sum('Jumlah');

        // Permintaan dari pembelian (hanya tahun berjalan, join ke pembelian)
        $permintaanLangsung = DB::table('detail_pembelian as dp')
            ->join('pembelian as p', 'dp.pembelian_Id_Pembelian', '=', 'p.Id_Pembelian')
            ->where('dp.bahan_baku_Id_Bahan', $b->Id_Bahan)
            ->whereYear('p.Tanggal_Pemesanan', $tahun)
            ->sum('dp.Jumlah');

        $D = $permintaanProduksi + $permintaanLangsung; // demand tahunan

            // Biaya pemesanan per order: rata-rata input user (ongkos kirim) tahun berjalan
            $tahun = date('Y');
            $S = DB::table('pembelian')
                ->whereYear('Tanggal_Pemesanan', $tahun)
                ->avg('Biaya_Pengiriman') ?? 20000; // default jika belum ada data

        // Ambil total biaya gudang tahun berjalan
        $tahun = date('Y');
        $biayaGudangTahun = DB::table('biaya_gudang')
            ->whereYear('tanggal_biaya', $tahun)
            ->sum(DB::raw('biaya_sewa + biaya_listrik + biaya_air'));

        // Hitung biaya simpan per unit per tahun 
        $stokRataRata = DB::table('barang')->avg('Stok') ?: 1;
        $H = $biayaGudangTahun / $stokRataRata;

        // Hitung EOQ
        $EOQ = ($H > 0) ? sqrt((2 * $D * $S) / $H) : 0;

        // Lead time (ambil rata-rata dari tabel pembelian)
        $L = DB::table('pembelian')
            ->selectRaw('AVG(DATEDIFF(Tanggal_Kedatangan, Tanggal_Pemesanan)) as lead')
            ->value('lead') ?? 7;

        $permintaanHarian = $D / 365;
        $ROP = $permintaanHarian * $L;

        // ✅ Update langsung ke tabel barang
        DB::table('barang')
            ->where('Id_Bahan', $b->Id_Bahan)
            ->update([
                'EOQ' => round($EOQ),
                'ROP' => round($ROP),
            ]);
    }
}
