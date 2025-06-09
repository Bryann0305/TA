<?php

namespace App\Http\Controllers;

use App\Models\Penjadwalan;
use Illuminate\Http\Request;

class PenjadwalanController extends Controller
{
    public function index()
    {
        return Penjadwalan::all();
    }

    public function show($id)
    {
        return Penjadwalan::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Tanggal_Mulai' => 'required|date',
            'Tanggal_Selesai' => 'required|date',
            'Status' => 'required',
        ]);
        return Penjadwalan::create($data);
    }

    public function update(Request $request, $id)
    {
        $jadwal = Penjadwalan::findOrFail($id);
        $jadwal->update($request->all());
        return $jadwal;
    }

    public function destroy($id)
    {
        Penjadwalan::destroy($id);
        return response()->noContent();
    }
} 