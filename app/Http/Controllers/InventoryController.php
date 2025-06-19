<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with('kategori');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Nama_Bahan', 'like', '%' . $search . '%')
                  ->orWhere('Id_Bahan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('Nama_Kategori', $request->category);
            });
        }

        if ($request->filled('jenis')) {
         $query->where('Jenis', $request->jenis);
        }


        $items = $query->get();

        return view('inventory.index', compact('items'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('inventory.create', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nama_Bahan' => 'required|string|max:100',
            'Stok' => 'required|integer',
            'Jenis' => 'required|in:Bahan_Baku,Produk',
            'Status' => 'required|in:Aktif,Tidak Aktif',
            'kategori_Id_Kategori' => 'required|exists:kategori,Id_Kategori',
            'EOQ' => 'nullable|integer',
            'Reorder_Point' => 'nullable|integer',
        ]);

        $validated['EOQ'] = $validated['EOQ'] ?? 0;
        $validated['Reorder_Point'] = $validated['Reorder_Point'] ?? 0;

        Barang::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Item berhasil ditambahkan!');
    }

    public function show($id)
    {
        $item = Barang::with('kategori')->findOrFail($id);
        return view('inventory.show', compact('item'));
    }

    public function edit($id)
    {
        $item = Barang::findOrFail($id);
        $kategori = Kategori::all();
        return view('inventory.edit', compact('item', 'kategori'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Nama_Bahan' => 'required|string|max:100',
            'Stok' => 'required|integer',
            'Jenis' => 'required|in:Bahan_Baku,Produk',
            'Status' => 'required|in:Aktif,Tidak Aktif',
            'kategori_Id_Kategori' => 'required|exists:kategori,Id_Kategori',
            'EOQ' => 'nullable|integer',
            'Reorder_Point' => 'nullable|integer',
        ]);

        $validated['EOQ'] = $validated['EOQ'] ?? 0;
        $validated['Reorder_Point'] = $validated['Reorder_Point'] ?? 0;

        $barang = Barang::findOrFail($id);
        $barang->update($validated);

        return redirect()->route('inventory.index')->with('success', 'Item berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('inventory.index')->with('success', 'Item berhasil dihapus!');
    }
}
