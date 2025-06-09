<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        return Pelanggan::all();
    }

    public function show($id)
    {
        return Pelanggan::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Nama_Pelanggan' => 'required',
            'Nomor_Telp' => 'required',
            'Alamat' => 'required',
            'Status' => 'required',
        ]);
        return Pelanggan::create($data);
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->all());
        return $pelanggan;
    }

    public function destroy($id)
    {
        Pelanggan::destroy($id);
        return response()->noContent();
    }
} 