<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggan = Pelanggan::all();
        return view('pelanggan.index', compact('pelanggan'));
    }

    public function create()
    {
        return view('pelanggan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_Pelanggan' => 'required|string|max:255',
            'Alamat' => 'required|string',
            'Nomor_Telp' => 'required|string|max:20',
        ]);

        // Jika form tidak ada input Status, set default 'Aktif'
        $data = $request->only('Nama_Pelanggan', 'Alamat', 'Nomor_Telp');
        $data['Status'] = $request->input('Status', 'Aktif');

        Pelanggan::create($data);

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        return view('pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Pelanggan' => 'required|string|max:255',
            'Alamat' => 'required|string',
            'Nomor_Telp' => 'required|string|max:20',
            'Status' => 'required|string',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->only('Nama_Pelanggan', 'Alamat', 'Nomor_Telp', 'Status'));

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Pelanggan::findOrFail($id)->delete();
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->Status = $pelanggan->Status === 'Aktif' ? 'Tidak Aktif' : 'Aktif';
        $pelanggan->save();

        return redirect()->route('pelanggan.index')->with('success', 'Status pelanggan berhasil diperbarui.');
    }
}
