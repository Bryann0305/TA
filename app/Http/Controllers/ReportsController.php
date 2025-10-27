<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    /**
     * Ambil semua data untuk laporan utama
     */
    private function getReportData()
    {
        $data = [];

        // =========================
        // INVENTORY REPORTS
        // =========================
        $barang = DB::table('barang')->get();

        $data['stockValue'] = $barang->sum(fn($b) => ($b->Stok ?? 0) * ($b->HPP ?? 0));
        $data['itemsBelowROP'] = $barang->filter(fn($b) => (($b->Stok ?? 0) < ($b->ROP ?? 0)))->count();
        $data['criticalItems'] = $barang->filter(fn($b) => (($b->Stok ?? 0) < (($b->ROP ?? 0) / 2)))->count();

        // Inventory by Category
        $categories = DB::table('barang')
            ->join('kategori', 'barang.kategori_Id_Kategori', '=', 'kategori.Id_Kategori')
            ->select('kategori.Nama_Kategori as name', DB::raw('SUM(Stok * COALESCE(HPP, 0)) as value'))
            ->groupBy('kategori.Nama_Kategori')
            ->get();

        $totalValue = $categories->sum('value');
        $data['categories'] = $categories->map(fn($cat) => [
            'name' => $cat->name,
            'value' => $cat->value,
            'percentage' => $totalValue ? round(($cat->value / $totalValue) * 100, 2) : 0,
        ]);

        // EOQ Summary
        $data['eoqSummary'] = $barang->map(fn($b) => [
            'material' => $b->Nama_Bahan ?? $b->Nama_Barang ?? 'Unknown',
            'demand' => ($b->Stok ?? 0) * 12,
            'qty' => $b->EOQ ?? 0,
            'rop' => $b->ROP ?? 0,
            'total' => ($b->Stok ?? 0) * ($b->HPP ?? 0),
        ]);

        // =========================
        // PRODUCTION REPORTS
        // =========================
        $productions = DB::table('produksi')
            ->leftJoin('production_order', 'produksi.production_order_id', '=', 'production_order.id')
            ->select(
                'produksi.Id_Produksi',
                'production_order.Nama_Produksi',
                'produksi.Tanggal_Produksi',
                'produksi.Jumlah_Berhasil',
                'produksi.Status'
            )
            ->get();

        $data['productions'] = $productions;
        $data['totalProductions'] = $productions->count();
        $data['completedProductions'] = $productions->where('Status', 'Selesai')->count();

        // =========================
        // PROCUREMENT REPORTS
        // =========================
        $purchases = DB::table('pembelian')->get();
        $data['purchases'] = $purchases;
        $data['totalPurchases'] = $purchases->sum('Total_Biaya') ?? 0;
        $data['inventoryTurns'] = ($data['stockValue'] > 0)
            ? round(($data['totalPurchases'] ?? 0) / ($data['stockValue'] ?: 1), 2)
            : 0;

        // =========================
        // ORDER REPORTS
        // =========================
        if (DB::getSchemaBuilder()->hasTable('pesanan_produksi')) {
            $orders = DB::table('pesanan_produksi as pp')
                ->leftJoin('pelanggan as pl', 'pp.pelanggan_Id_Pelanggan', '=', 'pl.Id_Pelanggan')
                ->select(
                    'pp.Id_Pesanan as id',
                    'pl.Nama_Pelanggan as customer_name',
                    'pp.Tanggal_Pesanan as tanggal_order',
                    'pp.Jumlah_Pesanan as total',
                    'pp.Status as status'
                )
                ->get();
            $data['orders'] = $orders;
        } else {
            $data['orders'] = collect();
        }

        // =========================
        // WAREHOUSE REPORTS
        // =========================
        if (DB::getSchemaBuilder()->hasTable('gudang')) {
            $warehouses = DB::table('gudang')->get();

            $data['totalCapacity'] = $warehouses->sum('Kapasitas') ?? 0;
            $data['usedCapacity'] = DB::table('barang')->sum('Stok') ?? 0;
            $data['usageRate'] = $data['totalCapacity']
                ? round(($data['usedCapacity'] / $data['totalCapacity']) * 100, 2)
                : 0;

            $data['storageCost'] = DB::table('biaya_gudang')
                ->sum(DB::raw('COALESCE(biaya_sewa,0) + COALESCE(biaya_listrik,0) + COALESCE(biaya_air,0)')) ?? 0;

            $data['avgCostPerItem'] = $barang->count()
                ? round($data['storageCost'] / max(1, $barang->count()), 2)
                : 0;
        } else {
            $data['totalCapacity'] = 0;
            $data['usedCapacity'] = 0;
            $data['usageRate'] = 0;
            $data['storageCost'] = 0;
            $data['avgCostPerItem'] = 0;
        }

        return $data;
    }

    /**
     * Menampilkan halaman laporan utama
     */
    public function index()
    {
        $data = $this->getReportData();
        return view('reports.index', compact('data'));
    }

    /**
     * Export PDF berdasarkan tipe tab (inventory, production, procurement, order, warehouse)
     */
    public function export($type)
    {
        $data = $this->getReportData();

        if (!in_array($type, ['inventory', 'production', 'procurement', 'order', 'warehouse'])) {
            abort(404, 'Invalid report type');
        }

        $pdf = Pdf::loadView("reports.pdf.$type", compact('data'));
        return $pdf->download("report_{$type}.pdf");
    }
}
