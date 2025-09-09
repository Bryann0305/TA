<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $data = [];

        // =========================
        // Inventory Reports
        // =========================
        $barang = DB::table('barang')->get();

        // Stock Value
        $data['stockValue'] = $barang->sum(fn($b) => $b->Stok * $b->Berat);

        // Items Below ROP
        $data['itemsBelowROP'] = $barang->where('Stok', '<', 'ROP')->count();

        // Critical Items (Stok < 50% ROP)
        $data['criticalItems'] = $barang->filter(fn($b) => $b->Stok < ($b->ROP / 2))->count();

        // Inventory Turns sederhana
        $data['inventoryTurns'] = $data['itemsBelowROP'] ? round($data['stockValue'] / $data['itemsBelowROP'], 2) : 0;

        // Inventory by Category
        $categories = DB::table('barang')
            ->join('kategori', 'barang.kategori_Id_Kategori', '=', 'kategori.Id_Kategori')
            ->select('kategori.Nama_Kategori as name', DB::raw('SUM(Stok * Berat) as value'))
            ->groupBy('kategori.Nama_Kategori')
            ->get();

        $totalValue = $categories->sum('value');
        $data['categories'] = $categories->map(fn($cat) => [
            'name' => $cat->name,
            'value' => $cat->value,
            'percentage' => $totalValue ? round(($cat->value / $totalValue) * 100, 2) : 0
        ]);

        // EOQ Summary (√(2DS/H))
        $data['eoqSummary'] = $barang->map(fn($b) => [
            'material' => $b->Nama_Bahan,
            'demand' => $b->Stok * 12, // contoh annual demand
            'qty' => $b->EOQ ?? round(sqrt(2 * ($b->Stok * 12) * 100 / max(0.1, $b->Stok * 0.1))), // EOQ formula contoh
            'rop' => $b->ROP,
            'holding' => $b->Stok * 0.1,
            'orderCost' => 12 / max($b->EOQ ?? 1,1),
            'total' => ($b->Stok * 0.1) + (12 / max($b->EOQ ?? 1,1))
        ]);

        // =========================
        // Production Reports
        // =========================
        $productions = DB::table('produksi')
            ->leftJoin('production_order', 'produksi.production_order_id', '=', 'production_order.id')
            ->leftJoin('pesanan_produksi', 'produksi.pesanan_produksi_Id_Pesanan', '=', 'pesanan_produksi.Id_Pesanan')
            ->select(
                'produksi.Id_Produksi',
                'production_order.Nama_Produksi',
                'produksi.Tanggal_Produksi',
                'produksi.Hasil_Produksi',
                'produksi.Status',
                'produksi.Jumlah_Berhasil',
                'pesanan_produksi.Nomor_Pesanan'
            )
            ->get();

        // Detail produksi per bahan
        $productionDetails = DB::table('produksi_detail')
            ->join('barang', 'produksi_detail.barang_id', '=', 'barang.Id_Bahan')
            ->join('bill_of_material', 'produksi_detail.bill_of_material_id', '=', 'bill_of_material.Id_bill_of_material')
            ->select(
                'produksi_detail.id as detail_id',
                'produksi_detail.produksi_id',
                'bill_of_material.Nama_bill_of_material',
                'barang.Nama_Bahan',
                'produksi_detail.jumlah',
                'produksi_detail.status'
            )
            ->get()
            ->groupBy('produksi_id');

        $data['productions'] = $productions;
        $data['productionDetails'] = $productionDetails;

        // =========================
        // Procurement Reports
        // =========================
        $purchases = DB::table('pembelian')->select(
            'Id_Pembelian',
            'Tanggal_Pemesanan',
            'Total_Biaya',
            'Metode_Pembayaran',
            'Status_Pembayaran'
        )->get();
        $data['purchases'] = $purchases;

        return view('reports.index', compact('data'));
    }
}
