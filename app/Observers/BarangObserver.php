<?php

namespace App\Observers;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
     * Fungsi untuk menghitung EOQ, ROP, Safety Stock, dan HPP
     */
    private function calculateEOQandROP(Barang $b)
    {
        $tahun = date('Y');

        // Inisialisasi default
        $EOQ = 0;
        $ROP = 0;
        $SS = 0;
        $HPP = null;

        if (strtolower($b->Jenis) === 'bahan baku') {
            // === 1. Permintaan Tahunan (D) ===
            $permintaanProduksi = DB::table('detail_pesanan_produksi')
                ->where('barang_Id_Bahan', $b->Id_Bahan)
                ->whereYear('created_at', $tahun)
                ->sum('Jumlah');

            $permintaanLangsung = DB::table('detail_pembelian as dp')
                ->join('pembelian as p', 'dp.pembelian_Id_Pembelian', '=', 'p.Id_Pembelian')
                ->where('dp.bahan_baku_Id_Bahan', $b->Id_Bahan)
                ->whereYear('p.Tanggal_Pemesanan', $tahun)
                ->sum('dp.Jumlah');

            $D = $permintaanProduksi + $permintaanLangsung;

            // === 2. Biaya Pemesanan (S) ===
            $S = DB::table('pembelian')
                ->whereYear('Tanggal_Pemesanan', $tahun)
                ->avg('Biaya_Pengiriman') ?? 20000;

            // === 3. Biaya Simpan (H) ===
            $biayaGudangTahun = DB::table('biaya_gudang')
                ->whereYear('tanggal_biaya', $tahun)
                ->sum(DB::raw('biaya_sewa + biaya_listrik + biaya_air'));

            $stokRataRata = DB::table('barang')->avg('Stok') ?: 1;
            $H = $stokRataRata > 0 ? ($biayaGudangTahun / $stokRataRata) : 0;

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

            // === 9. HPP untuk Bahan Baku ===
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
                    ->avg(DB::raw('CASE WHEN Jumlah > 0 THEN Harga_Keseluruhan / Jumlah ELSE 0 END'));
            }
        }

        else {
            // Barang jadi tidak perlu EOQ/ROP/SS
            $EOQ = 0;
            $ROP = 0;
            $SS = 0;

            // === 1. Hitung Biaya Material dari BOM ===
            $materialCost = 0;

            $bomRows = DB::table('barang_has_bill_of_material as bb')
                ->where('bb.barang_Id_Bahan', $b->Id_Bahan)
                ->select('bb.bill_of_material_Id_bill_of_material as bom_id')
                ->get();

            foreach ($bomRows as $bomRow) {
                $components = DB::table('barang_has_bill_of_material as comp')
                    ->where('comp.bill_of_material_Id_bill_of_material', $bomRow->bom_id)
                    ->join('barang as bahan', 'comp.barang_Id_Bahan', '=', 'bahan.Id_Bahan')
                    ->select('comp.barang_Id_Bahan as bahan_id', 'comp.Jumlah_Bahan as jumlah_bahan')
                    ->get();

                foreach ($components as $comp) {
                    $purchase = DB::table('detail_pembelian as dp')
                        ->join('pembelian as p', 'dp.pembelian_Id_Pembelian', '=', 'p.Id_Pembelian')
                        ->where('dp.bahan_baku_Id_Bahan', $comp->bahan_id)
                        ->selectRaw('SUM(dp.Harga_Keseluruhan) as total_price, SUM(dp.Jumlah) as total_qty')
                        ->first();

                    $unitCost = 0;
                    if ($purchase && $purchase->total_qty > 0) {
                        $unitCost = $purchase->total_price / $purchase->total_qty;
                    } else {
                        $unitCost = DB::table('detail_pembelian')
                            ->where('bahan_baku_Id_Bahan', $comp->bahan_id)
                            ->avg(DB::raw('CASE WHEN Jumlah > 0 THEN Harga_Keseluruhan / Jumlah ELSE 0 END')) ?? 0;
                    }

                    $materialCost += $comp->jumlah_bahan * $unitCost;
                }
            }

            // === 2. Overhead Produksi ===
            $totalBiayaGudang = DB::table('biaya_gudang')
                ->whereYear('tanggal_biaya', $tahun)
                ->sum(DB::raw('biaya_sewa + biaya_listrik + biaya_air'));

            $totalProduced = DB::table('produksi')
                ->whereYear('Tanggal_Produksi', $tahun)
                ->sum('Jumlah_Berhasil');

            $overheadPerUnit = ($totalProduced > 0)
                ? ($totalBiayaGudang / $totalProduced)
                : 0;

            // === 3. HPP Barang Jadi ===
            $HPP = $materialCost + $overheadPerUnit;
        }

        
        $updateData = [
            'EOQ' => round($EOQ),
            'ROP' => round($ROP),
            'Safety_Stock' => round($SS),
        ];

        // Tambahkan HPP jika kolom tersedia
        if (Schema::hasColumn('barang', 'HPP') && $HPP !== null) {
            $updateData['HPP'] = round($HPP, 2);
        }

        DB::table('barang')
            ->where('Id_Bahan', $b->Id_Bahan)
            ->update($updateData);
    }
}
