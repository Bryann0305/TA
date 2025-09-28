<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BiayaGudang;
use App\Models\Gudang;

class BiayaGudangController extends Controller
{
    // Index - tampilkan semua biaya gudang
    public function index()
    {
        $biayaGudang = BiayaGudang::with('gudang')->orderBy('tanggal_biaya', 'desc')->get();
        return view('biaya-gudang.index', compact('biayaGudang'));
    }

    // Create - form tambah biaya gudang
    public function create()
    {
        $gudangs = Gudang::all();
        return view('biaya-gudang.create', compact('gudangs'));
    }

    // Store - simpan biaya gudang baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'gudang_Id_Gudang' => 'required|exists:gudang,Id_Gudang',
            'biaya_sewa' => 'required|numeric|min:0',
            'biaya_listrik' => 'required|numeric|min:0',
            'biaya_air' => 'required|numeric|min:0',
            'tanggal_biaya' => 'required|date',
            'keterangan' => 'nullable|string|max:500'
        ]);

        BiayaGudang::create($validated);

        return redirect()->route('biaya-gudang.index')
                        ->with('success', 'Biaya gudang berhasil ditambahkan!');
    }

    // Show - detail biaya gudang
    public function show($id)
    {
        $biayaGudang = BiayaGudang::with('gudang')->findOrFail($id);
        return view('biaya-gudang.show', compact('biayaGudang'));
    }

    // Edit - form edit biaya gudang
    public function edit($id)
    {
        $biayaGudang = BiayaGudang::findOrFail($id);
        $gudangs = Gudang::all();
        return view('biaya-gudang.edit', compact('biayaGudang', 'gudangs'));
    }

    // Update - update biaya gudang
    public function update(Request $request, $id)
    {
        $biayaGudang = BiayaGudang::findOrFail($id);

        $validated = $request->validate([
            'gudang_Id_Gudang' => 'required|exists:gudang,Id_Gudang',
            'biaya_sewa' => 'required|numeric|min:0',
            'biaya_listrik' => 'required|numeric|min:0',
            'biaya_air' => 'required|numeric|min:0',
            'tanggal_biaya' => 'required|date',
            'keterangan' => 'nullable|string|max:500'
        ]);

        $biayaGudang->update($validated);

        return redirect()->route('biaya-gudang.index')
                        ->with('success', 'Biaya gudang berhasil diperbarui!');
    }

    // Destroy - hapus biaya gudang
    public function destroy($id)
    {
        $biayaGudang = BiayaGudang::findOrFail($id);
        $biayaGudang->delete();

        return redirect()->route('biaya-gudang.index')
                        ->with('success', 'Biaya gudang berhasil dihapus!');
    }
}
