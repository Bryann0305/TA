<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangController extends Controller
{
    public function index()
    {
        return Gudang::all();
    }

    public function show($id)
    {
        return Gudang::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Lokasi' => 'required',
            'Kapasitas' => 'required|integer',
        ]);
        return Gudang::create($data);
    }

    public function update(Request $request, $id)
    {
        $gudang = Gudang::findOrFail($id);
        $gudang->update($request->all());
        return $gudang;
    }

    public function destroy($id)
    {
        Gudang::destroy($id);
        return response()->noContent();
    }
} 