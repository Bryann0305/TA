<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_Supplier' => 'required|string|max:255',
            'Nama_Pegawai'  => 'nullable|string|max:255',
            'Email'         => 'nullable|email|max:255',
            'Kontak'        => 'nullable|string|max:50',
            'Alamat'        => 'nullable|string|max:255',
        ]);

        Supplier::create([
            'Nama_Supplier' => $request->Nama_Supplier,
            'Nama_Pegawai'  => $request->Nama_Pegawai,
            'Email'         => $request->Email,
            'Kontak'        => $request->Kontak,
            'Alamat'        => $request->Alamat,
            'Status'        => 'Pending',
            'keterangan'    => null,
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil ditambahkan (Pending).');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('supplier.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'Nama_Supplier' => 'required|string|max:255',
            'Nama_Pegawai'  => 'nullable|string|max:255',
            'Email'         => 'nullable|email|max:255',
            'Kontak'        => 'nullable|string|max:50',
            'Alamat'        => 'nullable|string|max:255',
        ]);

        $supplier->update([
            'Nama_Supplier' => $request->Nama_Supplier,
            'Nama_Pegawai'  => $request->Nama_Pegawai,
            'Email'         => $request->Email,
            'Kontak'        => $request->Kontak,
            'Alamat'        => $request->Alamat,
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil diupdate.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dihapus.');
    }

    public function approve($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'Status'     => 'Aktif',
            'keterangan' => null
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil di-approve (Aktif).');
    }

    public function deactivate(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string|max:255',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'Status'     => 'Non Aktif',
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('supplier.index')->with('success', 'Supplier berhasil dinonaktifkan.');
    }
}
