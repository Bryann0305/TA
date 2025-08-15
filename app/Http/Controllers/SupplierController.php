<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('procurement.supplier', compact('suppliers'));
    }

    public function create()
    {
        return view('procurement.create_supplier');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required',
            'nama_pegawai'  => 'required',
            'email'         => 'required|email',
            'kontak'        => 'required',
            'alamat'        => 'required',
            'status'        => 'required',
        ]);

        $data = [
            'Nama_Supplier' => $validated['nama_supplier'],
            'Nama_Pegawai'  => $validated['nama_pegawai'],
            'Email'         => $validated['email'],
            'Kontak'        => $validated['kontak'],
            'Alamat'        => $validated['alamat'],
            'Status'        => $validated['status'],
        ];

        Supplier::create($data);

        return redirect()->route('procurement.supplier')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
    return view('procurement.show_supplier', compact('supplier'));
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('procurement.edit_supplier', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'nama_supplier' => 'required',
            'nama_pegawai'  => 'required',
            'email'         => 'required|email',
            'kontak'        => 'required',
            'alamat'        => 'required',
            'status'        => 'required',
        ]);

        $data = [
            'Nama_Supplier' => $validated['nama_supplier'],
            'Nama_Pegawai'  => $validated['nama_pegawai'],
            'Email'         => $validated['email'],
            'Kontak'        => $validated['kontak'],
            'Alamat'        => $validated['alamat'],
            'Status'        => $validated['status'],
        ];

        $supplier->update($data);

        return redirect()->route('procurement.supplier')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Supplier::destroy($id);
        return redirect()->route('procurement.supplier')->with('success', 'Supplier berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->Status = $supplier->Status === 'Active' ? 'Inactive' : 'Active';
        $supplier->save();
        return redirect()->route('procurement.supplier')->with('success', 'Status supplier berhasil diubah.');
    }
}