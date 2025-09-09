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
        // Hitung permintaan tahunan (dari produksi & pembelian)
        $permintaanProduksi = DB::table('detail_pesanan_produksi')
            ->where('barang_Id_Bahan', $b->Id_Bahan)
            ->sum('Jumlah');

        $permintaanLangsung = DB::table('detail_pembelian')
            ->where('bahan_baku_Id_Bahan', $b->Id_Bahan)
            ->sum('Jumlah');

        $D = $permintaanProduksi + $permintaanLangsung; // demand

        // Ambil biaya pemesanan rata-rata
        $S = DB::table('pembelian')->avg('Total_Biaya') ?? 50000;

        // Ambil harga per unit terakhir
        $hargaSatuan = DB::table('detail_pembelian')
            ->where('bahan_baku_Id_Bahan', $b->Id_Bahan)
            ->orderBy('Id_Detail', 'desc')
            ->value(DB::raw('Harga_Keseluruhan / NULLIF(Jumlah,0)')) ?? 10000;

        // Biaya simpan (misal 10% dari harga)
        $H = $hargaSatuan * 0.1;

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
