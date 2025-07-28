<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesananProduksi;
use App\Models\User;
use App\Models\Pelanggan;

class PesananProduksiController extends Controller
{
    public function index()
    {
        $pesanan = PesananProduksi::with(['user', 'pelanggan'])->get();
        return view('pesanan_produksi.index', compact('pesanan'));
    }

    public function create()
    {
        $users = User::all();
        $pelanggans = Pelanggan::all();
        return view('pesanan_produksi.create', compact('users', 'pelanggans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Jumlah_Pesanan' => 'required|numeric',
            'Status' => 'required|in:Menunggu,Diproses,Selesai',
            'Tanggal_Pesanan' => 'required|date',
            'user_Id_User' => 'required|exists:users,Id_User',
            'pelanggan_Id_Pelanggan' => 'required|exists:pelanggans,Id_Pelanggan',
            'Surat_Perintah_Produksi' => 'nullable|string',
        ]);

        PesananProduksi::create($request->all());

        return redirect()->route('pesananproduksi.index')->with('success', 'Pesanan produksi berhasil ditambahkan.');
    }

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

        return redirect()->route('pesananproduksi.index')->with('success', 'Pesanan produksi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $pesanan = PesananProduksi::findOrFail($id);
        $pesanan->delete();

        return redirect()->route('pesananproduksi.index')->with('success', 'Pesanan produksi berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $pesanan = PesananProduksi::findOrFail($id);
        if ($pesanan->Status === 'On Progress') {
            $pesanan->Status = 'Completed';
        } else {
            $pesanan->Status = 'On Progress';
        }
        $pesanan->save();
        return redirect()->route('pesanan-produksi.index')->with('success', 'Status pesanan berhasil diubah.');
    }
}
