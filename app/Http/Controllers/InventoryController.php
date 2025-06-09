<?php

namespace App\Http\Controllers;


use App\Models\Barang;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryController extends Controller
{
    public function index()
    {
        $items = Barang::with('kategori')->get();

        return view('inventory.index', compact('items'));
    }

    public function create()
{
    return view('inventory.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'Nama_Bahan' => 'required|string|max:100',
        'Stok' => 'required|integer',
        'Jenis' => 'required|in:Bahan_Baku,Produk',
        'Status' => 'required|string|max:60',
        'kategori_Id_Kategori' => 'required|exists:kategori,Id_Kategori',
    ]);

    Barang::create($validated);

    return redirect()->route('inventory.index')->with('success', 'Item successfully added!');
}

public function exportPdf()
    {
        $items = Barang::with('kategori')->get();

        $pdf = Pdf::loadView('inventory.export_pdf', compact('items'));

        return $pdf->download('inventory_export.pdf');
    }


}
