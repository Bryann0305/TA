<?php

namespace App\Http\Controllers;

use App\Models\DetailPembelian;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Pembelian;
use Illuminate\Http\Request;

class DetailPembelianController extends Controller
{
    public function create($pembelianId)
    {
        $pembelian = Pembelian::findOrFail($pembelianId);
        $barang = Barang::all();
        $gudang = Gudang::all();

        return view('detail-pembelian.create', compact('pembelian', 'barang', 'gudang'));
    }

    public function store(Request $request, $pembelianId)
    {
        $validated = $request->validate([
            'bahan_baku_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'gudang_Id_Gudang' => 'required|exists:gudang,Id_Gudang',
            'Jumlah' => 'required|integer|min:1',
            'Harga_Keseluruhan' => 'required|numeric|min:0',
            'Keterangan' => 'nullable|string',
        ]);

        $validated['pembelian_Id_Pembelian'] = $pembelianId;

        DetailPembelian::create($validated);

        return redirect()->route('procurement.show', $pembelianId)
                         ->with('success', 'Detail pembelian berhasil ditambahkan!');
    }
}
