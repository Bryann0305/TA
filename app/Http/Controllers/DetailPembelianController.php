<?php

namespace App\Http\Controllers;

use App\Models\DetailPembelian;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\Pembelian;
use Illuminate\Http\Request;

class DetailPembelianController extends Controller
{

    public function receive($purchaseId, $detailId)
{
    $detail = DetailPembelian::findOrFail($detailId);

    if ($detail->Status_Penerimaan === 'Pending') {
        // Update stok barang
        $barang = Barang::find($detail->bahan_baku_Id_Bahan);
        if ($barang) {
            $barang->Stok += $detail->Jumlah;
            $barang->save();
        }

        // Update status detail
        $detail->Status_Penerimaan = 'Diterima';
        $detail->save();

        // Update status pembelian kalau semua sudah diterima
        $pembelian = Pembelian::with('detailPembelian')->find($purchaseId);
        if ($pembelian && $pembelian->detailPembelian->every(fn($d) => $d->Status_Penerimaan === 'Diterima')) {
            $pembelian->Status_Penerimaan = 'Diterima';
            $pembelian->save();
        }
    }

    return redirect()->route('procurement.show', $purchaseId)
                     ->with('success', 'Barang berhasil diterima!');
}

    public function store(Request $request, $pembelianId)
    {
        $validated = $request->validate([
            // sesuaikan nama kolom PK di tabel barang (Id_Bahan atau Id_Barang)
            'bahan_baku_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'gudang_Id_Gudang'    => 'required|exists:gudang,Id_Gudang',
            'Jumlah'              => 'required|integer|min:1',
            'Harga_Keseluruhan'   => 'required|numeric|min:0',
            'Keterangan'          => 'nullable|string',
        ]);

        $validated['pembelian_Id_Pembelian'] = $pembelianId;
        $validated['Status_Penerimaan'] = 'Pending';

        DetailPembelian::create($validated);

        return redirect()->route('procurement.show', $pembelianId)
                         ->with('success', 'Detail pembelian berhasil ditambahkan!');
    }

    /**
     * Toggle status penerimaan per detail
     */
    public function toggleReceiving($id)
    {
        $detail = DetailPembelian::findOrFail($id);
        $barang = Barang::find($detail->bahan_baku_Id_Bahan);

        if ($detail->Status_Penerimaan === 'Pending') {
            // Tambah stok
            if ($barang) {
                $barang->Stok += $detail->Jumlah;
                $barang->save();
            }
            $detail->Status_Penerimaan = 'Diterima';
        } else {
            // Prevent changing from "Diterima" (Complete) back to "Pending"
            return redirect()->back()->with('error', 'Tidak dapat mengubah status dari Complete kembali ke Pending!');
        }

        $detail->save();

        return redirect()->back()->with('success', 'Status penerimaan berhasil diperbarui!');
    }
}
