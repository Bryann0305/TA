<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Gudang;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    // Daftar semua gudang
    public function index()
    {
        $gudangs = Gudang::all();
        return view('inventory.index', compact('gudangs'));
    }

    // Detail inventory per gudang
    public function showGudang(Request $request, $id)
    {
        $gudang = Gudang::with('inventories.kategori')->findOrFail($id);
        $items = $gudang->inventories();

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $items = $items->where(function ($q) use ($search) {
                $q->where('Nama_Bahan', 'like', "%$search%")
                  ->orWhere('Id_Bahan', 'like', "%$search%");
            });
        }

        // Filter kategori
        if ($request->filled('category')) {
            $items = $items->whereHas('kategori', function ($q) use ($request) {
                $q->where('Nama_Kategori', $request->category);
            });
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $items = $items->where('Jenis', $request->jenis);
        }

        $items = $items->get();

        // Hitung status otomatis
        foreach ($items as $item) {
            $item->Status = $this->getStatus($item->Stok, $item->ROP);
        }

        return view('inventory.gudang_detail', compact('gudang', 'items'));
    }

    // Tampilkan detail barang
    public function show($id)
    {
        $item = Barang::with(['kategori', 'gudang'])->findOrFail($id);

        $stok_kg = $item->Stok * $item->Berat;
        $rop_kg = $item->ROP * $item->Berat;
        $eoq_kg = $item->EOQ * $item->Berat;

        return view('inventory.show', compact('item', 'stok_kg', 'rop_kg', 'eoq_kg'));
    }

    // Form edit barang
    public function edit($id)
    {
        $item = Barang::findOrFail($id);
        $kategori = Kategori::all();
        $gudangs = Gudang::all();
        return view('inventory.edit', compact('item', 'kategori', 'gudangs'));
    }

    // Update barang
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);

        $barang = Barang::findOrFail($id);
        $barang->update($validated);

        return redirect()->route('inventory.showGudang', $barang->gudang_Id_Gudang)
                         ->with('success', 'Item berhasil diperbarui!');
    }

    // Hapus barang
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $gudangId = $barang->gudang_Id_Gudang;
        $barang->delete();

        return redirect()->route('inventory.showGudang', $gudangId)
                         ->with('success', 'Item berhasil dihapus!');
    }

    // Validasi request & set status otomatis
    private function validateRequest(Request $request)
    {
        $validated = $request->validate([
            'Nama_Bahan' => 'required|string|max:100',
            'Stok' => 'required|numeric|min:0',
            'Jenis' => 'required|in:Bahan_Baku,Produk',
            'kategori_Id_Kategori' => 'required|exists:kategori,Id_Kategori',
            'gudang_Id_Gudang' => 'required|exists:gudang,Id_Gudang',
            'EOQ' => 'nullable|numeric|min:0',
            'ROP' => 'nullable|numeric|min:0',
            'Unit' => 'required|string|max:20',
            'Berat' => 'required|numeric|min:0',
            'Satuan' => 'required|string|max:10',
        ]);

        $validated['EOQ'] = $validated['EOQ'] ?? 0;
        $validated['ROP'] = $validated['ROP'] ?? 100;

        $validated['Status'] = $this->getStatus($validated['Stok'], $validated['ROP']);

        return $validated;
    }

    // Tentukan status stok
    private function getStatus($stok, $rop)
    {
        if ($stok <= $rop / 2) {
            return 'Critical Low';
        } elseif ($stok < $rop) {
            return 'Below Reorder Point';
        } else {
            return 'In Stock';
        }
    }
}
