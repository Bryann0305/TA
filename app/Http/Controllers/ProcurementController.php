<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\DetailPembelian;
use Illuminate\Database\QueryException;

class ProcurementController extends Controller
{
    // List semua Purchase Order
    public function index()
    {
        $orders = Pembelian::with(['supplier', 'detailPembelian', 'detailPembelian.barang'])->get();
        return view('procurement.index', compact('orders'));
    }

    // Form tambah PO
    public function create()
    {
        $suppliers = Supplier::all();
        $barangs   = Barang::all();
        $gudangs   = Gudang::all();
        return view('procurement.create', compact('suppliers', 'barangs', 'gudangs'));
    }

    // Simpan PO baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'Tanggal_Pemesanan'    => 'required|date',
            'Tanggal_Kedatangan'   => 'nullable|date|after_or_equal:Tanggal_Pemesanan',
            'Metode_Pembayaran'    => 'nullable|string|max:50',
            'Status_Pembayaran'    => 'nullable|in:Pending,Confirmed',
            'details.*.bahan_baku_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'details.*.Jumlah'              => 'required|integer|min:1',
            'details.*.Harga'               => 'required|numeric|min:0',
            'details.*.Keterangan'          => 'nullable|string|max:255',
        ]);

        $totalBiaya = 0;
        foreach ($request->details as $d) {
            $totalBiaya += $d['Jumlah'] * $d['Harga'];
        }

        // Simpan header pembelian
        $pembelian = Pembelian::create([
            'supplier_Id_Supplier' => $request->supplier_Id_Supplier,
            'Tanggal_Pemesanan'    => $request->Tanggal_Pemesanan,
            'Tanggal_Kedatangan'   => $request->Tanggal_Kedatangan,
            'Metode_Pembayaran'    => $request->Metode_Pembayaran,
            'Total_Biaya'          => $totalBiaya,
            'Status_Pembayaran'    => 'Pending',
            'user_Id_User'         => auth()->id(),
        ]);

        // Simpan detail barang
        foreach ($request->details as $d) {
            DetailPembelian::create([
                'pembelian_Id_Pembelian' => $pembelian->Id_Pembelian,
                'bahan_baku_Id_Bahan'    => $d['bahan_baku_Id_Bahan'],
                'Jumlah'                 => $d['Jumlah'],
                'Harga_Keseluruhan'      => $d['Jumlah'] * $d['Harga'],
                'gudang_Id_Gudang'       => $request->gudang_Id_Gudang,
                'Keterangan'             => $d['Keterangan'] ?? '-', // ✅ default jika kosong
            ]);

            // Update stok barang
            $barang = Barang::find($d['bahan_baku_Id_Bahan']);
            if ($barang) {
                $barang->Stok += $d['Jumlah'];
                $barang->save();
            }
        }

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dibuat!');
    }

    // Detail PO
    public function show($id)
    {
        $order = Pembelian::with(['supplier', 'detailPembelian', 'detailPembelian.barang'])->findOrFail($id);
        return view('procurement.show', compact('order'));
    }

    // Form edit PO
    public function edit($id)
    {
        $order     = Pembelian::with('detailPembelian')->findOrFail($id);
        $suppliers = Supplier::all();
        $barangs   = Barang::all();
        $gudangs   = Gudang::all();
        return view('procurement.edit', compact('order', 'suppliers', 'barangs', 'gudangs'));
    }

    // Update PO
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'Tanggal_Pemesanan'    => 'required|date',
            'Tanggal_Kedatangan'   => 'nullable|date|after_or_equal:Tanggal_Pemesanan',
            'Metode_Pembayaran'    => 'nullable|string|max:50',
            'Status_Pembayaran'    => 'required|in:Pending,Confirmed',
        ]);

        $order = Pembelian::findOrFail($id);
        $order->update($validated);

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil diperbarui!');
    }

    // Hapus PO
    public function destroy($id)
    {
        $order = Pembelian::findOrFail($id);

        try {
            $order->delete();
            return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dihapus!');
        } catch (QueryException $e) {
            return redirect()->route('procurement.index')
                ->with('error', 'Purchase Order tidak bisa dihapus karena masih memiliki data terkait.');
        }
    }

    // Toggle status pembayaran
    public function togglePayment($id)
    {
        $order = Pembelian::findOrFail($id);
        $order->Status_Pembayaran = $order->Status_Pembayaran === 'Pending' ? 'Confirmed' : 'Pending';
        $order->save();

        return redirect()->route('procurement.index')->with('success', 'Status pembayaran berhasil diperbarui!');
    }
}
