<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index()
    {
        $orders = Pembelian::with(['supplier', 'user'])->get();

        return view('procurement.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('procurement.create_purchaseOrder', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'user_Id_User' => 'required|exists:user,Id_User',  // sesuaikan nama tabel user di DB kamu
            'Status' => 'required|string',
            'Tanggal_Pemesanan' => 'required|date',
            'Tanggal_Kedatangan' => 'nullable|date',
            'Status_Pembayaran' => 'required|in:Pending,Confirmed,Delivered',
            'Total_Biaya' => 'required|numeric',
        ]);

        Pembelian::create($validated);

        return redirect()->route('procurement.index')->with('success', 'Purchase order created successfully!');
    }
}
