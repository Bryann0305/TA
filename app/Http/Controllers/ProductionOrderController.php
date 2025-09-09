<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\PesananProduksi;
use App\Models\Penjadwalan;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    /**
     * Tampilkan daftar semua SPK (Production Orders)
     */
    public function index()
    {
        $orders = ProductionOrder::with(['pesananProduksi', 'penjadwalan'])->get();
        return view('production-order.index', compact('orders'));
    }

    /**
     * Form untuk membuat SPK baru
     */
    public function create()
    {
        // Hanya tampilkan pesanan produksi yang belum punya SPK
        $pesanan = PesananProduksi::doesntHave('productionOrder')->get();
        return view('production-order.create', compact('pesanan'));
    }

    /**
     * Simpan SPK baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'pesanan_produksi_id' => 'required|exists:pesanan_produksi,Id_Pesanan',
            'Nama_Produksi' => 'required|string',
            'Tanggal_Produksi' => 'required|date',
            'Tanggal_Mulai' => 'required|date',
            'Tanggal_Selesai' => 'required|date|after_or_equal:Tanggal_Mulai',
        ]);

        // Simpan data SPK
        $order = ProductionOrder::create([
            'pesanan_produksi_id' => $request->pesanan_produksi_id,
            'Nama_Produksi' => $request->Nama_Produksi,
            'Tanggal_Produksi' => $request->Tanggal_Produksi,
            'Status' => 'pending',
        ]);

        // Simpan penjadwalan untuk SPK
        Penjadwalan::create([
            'production_order_id' => $order->id,
            'Tanggal_Mulai' => $request->Tanggal_Mulai,
            'Tanggal_Selesai' => $request->Tanggal_Selesai,
            'Status' => 'scheduled',
        ]);

        return redirect()->route('production_order.index')
                         ->with('success', 'Surat Perintah Produksi berhasil dibuat');
    }

    /**
     * Tampilkan detail SPK
     */
    public function show($id)
    {
        $order = ProductionOrder::with(['pesananProduksi', 'penjadwalan'])->findOrFail($id);
        return view('production-order.show', compact('order'));
    }

    /**
     * Form untuk edit SPK
     */
    public function edit($id)
    {
        $order = ProductionOrder::with('penjadwalan')->findOrFail($id);
        return view('production-order.edit', compact('order'));
    }

    /**
     * Update SPK
     */
    public function update(Request $request, $id)
    {
        $order = ProductionOrder::findOrFail($id);

        $request->validate([
            'Nama_Produksi' => 'required|string',
            'Tanggal_Produksi' => 'required|date',
            'Tanggal_Mulai' => 'required|date',
            'Tanggal_Selesai' => 'required|date|after_or_equal:Tanggal_Mulai',
        ]);

        // Update data SPK
        $order->update([
            'Nama_Produksi' => $request->Nama_Produksi,
            'Tanggal_Produksi' => $request->Tanggal_Produksi,
        ]);

        // Update penjadwalan
        if ($order->penjadwalan) {
            $order->penjadwalan->update([
                'Tanggal_Mulai' => $request->Tanggal_Mulai,
                'Tanggal_Selesai' => $request->Tanggal_Selesai,
            ]);
        }

        return redirect()->route('production_order.index')
                         ->with('success', 'Surat Perintah Produksi berhasil diperbarui');
    }

    /**
     * Hapus SPK
     */
    public function destroy($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $order->delete();

        return redirect()->route('production_order.index')
                         ->with('success', 'Surat Perintah Produksi berhasil dihapus');
    }

    /**
     * Approve SPK (ubah status menjadi confirmed)
     */
    public function approve($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $order->update(['Status' => 'confirmed']);

        return redirect()->route('production_order.index')
                         ->with('success', 'Surat Perintah Produksi berhasil di-approve');
    }

    /**
     * Update status SPK (alternatif approve)
     */
    public function updateStatus($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $order->Status = 'confirmed';
        $order->save();

        return redirect()->route('production_order.index')
                         ->with('success', 'Surat Perintah Produksi berhasil di-approve!');
    }
}
