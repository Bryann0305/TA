<?php

namespace App\Http\Controllers;

use App\Models\{
    Produksi,
    BillOfMaterial,
    Barang,
    PesananProduksi,
    Penjadwalan
};
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function index()
{
    $today = now()->startOfDay();

    $produksiBerjalan = Produksi::with([
            'pesananProduksi.pelanggan',
            'billOfMaterial.barang',
            'jadwal'
        ])
        ->whereDate('Tanggal_Produksi', '<=', $today)
        ->where('Status', '!=', 'Selesai')
        ->orderBy('Tanggal_Produksi', 'desc')
        ->get();

    $produksiDirencanakan = Produksi::with([
            'pesananProduksi.pelanggan',
            'billOfMaterial.barang',
            'jadwal'
        ])
        ->whereDate('Tanggal_Produksi', '>', $today)
        ->orderBy('Tanggal_Produksi', 'asc')
        ->get();

    $produksiSelesai = Produksi::with([
            'pesananProduksi.pelanggan',
            'billOfMaterial.barang',
            'jadwal'
        ])
        ->where('Status', 'Selesai')
        ->orderBy('Tanggal_Produksi', 'desc')
        ->get();

    // BOM list untuk bawahnya
    $boms = BillOfMaterial::with('barang')->get();

    return view('production.index', compact(
        'produksiBerjalan',
        'produksiDirencanakan',
        'produksiSelesai',
        'boms'
    ));
}

    public function create($pesanan_id = null)
    {
        $pesanan = null;
        if ($pesanan_id) {
            $pesanan = PesananProduksi::with('pelanggan')->findOrFail($pesanan_id);
        }

        $boms = BillOfMaterial::where('Status', 'approved')->get();
        $barang = Barang::all();
        $jadwal = Penjadwalan::all();

        return view('production.create', compact('pesanan', 'boms', 'barang', 'jadwal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Hasil_Produksi' => 'required|string|max:255',
            'Status' => 'required|in:Menunggu,Berjalan,Selesai',
            'Keterangan' => 'nullable|string',
            'bill_of_material_Id_bill_of_material' => 'required|exists:bill_of_material,Id_bill_of_material',
            'bahan_baku_Id_Bahan' => 'nullable|exists:barang,Id_Bahan',
            'pesanan_produksi_Id_Pesanan' => 'nullable|exists:pesanan_produksi,Id_Pesanan',
            'penjadwalan_Id_Jadwal' => 'nullable|exists:penjadwalan,Id_Jadwal',
        ]);

        $produksi = Produksi::create([
            'Hasil_Produksi' => $request->Hasil_Produksi,
            'Status' => $request->Status,
            'Tanggal_Produksi' => now(),
            'Keterangan' => $request->Keterangan ?? '',
            'Jumlah_Berhasil' => 0,
            'Jumlah_Gagal' => 0,
            'bahan_baku_Id_Bahan' => $request->bahan_baku_Id_Bahan,
            'pesanan_produksi_Id_Pesanan' => $request->pesanan_produksi_Id_Pesanan,
            'penjadwalan_Id_Jadwal' => $request->penjadwalan_Id_Jadwal,
            'bill_of_material_Id_bill_of_material' => $request->bill_of_material_Id_bill_of_material,
        ]);

        // Update status pesanan menjadi "Diproses" jika ada
        if ($request->pesanan_produksi_Id_Pesanan) {
            PesananProduksi::where('Id_Pesanan', $request->pesanan_produksi_Id_Pesanan)
                ->update(['Status' => 'Diproses']);
        }

        return redirect()->route('production.index')->with('success', 'Produksi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $produksi = Produksi::with(['billOfMaterial', 'pesananProduksi', 'jadwal'])->findOrFail($id);

        if ($produksi->Status === 'Selesai') {
            return redirect()->route('production.index')->with('error', 'Produksi yang sudah selesai tidak dapat diubah.');
        }

        $boms = BillOfMaterial::where('Status', 'approved')->get();
        $barang = Barang::all();
        $pesanan = PesananProduksi::all();
        $jadwal = Penjadwalan::all();

        return view('production.edit', compact('produksi', 'boms', 'barang', 'pesanan', 'jadwal'));
    }

    public function update(Request $request, $id)
    {
        $produksi = Produksi::with('pesananProduksi')->findOrFail($id);

        if ($produksi->Status === 'Selesai') {
            return back()->with('error', 'Produksi yang sudah selesai tidak dapat diubah.');
        }

        $request->validate([
            'Nama_Produksi' => 'required|string|max:255',
            'Hasil_Produksi' => 'required|string|max:255',
            'Jumlah_Berhasil' => 'required|numeric|min:0',
            'Jumlah_Gagal' => 'required|numeric|min:0',
            'Keterangan' => 'nullable|string',
            'Status' => 'required|in:Menunggu,Berjalan,Selesai',
            'bill_of_material_Id_bill_of_material' => 'required|exists:bill_of_material,Id_bill_of_material',
            'bahan_baku_Id_Bahan' => 'nullable|exists:barang,Id_Bahan',
            'pesanan_produksi_Id_Pesanan' => 'nullable|exists:pesanan_produksi,Id_Pesanan',
            'penjadwalan_Id_Jadwal' => 'nullable|exists:penjadwalan,Id_Jadwal',
        ]);

        $jumlahBerhasil = $request->Jumlah_Berhasil;
        $jumlahGagal = $request->Jumlah_Gagal;
        $jumlahProduksi = $jumlahBerhasil + $jumlahGagal;

        if ($produksi->pesananProduksi && $jumlahProduksi > $produksi->pesananProduksi->Jumlah_Pesanan) {
            return back()->withErrors(['Jumlah_Berhasil' => 'Total produksi (berhasil + gagal) melebihi jumlah pesanan']);
        }

        $produksi->update([
            'Nama_Produksi' => $request->Nama_Produksi,
            'Hasil_Produksi' => $request->Hasil_Produksi,
            'Jumlah_Produksi' => $jumlahProduksi,
            'Jumlah_Berhasil' => $jumlahBerhasil,
            'Jumlah_Gagal' => $jumlahGagal,
            'Keterangan' => $request->Keterangan ?? '',
            'Status' => $request->Status,
            'bill_of_material_Id_bill_of_material' => $request->bill_of_material_Id_bill_of_material,
            'bahan_baku_Id_Bahan' => $request->bahan_baku_Id_Bahan,
            'pesanan_produksi_Id_Pesanan' => $request->pesanan_produksi_Id_Pesanan,
            'penjadwalan_Id_Jadwal' => $request->penjadwalan_Id_Jadwal,
        ]);

        return redirect()->route('production.index')->with('success', 'Produksi berhasil diperbarui.');
    }

    public function show($id)
    {
        $produksi = Produksi::with(['billOfMaterial', 'pesananProduksi', 'jadwal'])->findOrFail($id);
        return response()->json($produksi);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['Status' => 'required|in:Menunggu,Berjalan,Selesai']);

        $produksi = Produksi::findOrFail($id);

        if ($produksi->Status === 'Selesai') {
            return back()->with('error', 'Status sudah selesai dan tidak dapat diubah.');
        }

        $produksi->update(['Status' => $request->Status]);

        return redirect()->route('production.index')->with('success', 'Status produksi berhasil diperbarui.');
    }
}
