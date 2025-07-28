<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjadwalan;
use App\Models\PesananProduksi;

class PenjadwalanController extends Controller
{
    public function create()
    {
        $pesananList = PesananProduksi::whereNull('Surat_Perintah_Produksi')->get();
        return view('penjadwalan.create', compact('pesananList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Tanggal_Mulai' => 'required|date',
            'Tanggal_Selesai' => 'required|date|after_or_equal:Tanggal_Mulai',
            'Status' => 'required|in:Menunggu,Berjalan,Selesai',
            'pesanan_produksi_Id_Pesanan' => 'required|exists:pesanan_produksi,Id_Pesanan',
        ]);

        $jadwal = Penjadwalan::create([
            'Tanggal_Mulai' => $request->Tanggal_Mulai,
            'Tanggal_Selesai' => $request->Tanggal_Selesai,
            'Status' => $request->Status,
            'pesanan_produksi_Id_Pesanan' => $request->pesanan_produksi_Id_Pesanan,
        ]);

        // Update pesanan produksi
        $pesanan = PesananProduksi::findOrFail($request->pesanan_produksi_Id_Pesanan);
        $pesanan->Surat_Perintah_Produksi = 'Jadwal ID #' . $jadwal->Id_Jadwal;
        $pesanan->save();

        return redirect()->route('production.index')->with('success', 'Penjadwalan berhasil ditambahkan.');
    }
}
