<?php

namespace App\Http\Controllers;

use App\Models\DetailPembelian;
use Illuminate\Http\Request;

class DetailPembelianController extends Controller
{
    public function index()
    {
        return DetailPembelian::all();
    }

    public function show($id)
    {
        return DetailPembelian::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Harga_Keseluruhan' => 'required|numeric',
            'Jumlah' => 'required|integer',
            'Keterangan' => 'nullable',
            'pembelian_Id_Pembelian' => 'required|integer',
            'gudang_Id_Gudang' => 'required|integer',
            'bahan_baku_Id_Bahan' => 'required|integer',
        ]);
        return DetailPembelian::create($data);
    }

    public function update(Request $request, $id)
    {
        $detail = DetailPembelian::findOrFail($id);
        $detail->update($request->all());
        return $detail;
    }

    public function destroy($id)
    {
        DetailPembelian::destroy($id);
        return response()->noContent();
    }
} 