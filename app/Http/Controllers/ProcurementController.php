<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\User;
use App\Models\DetailPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcurementController extends Controller
{
    public function index()
    {
        $orders = Pembelian::with(['supplier', 'user'])->orderBy('Tanggal_Pemesanan', 'desc')->get();
        return view('procurement.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $users = User::all();
        $barangs = \App\Models\Barang::all();
        return view('procurement.create_purchaseOrder', compact('suppliers', 'users', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'Tanggal_Pemesanan' => 'required|date',
            'Status' => 'required|string',
            'Status_Pembayaran' => 'required|string',
            'Total_Biaya' => 'required|numeric|min:0',
            'user_Id_User' => 'required|exists:user,Id_User',
            'items' => 'required|array|min:1',
            'items.*.barang_Id_Barang' => 'required|exists:barang,Id_Bahan',
            'items.*.Jumlah' => 'required|integer|min:1',
            'items.*.Harga_Satuan' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pembelian = Pembelian::create([
                'supplier_Id_Supplier' => $request->supplier_Id_Supplier,
                'Tanggal_Pemesanan' => $request->Tanggal_Pemesanan,
                'Status' => $request->Status,
                'Status_Pembayaran' => $request->Status_Pembayaran,
                'Total_Biaya' => $request->Total_Biaya,
                'user_Id_User' => $request->user_Id_User,
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['Jumlah'] * $item['Harga_Satuan'];
                DetailPembelian::create([
                    'pembelian_Id_Pembelian' => $pembelian->Id_Pembelian,
                    'barang_Id_Barang' => $item['barang_Id_Barang'],
                    'Jumlah' => $item['Jumlah'],
                    'Harga_Satuan' => $item['Harga_Satuan'],
                    'Subtotal' => $subtotal,
                ]);
            }

            DB::commit();
            return redirect()->route('procurement.index')->with('success', 'Purchase Order created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating purchase order: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $procurement = Pembelian::with(['supplier', 'user', 'detailPembelian'])->findOrFail($id);
        return view('procurement.show', compact('procurement'));
    }

    public function edit($id)
    {
        $procurement = Pembelian::findOrFail($id);
        $suppliers = Supplier::all();
        $users = User::all();
        return view('procurement.edit', compact('procurement', 'suppliers', 'users'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Status' => 'required|string|max:50',
            'Total_Biaya' => 'required|numeric',
            'Tanggal_Pemesanan' => 'required|date',
            'Tanggal_Kedatangan' => 'nullable|date',
            'Status_Pembayaran' => 'required|string',
            'user_Id_User' => 'required|exists:user,Id_User',
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'nama_bahan' => 'required|string|max:255',
        ]);

        $procurement = Pembelian::findOrFail($id);
        $procurement->update($validated);

        return redirect()->route('procurement.index')->with('success', 'Procurement berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $procurement = Pembelian::findOrFail($id);
        $procurement->delete();

        return redirect()->route('procurement.index')->with('success', 'Procurement berhasil dihapus.');
    }
}
