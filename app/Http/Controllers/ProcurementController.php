<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\DetailPembelian;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    /**
     * List semua Purchase Order
     */
    public function index()
    {
        $orders = Pembelian::with(['supplier', 'detailPembelian', 'detailPembelian.barang'])->get();
        return view('procurement.index', compact('orders'));
    }

    /**
     * Form tambah PO
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $barangs   = Barang::all();
        $gudangs   = Gudang::all();
        return view('procurement.create', compact('suppliers', 'barangs', 'gudangs'));
    }

    /**
     * Simpan PO baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'Tanggal_Pemesanan'    => 'required|date',
            'Tanggal_Kedatangan'   => 'nullable|date|after_or_equal:Tanggal_Pemesanan',
            'Metode_Pembayaran'    => 'nullable|string|max:50',
            'details.*.bahan_baku_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'details.*.Jumlah'              => 'required|integer|min:1',
            'details.*.Harga'               => 'required|numeric|min:0',
            'details.*.Keterangan'          => 'nullable|string|max:255',
        ]);

        $totalBiaya = 0;
        foreach ($request->details as $d) {
            $totalBiaya += $d['Jumlah'] * $d['Harga'];
        }

        DB::transaction(function() use ($request, $totalBiaya) {
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

            // Simpan detail pembelian
            foreach ($request->details as $d) {
                DetailPembelian::create([
                    'pembelian_Id_Pembelian' => $pembelian->Id_Pembelian,
                    'bahan_baku_Id_Bahan'    => $d['bahan_baku_Id_Bahan'],
                    'Jumlah'                 => $d['Jumlah'],
                    'Harga_Keseluruhan'      => $d['Jumlah'] * $d['Harga'],
                    'gudang_Id_Gudang'       => $request->gudang_Id_Gudang,
                    'Keterangan'             => $d['Keterangan'] ?? '-',
                    'Status_Penerimaan'      => 'Pending',
                ]);
            }
        });

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dibuat!');
    }

    /**
     * Detail PO
     */
    public function show($id)
    {
        $order = Pembelian::with(['supplier', 'detailPembelian', 'detailPembelian.barang'])->findOrFail($id);
        return view('procurement.show', compact('order'));
    }

    /**
     * Form edit PO
     */
    public function edit($id)
    {
        $pembelian = Pembelian::with('detailPembelian')->findOrFail($id);
        $suppliers = Supplier::all();
        $barangs   = Barang::all();
        $gudangs   = Gudang::all();

        return view('procurement.edit', compact('pembelian', 'suppliers', 'barangs', 'gudangs'));
    }

    /**
     * Update PO dan detail
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'Tanggal_Pemesanan'    => 'required|date',
            'Tanggal_Kedatangan'   => 'nullable|date|after_or_equal:Tanggal_Pemesanan',
            'Metode_Pembayaran'    => 'nullable|string|max:50',
            'details.*.bahan_baku_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'details.*.Jumlah'              => 'required|integer|min:1',
            'details.*.Harga'               => 'required|numeric|min:0',
            'details.*.Keterangan'          => 'nullable|string|max:255',
        ]);

        $pembelian = Pembelian::findOrFail($id);

        DB::transaction(function() use ($request, $pembelian) {
            // Update header
            $totalBiaya = 0;
            foreach ($request->details as $d) {
                $totalBiaya += $d['Jumlah'] * $d['Harga'];
            }

            $pembelian->update([
                'supplier_Id_Supplier' => $request->supplier_Id_Supplier,
                'Tanggal_Pemesanan'    => $request->Tanggal_Pemesanan,
                'Tanggal_Kedatangan'   => $request->Tanggal_Kedatangan,
                'Metode_Pembayaran'    => $request->Metode_Pembayaran,
                'Total_Biaya'          => $totalBiaya,
            ]);

            // Hapus detail lama (atau bisa update item-by-item jika mau lebih kompleks)
            $pembelian->detailPembelian()->delete();

            // Simpan detail baru
            foreach ($request->details as $d) {
                DetailPembelian::create([
                    'pembelian_Id_Pembelian' => $pembelian->Id_Pembelian,
                    'bahan_baku_Id_Bahan'    => $d['bahan_baku_Id_Bahan'],
                    'Jumlah'                 => $d['Jumlah'],
                    'Harga_Keseluruhan'      => $d['Jumlah'] * $d['Harga'],
                    'gudang_Id_Gudang'       => $request->gudang_Id_Gudang,
                    'Keterangan'             => $d['Keterangan'] ?? '-',
                    'Status_Penerimaan'      => 'Pending',
                ]);
            }
        });

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil diperbarui!');
    }

    /**
     * Hapus PO
     */
    public function destroy($id)
{
    // Ambil PO beserta detail pembelian
    $order = Pembelian::with('detailPembelian')->findOrFail($id);

    // Cek apakah PO masih memiliki detail pembelian
    if ($order->detailPembelian()->count() > 0) {
        return redirect()->route('procurement.index')
            ->with('error', 'Purchase Order tidak bisa dihapus karena masih memiliki item terkait.');
    }

    try {
        $order->delete();
        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dihapus!');
    } catch (QueryException $e) {
        // Menangani kemungkinan relasi lain (misal ada foreign key lain di database)
        return redirect()->route('procurement.index')
            ->with('error', 'Purchase Order tidak bisa dihapus karena ada relasi lain di database.');
    }
}



    /**
     * Toggle status pembayaran
     */
    public function togglePayment($id)
    {
        $order = Pembelian::findOrFail($id);
        $order->Status_Pembayaran = $order->Status_Pembayaran === 'Pending' ? 'Confirmed' : 'Pending';
        $order->save();

        return redirect()->route('procurement.index')->with('success', 'Status pembayaran berhasil diperbarui!');
    }
}
