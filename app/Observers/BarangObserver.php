<?php

namespace App\Observers;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;

class BarangObserver
{
    /**
     * Jalankan setiap kali data Barang disimpan (create/update)
     */
    public function saved(Barang $barang)
    {
        $this->calculateEOQandROP($barang);
    }

    /**
     * Fungsi untuk menghitung EOQ, ROP, dan Safety Stock
     */
    private function calculateEOQandROP(Barang $b)
    {
        $tahun = date('Y');

        // === 1. Permintaan Tahunan ===
        $permintaanProduksi = DB::table('detail_pesanan_produksi')
            ->where('barang_Id_Bahan', $b->Id_Bahan)
            ->whereYear('created_at', $tahun)
            ->sum('Jumlah');

        $permintaanLangsung = DB::table('detail_pembelian as dp')
            ->join('pembelian as p', 'dp.pembelian_Id_Pembelian', '=', 'p.Id_Pembelian')
            ->where('dp.bahan_baku_Id_Bahan', $b->Id_Bahan)
            ->whereYear('p.Tanggal_Pemesanan', $tahun)
            ->sum('dp.Jumlah');

        $D = $permintaanProduksi + $permintaanLangsung; // total permintaan tahunan

        // === 2. Biaya Pemesanan (S) ===
        $S = DB::table('pembelian')
            ->whereYear('Tanggal_Pemesanan', $tahun)
            ->avg('Biaya_Pengiriman') ?? 20000;

        // === 3. Biaya Simpan (H) ===
        $biayaGudangTahun = DB::table('biaya_gudang')
            ->whereYear('tanggal_biaya', $tahun)
            ->sum(DB::raw('biaya_sewa + biaya_listrik + biaya_air'));

        $stokRataRata = DB::table('barang')->avg('Stok') ?: 1;
        $H = $biayaGudangTahun / $stokRataRata;

        // === 4. Hitung EOQ ===
        $EOQ = ($H > 0) ? sqrt((2 * $D * $S) / $H) : 0;

        // === 5. Lead Time (hari) ===
        $L = DB::table('pembelian')
            ->selectRaw('AVG(DATEDIFF(Tanggal_Kedatangan, Tanggal_Pemesanan)) as lead')
            ->value('lead') ?? 7;

        // === 6. Permintaan Harian ===
        $permintaanHarian = $D / 365;

        // === 7. Safety Stock (SS) ===
        $dataPermintaan = DB::table('detail_pesanan_produksi')
            ->selectRaw('DATE(created_at) as tanggal, SUM(Jumlah) as total')
            ->where('barang_Id_Bahan', $b->Id_Bahan)
            ->whereYear('created_at', $tahun)
            ->groupBy('tanggal')
            ->pluck('total');

        $avgDaily = $dataPermintaan->avg() ?? $permintaanHarian;
        $maxDaily = $dataPermintaan->max() ?? $avgDaily;
        $SS = ($maxDaily - $avgDaily) * $L;

        // === 8. Reorder Point (ROP) ===
        $ROP = ($permintaanHarian * $L) + $SS;

        // === 9. Simpan hasil ke tabel barang ===
        DB::table('barang')
            ->where('Id_Bahan', $b->Id_Bahan)
            ->update([
                'EOQ' => round($EOQ),
                'ROP' => round($ROP),
                'Safety_Stock' => round($SS),
            ]);
    }
}
