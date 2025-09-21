<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PesananProduksi;
use App\Models\DetailPesananProduksi;
use App\Models\Barang;
use App\Models\Pelanggan;

class PesananProduksiController extends Controller
{
    /**
     * Tampilkan semua pesanan produksi
     */
    public function index()
    {
        $pesanan = PesananProduksi::with(['pelanggan', 'detail.barang'])->get();
        return view('pesanan-produksi.index', compact('pesanan'));
    }

    /**
     * Form buat pesanan produksi baru
     */
    public function create()
{
    // hanya ambil barang dengan Jenis = Produk
    $barangs = Barang::where('Jenis', 'Produk')->get();
    $pelanggans = Pelanggan::all();

    return view('pesanan-produksi.create', compact('barangs', 'pelanggans'));
}



    /**
     * Simpan pesanan produksi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Tanggal_Pesanan' => 'required|date',
            'pelanggan_Id_Pelanggan' => 'nullable|exists:pelanggan,Id_Pelanggan',
            'barang' => 'required|array',
            'barang.*.barang_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'barang.*.Jumlah' => 'required|numeric|min:1',
        ]);

        // Hitung total jumlah
        $totalJumlah = collect($validated['barang'])->sum('Jumlah');

        // Buat nomor pesanan otomatis
        $last = PesananProduksi::latest('Id_Pesanan')->first();
        $nomorPesanan = $last
            ? str_pad($last->Id_Pesanan + 1, 4, '0', STR_PAD_LEFT)
            : '0001';

        // Simpan pesanan
        $pesanan = PesananProduksi::create([
            'Nomor_Pesanan' => $nomorPesanan,
            'Jumlah_Pesanan' => $totalJumlah,
            'Tanggal_Pesanan' => $validated['Tanggal_Pesanan'],
            'Status' => 'pending',
            'user_Id_User' => Auth::id(),
            'pelanggan_Id_Pelanggan' => $validated['pelanggan_Id_Pelanggan'] ?? null,
        ]);

        // Simpan detail pesanan
        foreach ($validated['barang'] as $b) {
            DetailPesananProduksi::create([
                'pesanan_produksi_Id_Pesanan' => $pesanan->Id_Pesanan,
                'barang_Id_Bahan' => $b['barang_Id_Bahan'],
                'Jumlah' => $b['Jumlah'],
            ]);
        }

        return redirect()
            ->route('pesanan_produksi.index')
            ->with('success', 'Pesanan produksi berhasil dibuat!');
    }

    /**
     * Form edit pesanan produksi
     */
    public function edit($id)
{
    $pesanan = PesananProduksi::with('detail')->findOrFail($id);

    // hanya ambil barang dengan Jenis = Produk
    $barangs = Barang::where('Jenis', 'Produk')->get();
    $pelanggans = Pelanggan::all();

    return view('pesanan-produksi.edit', compact('pesanan', 'barangs', 'pelanggans'));
}


    /**
     * Update pesanan produksi
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Tanggal_Pesanan' => 'required|date',
            'pelanggan_Id_Pelanggan' => 'nullable|exists:pelanggan,Id_Pelanggan',
            'barang' => 'required|array',
            'barang.*.barang_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'barang.*.Jumlah' => 'required|numeric|min:1',
        ]);

        $totalJumlah = collect($validated['barang'])->sum('Jumlah');

        $pesanan = PesananProduksi::findOrFail($id);
        $pesanan->update([
            'Jumlah_Pesanan' => $totalJumlah,
            'Tanggal_Pesanan' => $validated['Tanggal_Pesanan'],
            'pelanggan_Id_Pelanggan' => $validated['pelanggan_Id_Pelanggan'] ?? null,
            'user_Id_User' => Auth::id(),
        ]);

        // Hapus detail lama & simpan ulang
        DetailPesananProduksi::where('pesanan_produksi_Id_Pesanan', $id)->delete();

        foreach ($validated['barang'] as $b) {
            DetailPesananProduksi::create([
                'pesanan_produksi_Id_Pesanan' => $id,
                'barang_Id_Bahan' => $b['barang_Id_Bahan'],
                'Jumlah' => $b['Jumlah'],
            ]);
        }

        return redirect()
            ->route('pesanan_produksi.index')
            ->with('success', 'Pesanan Produksi berhasil diperbarui!');
    }

    /**
     * Hapus pesanan produksi
     */
    public function destroy($id)
    {
        PesananProduksi::findOrFail($id)->delete();

        return redirect()
            ->route('pesanan_produksi.index')
            ->with('success', 'Pesanan Produksi berhasil dihapus!');
    }

    /**
     * Halaman detail pesanan produksi
     */
    public function show($id)
    {
        $pesanan = PesananProduksi::with('detail.barang', 'pelanggan')->findOrFail($id);

        return view('pesanan-produksi.show', compact('pesanan'));
    }

    /**
     * Toggle status pesanan produksi
     */
    public function toggleStatus($id)
    {
        $pesanan = PesananProduksi::findOrFail($id);

        if ($pesanan->Status === 'pending') {
            $pesanan->Status = 'confirmed';
        } elseif ($pesanan->Status === 'confirmed') {
            $pesanan->Status = 'pending';
        }

        $pesanan->save();

        return redirect()
            ->route('pesanan_produksi.index')
            ->with('success', 'Status pesanan diperbarui!');
    }
}
