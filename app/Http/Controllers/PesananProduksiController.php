<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesananProduksi;
use App\Models\User;
use App\Models\Pelanggan;

class PesananProduksiController extends Controller
{
    // Tampilkan semua data pesanan produksi
    public function index()
    {
        $pesanan = PesananProduksi::with(['user', 'pelanggan'])->get();
        return view('pesanan_produksi.index', compact('pesanan'));
    }

    // Tampilkan form untuk membuat pesanan produksi baru
    public function create()
    {
        $users = User::all();
        $pelanggans = Pelanggan::all();
        return view('pesanan_produksi.create', compact('users', 'pelanggans'));
    }

    // Simpan data pesanan produksi baru
    public function store(Request $request)
    {
        $request->validate([
            'Jumlah_Pesanan' => 'required|numeric',
            'Status' => 'required|string',
            'Tanggal_Pesanan' => 'required|date',
            'user_Id_User' => 'required|exists:users,Id_User',
            'pelanggan_Id_Pelanggan' => 'required|exists:pelanggans,Id_Pelanggan',
            'Surat_Perintah_Produksi' => 'nullable|string',
        ]);

        PesananProduksi::create($request->all());

        return redirect()->route('pesananproduksi.index')->with('success', 'Pesanan produksi berhasil ditambahkan.');
    }

    // Tampilkan detail pesanan produksi
    public function show($id)
    {
        $pesanan = PesananProduksi::with(['user', 'pelanggan'])->findOrFail($id);
        return view('pesanan_produksi.show', compact('pesanan'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $pesanan = PesananProduksi::findOrFail($id);
        $users = User::all();
        $pelanggans = Pelanggan::all();
        return view('pesanan_produksi.edit', compact('pesanan', 'users', 'pelanggans'));
    }

    // Proses update data
    public function update(Request $request, $id)
    {
        $request->validate([
            'Jumlah_Pesanan' => 'required|numeric',
            'Status' => 'required|string',
            'Tanggal_Pesanan' => 'required|date',
            'user_Id_User' => 'required|exists:users,Id_User',
            'pelanggan_Id_Pelanggan' => 'required|exists:pelanggans,Id_Pelanggan',
            'Surat_Perintah_Produksi' => 'nullable|string',
        ]);

        $pesanan = PesananProduksi::findOrFail($id);
        $pesanan->update($request->all());

        return redirect()->route('pesananproduksi.index')->with('success', 'Pesanan produksi berhasil diperbarui.');
    }

    // Hapus data
    public function destroy($id)
    {
        $pesanan = PesananProduksi::findOrFail($id);
        $pesanan->delete();

        return redirect()->route('pesananproduksi.index')->with('success', 'Pesanan produksi berhasil dihapus.');
    }
}
