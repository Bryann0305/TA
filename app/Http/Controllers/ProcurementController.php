<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use App\Models\Barang; 
use App\Models\Gudang;

class ProcurementController extends Controller
{
    // Menampilkan daftar pembelian
    public function index()
    {
        $orders = Pembelian::with(['supplier', 'detailPembelian.barang'])->get();
        return view('procurement.index', compact('orders'));
    }

    // Form buat pembelian baru
    public function create()
    {
        $suppliers = Supplier::all();
        $barangs = Barang::all();
        $gudangs = Gudang::all(); // Ambil data gudang
        return view('procurement.create', compact('suppliers', 'barangs', 'gudangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Total_Biaya' => 'required|numeric',
            'Tanggal_Pemesanan' => 'required|date',
            'Tanggal_Kedatangan' => 'nullable|date',
            'Metode_Pembayaran' => 'required|string',
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'gudang_Id_Gudang' => 'required|exists:gudang,Id_Gudang',
            'Nama_Barang' => 'required|array',
        ]);

        $pembelian = Pembelian::create([
            'Total_Biaya' => $request->Total_Biaya,
            'Tanggal_Pemesanan' => $request->Tanggal_Pemesanan,
            'Tanggal_Kedatangan' => $request->Tanggal_Kedatangan,
            'Metode_Pembayaran' => $request->Metode_Pembayaran,
            'Status_Pembayaran' => 'Pending',
            'user_Id_User' => auth()->id(),
            'supplier_Id_Supplier' => $request->supplier_Id_Supplier,
        ]);

        $gudangId = $request->gudang_Id_Gudang;

        foreach ($request->Nama_Barang as $barangId) {
            DetailPembelian::create([
                'pembelian_Id_Pembelian' => $pembelian->Id_Pembelian,
                'bahan_baku_Id_Bahan' => $barangId,
                'Jumlah' => 1,
                'Harga_Keseluruhan' => 0,
                'Keterangan' => '',
                'gudang_Id_Gudang' => $gudangId,
            ]);
        }

        return redirect()->route('procurement.index')->with('success', 'Purchase Order created successfully!');
    }


    // Tampilkan detail pembelian
    public function show($id)
    {
    $pembelian = Pembelian::with(['supplier', 'detailPembelian.barang', 'detailPembelian.gudang'])
                          ->findOrFail($id);

    return view('procurement.show', compact('pembelian'));
    }


    // Form edit pembelian
    public function edit($id)
    {
        $order = Pembelian::with('detailPembelian.barang')->findOrFail($id);
        $suppliers = Supplier::all();
        $barangs = Barang::all(); // ganti variabel jadi $barangs
        $gudangs = Gudang::all(); // Ambil data gudang
        return view('procurement.edit', compact('order', 'suppliers', 'barangs', 'gudangs'));
    }

    // Update pembelian
    public function update(Request $request, $id)
    {
        $request->validate([
            'Total_Biaya' => 'required|numeric',
            'Tanggal_Pemesanan' => 'required|date',
            'Tanggal_Kedatangan' => 'nullable|date',
            'Metode_Pembayaran' => 'required|string',
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'gudang_Id_Gudang' => 'required|exists:gudang,Id_Gudang',
            'Nama_Barang' => 'required|array',
        ]);

        $pembelian = Pembelian::findOrFail($id);
        $pembelian->update([
            'Total_Biaya' => $request->Total_Biaya,
            'Tanggal_Pemesanan' => $request->Tanggal_Pemesanan,
            'Tanggal_Kedatangan' => $request->Tanggal_Kedatangan,
            'Metode_Pembayaran' => $request->Metode_Pembayaran,
            'supplier_Id_Supplier' => $request->supplier_Id_Supplier,
        ]);

        // Hapus detail lama dan buat ulang
        $pembelian->detailPembelian()->delete();
        foreach ($request->Nama_Barang as $barangId) {
            DetailPembelian::create([
                'pembelian_Id_Pembelian' => $pembelian->Id_Pembelian,
                'bahan_baku_Id_Bahan' => $barangId,
                'Jumlah' => 1,
                'Harga_Keseluruhan' => 0,
            ]);
        }

        return redirect()->route('procurement.index')->with('success', 'Purchase Order updated successfully!');
    }

    // Hapus pembelian
    public function destroy($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $pembelian->detailPembelian()->delete();
        $pembelian->delete();

        return redirect()->route('procurement.index')->with('success', 'Purchase Order deleted successfully!');
    }

    // Toggle status pembayaran (Pending <-> Confirmed)
    public function togglePayment($id)
    {
        $pembelian = Pembelian::findOrFail($id);
        $pembelian->Status_Pembayaran = $pembelian->Status_Pembayaran === 'Pending' ? 'Confirmed' : 'Pending';
        $pembelian->save();

        return redirect()->back()->with('success', 'Payment status updated successfully!');
    }
}
