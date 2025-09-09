<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Tampilkan semua pelanggan
     */
    public function index()
    {
        $pelanggans = Pelanggan::all();
        return view('pelanggan.index', compact('pelanggans'));
    }

    /**
     * Form tambah pelanggan
     */
    public function create()
    {
        return view('pelanggan.create');
    }

    /**
     * Simpan pelanggan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nama_Pelanggan' => 'required|string|max:255',
            'Alamat'        => 'required|string',
            'Nomor_Telp'    => 'required|string|max:20',
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
        ]);

        // Default status aktif
        $validated['status'] = 'active';

        Pelanggan::create($validated);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Detail pelanggan
     */
    public function show(int $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('pelanggan.show', compact('pelanggan'));
    }

    /**
     * Form edit pelanggan
     */
    public function edit(int $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update pelanggan
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'Nama_Pelanggan' => 'required|string|max:255',
            'Alamat'        => 'required|string',
            'Nomor_Telp'    => 'required|string|max:20',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($validated);

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Nonaktifkan pelanggan
     */
    public function deactivate(int $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        // Ubah status menjadi inactive
        $pelanggan->status = 'inactive';
        $pelanggan->save();

        return redirect()->route('pelanggan.index')
            ->with('success', 'Pelanggan berhasil dinonaktifkan.');
    }
}
