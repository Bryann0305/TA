<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        return Notifikasi::all();
    }

    public function show($id)
    {
        return Notifikasi::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'Pesan' => 'required',
            'Status' => 'required',
            'user_Id_User' => 'required|integer',
        ]);
        return Notifikasi::create($data);
    }

    public function update(Request $request, $id)
    {
        $notifikasi = Notifikasi::findOrFail($id);
        $notifikasi->update($request->all());
        return $notifikasi;
    }

    public function destroy($id)
    {
        Notifikasi::destroy($id);
        return response()->noContent();
    }
} 