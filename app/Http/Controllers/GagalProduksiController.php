<?php

namespace App\Http\Controllers;

use App\Models\GagalProduksi;
use Illuminate\Http\Request;

class GagalProduksiController extends Controller
{
    public function index()
    {
        return GagalProduksi::all();
    }

    public function show($id)
    {
        return GagalProduksi::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Total_Gagal' => 'required|integer',
            'Keterangan' => 'nullable',
            'produksi_Id_Produksi' => 'required|integer',
        ]);
        return GagalProduksi::create($data);
    }

    public function update(Request $request, $id)
    {
        $gagal = GagalProduksi::findOrFail($id);
        $gagal->update($request->all());
        return $gagal;
    }

    public function destroy($id)
    {
        GagalProduksi::destroy($id);
        return response()->noContent();
    }
} 