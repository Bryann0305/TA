<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesananProduksi;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Barang;

class PesananProduksiController extends Controller
{
    public function index()
    {
        $pesanan = PesananProduksi::with(['user', 'pelanggan', 'barang'])->get();
        return view('pesanan_produksi.index', compact('pesanan'));
    }

    public function create()
    {
        $users = User::all();
        $pelanggans = Pelanggan::all();
        $produks = Barang::all(); // daftar produk
        return view('pesanan_produksi.create', compact('users', 'pelanggans', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Jumlah_Pesanan' => 'required|integer|min:1',
            'Tanggal_Pesanan' => 'required|date',
            'Status' => 'required|in:Menunggu,Diproses,Selesai',
            'user_Id_User' => 'required|exists:users,Id_User',
            'pelanggan_Id_Pelanggan' => 'required|exists:pelanggans,Id_Pelanggan',
            'Surat_Perintah_Produksi' => 'nullable|string',
        ]);

        PesananProduksi::create([
            'Jumlah_Pesanan' => $request->Jumlah_Pesanan,
            'Tanggal_Pesanan' => $request->Tanggal_Pesanan,
            'Status' => $request->Status,
            'user_Id_User' => $request->user_Id_User,
            'pelanggan_Id_Pelanggan' => $request->pelanggan_Id_Pelanggan,
            'Surat_Perintah_Produksi' => $request->Surat_Perintah_Produksi,
        ]);

        return redirect()->route('pesanan_produksi.index')
            ->with('success', 'Pesanan produksi berhasil ditambahkan.');
    }

    // method lainnya tetap seperti sebelumnya...

    public function show($id)
    {
        $pesanan = PesananProduksi::with(['user', 'pelanggan'])->findOrFail($id);
        return view('pesanan_produksi.show', compact('pesanan'));
    }

    public function edit($id)
    {
        $pesanan = PesananProduksi::findOrFail($id);
        $users = User::all();
        $pelanggans = Pelanggan::all();
        return view('pesanan_produksi.edit', compact('pesanan', 'users', 'pelanggans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Jumlah_Pesanan' => 'required|numeric',
            'Status' => 'required|in:Menunggu,Diproses,Selesai',
            'Tanggal_Pesanan' => 'required|date',
            'user_Id_User' => 'required|exists:users,Id_User',
            'pelanggan_Id_Pelanggan' => 'required|exists:pelanggans,Id_Pelanggan',
            'Surat_Perintah_Produksi' => 'nullable|string',
        ]);

        $pesanan = PesananProduksi::findOrFail($id);
        $pesanan->update($request->all());

        return redirect()->route('pesananp_roduksi.index')->with('success', 'Pesanan produksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pesanan = PesananProduksi::findOrFail($id);
        $pesanan->delete();

        return redirect()->route('pesanan_produksi.index')->with('success', 'Pesanan produksi berhasil dihapus.');
    }
}
