<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

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
        $users = User::all(); // atau auth()->user() kalau hanya 1 user login
        return view('procurement.create_purchaseOrder', compact('suppliers', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Status' => 'required|string|max:50',
            'Total_Biaya' => 'required|numeric',
            'Tanggal_Pemesanan' => 'required|date',
            'Tanggal_Kedatangan' => 'nullable|date',
            'Status_Pembayaran' => 'required|string',
            'user_Id_User' => 'required|exists:user,Id_User',
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
        ]);

        Pembelian::create($validated);

        return redirect()->route('procurement.index')->with('success', 'Procurement berhasil ditambahkan.');
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
