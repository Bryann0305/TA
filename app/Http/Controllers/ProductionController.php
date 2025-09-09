<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\ProduksiDetail;
use App\Models\ProductionOrder;
use App\Models\BillOfMaterial;
use App\Models\GagalProduksi;

class ProductionController extends Controller
{
    // Halaman index produksi
    public function index()
    {
        $produksiPlanned = Produksi::where('Status', 'planned')
            ->with(['details.billOfMaterial', 'details.barang', 'productionOrder'])
            ->get();

        $produksiCurrent = Produksi::where('Status', 'current')
            ->with(['details.billOfMaterial', 'details.barang', 'productionOrder'])
            ->get();

        $produksiCompleted = Produksi::where('Status', 'completed')
            ->with(['details.billOfMaterial', 'details.barang', 'productionOrder'])
            ->get();

        return view('production.index', compact('produksiPlanned','produksiCurrent','produksiCompleted'));
    }

    // Form create produksi baru
    public function create()
    {
    // Ambil SPK yang belum pernah dipakai di produksi
    // Tampilkan semua Production Order agar dropdown tidak kosong
    // Jika ingin mengecualikan yang sudah dipakai, aktifkan kembali whereNotIn
    $orders = ProductionOrder::with(['pesananProduksi.detail'])
        ->orderBy('id', 'desc')
        ->get();
        $boms = BillOfMaterial::with('barangs')->get(); // BOM pilihan

        return view('production.create', compact('orders','boms'));
    }

    // Simpan produksi baru
    public function store(Request $request)
    {
        $request->validate([
            'production_order_id' => 'required|exists:production_order,id',
            'bill_of_materials' => 'required|array|min:1',
        ]);

        // Cek apakah SPK sudah pernah dipakai
        if (Produksi::where('production_order_id', $request->production_order_id)->exists()) {
            return redirect()->back()->with('error', 'SPK sudah pernah dipakai untuk produksi.');
        }

        $order = ProductionOrder::findOrFail($request->production_order_id);

        // Ambil penjadwalan ID jika ada
        $jadwalId = $order->penjadwalan->Id_Jadwal ?? null;

        // Buat produksi baru
        $produksi = Produksi::create([
            'Hasil_Produksi' => $order->Nama_Produksi ?? 'Produksi #' . $order->id,
            'Tanggal_Produksi' => now(),
            'Status' => 'planned',
            'Jumlah_Berhasil' => 0,
            'Jumlah_Gagal' => 0,
            'pesanan_produksi_Id_Pesanan' => $order->pesanan_produksi_id,
            'penjadwalan_Id_Jadwal' => $jadwalId,
            'bill_of_material_Id_bill_of_material' => $request->bill_of_materials[0] ?? null,
            'production_order_id' => $order->id,
        ]);

        // Tambahkan detail produksi tiap BOM & barang sesuai jumlah BOM yang diinput
        $billOfMaterials = $request->bill_of_materials;
        $jumlahBOMs = $request->jumlah_bom;
        foreach ($billOfMaterials as $idx => $bom_id) {
            $bom = BillOfMaterial::with('barangs')->find($bom_id);
            if (!$bom) continue;
            $jumlah = isset($jumlahBOMs[$idx]) ? (int)$jumlahBOMs[$idx] : 1;
            foreach ($bom->barangs as $barang) {
                for ($i = 0; $i < $jumlah; $i++) {
                    ProduksiDetail::create([
                        'produksi_id' => $produksi->Id_Produksi,
                        'bill_of_material_id' => $bom->Id_bill_of_material,
                        'barang_id' => $barang->Id_Bahan,
                        'jumlah' => 0,
                        'status' => 'pending',
                    ]);
                }
            }
        }

        return redirect()->route('production.index')
            ->with('success', 'Produksi berhasil dibuat, status: planned.');
    }

    // Approve produksi
    public function approve($id)
    {
        $produksi = Produksi::findOrFail($id);
        if ($produksi->Status != 'planned') {
            return redirect()->route('production.index')
                ->with('error', 'Produksi hanya bisa diapprove dari status planned.');
        }

        $produksi->update(['Status' => 'current']);
        return redirect()->route('production.index')
            ->with('success', 'Produksi diapprove, status sekarang: current.');
    }

    // Detail produksi
    public function show($id)
    {
        $produksi = Produksi::with([
            'details.billOfMaterial',
            'details.barang',
            'gagalProduksi',
            'productionOrder'
        ])->findOrFail($id);

        return view('production.show', compact('produksi'));
    }

    // Selesai produksi
    public function complete(Request $request, $id)
    {
        $request->validate([
            'Jumlah_Berhasil' => 'required|numeric|min:0',
            'gagal' => 'array'
        ]);

        $produksi = Produksi::findOrFail($id);

        $produksi->update([
            'Status' => 'completed',
            'Jumlah_Berhasil' => $request->Jumlah_Berhasil,
            'Jumlah_Gagal' => array_sum(array_column($request->gagal ?? [], 'total'))
        ]);

        if ($request->gagal) {
            foreach ($request->gagal as $detail_id => $gagal) {
                GagalProduksi::create([
                    'produksi_Id_Produksi' => $id,
                    'Total_Gagal' => $gagal['total'] ?? 0,
                    'Keterangan' => $gagal['keterangan'] ?? null
                ]);

                $detail = ProduksiDetail::find($detail_id);
                if ($detail) $detail->update(['status' => 'completed']);
            }
        }

        return redirect()->route('production.show', $id)
            ->with('success', 'Produksi selesai dan berhasil/gagal tersimpan.');
    }
}
