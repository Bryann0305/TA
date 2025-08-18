<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;

class GudangController extends Controller
{
    // List semua gudang
    public function index()
    {
        $gudangs = Gudang::all();
        return view('gudang.index', compact('gudangs'));
    }

    // Form create gudang baru
    public function create()
    {
        return view('gudang.create');
    }

    // Simpan gudang baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nama_Gudang' => 'required|string|max:255',
            'Lokasi' => 'required|string|max:255',
            'Kapasitas' => 'required|numeric|min:0',
        ]);

        Gudang::create($validated);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil ditambahkan!');
    }

    // Form edit gudang
    public function edit($id)
    {
        $gudang = Gudang::findOrFail($id);
        return view('gudang.edit', compact('gudang'));
    }

    // Update gudang
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Nama_Gudang' => 'required|string|max:255',
            'Lokasi' => 'required|string|max:255',
            'Kapasitas' => 'required|numeric|min:0',
        ]);

        $gudang = Gudang::findOrFail($id);
        $gudang->update($validated);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil diperbarui!');
    }

    // Hapus gudang
    public function destroy($id)
    {
        $gudang = Gudang::findOrFail($id);
        $gudang->delete();

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus!');
    }
}
