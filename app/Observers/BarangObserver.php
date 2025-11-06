<?php

namespace App\Observers;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BarangObserver
{
    /**
     * Event otomatis setelah Barang disimpan (insert/update)
     */
    public function saved(Barang $barang)
    {
        $this->calculateEOQandROP($barang);
    }

    /**
     * Hitung EOQ, ROP, Safety Stock, dan HPP untuk Barang
     */
    private function calculateEOQandROP(Barang $b)
    {
        $tahun = date('Y');

        // Nilai default
        $EOQ = 0;
        $ROP = 0;
        $SS  = 0;
        $HPP = null;

        // Proses hanya untuk bahan baku
        if (strtolower($b->Jenis) === 'bahan_baku') {

            // 1️⃣ Permintaan Tahunan (D)
            $permintaanProduksi = DB::table('detail_pesanan_produksi')
                ->where('barang_Id_Bahan', $b->Id_Bahan)
                ->whereYear('created_at', $tahun)
                ->sum('Jumlah') ?: 0;

            $permintaanLangsung = DB::table('detail_pembelian as dp')
                ->join('pembelian as p', 'dp.pembelian_Id_Pembelian', '=', 'p.Id_Pembelian')
                ->where('dp.bahan_baku_Id_Bahan', $b->Id_Bahan)
                ->whereYear('p.Tanggal_Pemesanan', $tahun)
                ->sum('dp.Jumlah') ?: 0;

            $D = $permintaanProduksi + $permintaanLangsung;

            // 2️⃣ Biaya Pemesanan (S)
            $S = DB::table('pembelian')
                ->whereYear('Tanggal_Pemesanan', $tahun)
                ->avg('Biaya_Pengiriman') ?: 20000;

            // 3️⃣ Biaya Simpan (H)
            $biayaGudangTahun = DB::table('biaya_gudang')
                ->whereYear('tanggal_biaya', $tahun)
                ->sum(DB::raw('COALESCE(biaya_sewa,0) + COALESCE(biaya_listrik,0) + COALESCE(biaya_air,0)')) ?: 1;

            $stokRataRata = DB::table('barang')->avg('Stok') ?: 1;
            $H = $stokRataRata > 0 ? ($biayaGudangTahun / $stokRataRata) : 1;

            // 4️⃣ Hitung EOQ
            $EOQ = sqrt((2 * $D * $S) / $H);

            // 5️⃣ Lead Time (hari)
            $L = DB::table('pembelian')
                ->selectRaw('AVG(DATEDIFF(Tanggal_Kedatangan, Tanggal_Pemesanan)) as lead')
                ->value('lead') ?: 7;

            // 6️⃣ Permintaan Harian
            $permintaanHarian = $D / 365;

            // 7️⃣ Safety Stock (SS)
            $dataPermintaan = DB::table('detail_pesanan_produksi')
                ->selectRaw('DATE(created_at) as tanggal, SUM(Jumlah) as total')
                ->where('barang_Id_Bahan', $b->Id_Bahan)
                ->whereYear('created_at', $tahun)
                ->groupBy('tanggal')
                ->pluck('total');

            $avgDaily = $dataPermintaan->avg() ?: $permintaanHarian;
            $maxDaily = $dataPermintaan->max() ?: $avgDaily;

            $SS = max(0, ($maxDaily - $avgDaily) * $L);

            // 8️⃣ Reorder Point (ROP)
            $ROP = ($permintaanHarian * $L) + $SS;

            // 9️⃣ HPP (Harga Pokok Produksi / per unit)
            $purchaseRows = DB::table('detail_pembelian as dp')
                ->join('pembelian as p', 'dp.pembelian_Id_Pembelian', '=', 'p.Id_Pembelian')
                ->where('dp.bahan_baku_Id_Bahan', $b->Id_Bahan)
                ->selectRaw('SUM(dp.Harga_Keseluruhan) as total_price, SUM(dp.Jumlah) as total_qty')
                ->first();

            if ($purchaseRows && $purchaseRows->total_qty > 0) {
                $HPP = $purchaseRows->total_price / $purchaseRows->total_qty;
            } else {
                $HPP = DB::table('detail_pembelian')
                    ->where('bahan_baku_Id_Bahan', $b->Id_Bahan)
                    ->avg(DB::raw('CASE WHEN Jumlah>0 THEN Harga_Keseluruhan/Jumlah ELSE 0 END')) ?: 0;
            }

        } else {
            // Barang jadi tidak dihitung EOQ/ROP/SS
            $EOQ = 0;
            $ROP = 0;
            $SS  = 0;
        }

        // 🔟 Update hasil perhitungan ke tabel barang
        $updateData = [
            'EOQ'          => round($EOQ),
            'ROP'          => round($ROP),
            'Safety_Stock' => round($SS),
        ];

        if (Schema::hasColumn('barang', 'HPP') && $HPP !== null) {
            $updateData['HPP'] = round($HPP, 2);
        }

        DB::table('barang')
            ->where('Id_Bahan', $b->Id_Bahan)
            ->update($updateData);
    }
}
