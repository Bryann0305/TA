<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Tampilkan semua barang dengan filter pencarian
     */
    public function index(Request $request)
    {
        $query = Barang::with('kategori');

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Nama_Bahan', 'like', '%' . $search . '%')
                  ->orWhere('Id_Bahan', 'like', '%' . $search . '%');
            });
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('Nama_Kategori', $request->category);
            });
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('Jenis', $request->jenis);
        }

        $items = $query->get();
        return view('inventory.index', compact('items'));
    }

    /**
     * Form tambah barang
     */
    public function create()
    {
        $kategori = Kategori::all();
        return view('inventory.create', compact('kategori'));
    }

    /**
     * Simpan barang baru
     */
    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        Barang::create($validated);

        return redirect()->route('inventory.index')
                         ->with('success', 'Item berhasil ditambahkan!');
    }

    /**
     * Lihat detail barang
     */
    public function show($id)
    {
        $item = Barang::with('kategori')->findOrFail($id);

        // Konversi stok, ROP, EOQ ke kg/liter
        $stok_kg = $item->Stok * $item->Berat;
        $rop_kg = $item->ROP * $item->Berat;
        $eoq_kg = $item->EOQ * $item->Berat;

        return view('inventory.show', compact('item', 'stok_kg', 'rop_kg', 'eoq_kg'));
    }

    /**
     * Form edit barang
     */
    public function edit($id)
    {
        $item = Barang::findOrFail($id);
        $kategori = Kategori::all();
        return view('inventory.edit', compact('item', 'kategori'));
    }

    /**
     * Update barang
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);

        $barang = Barang::findOrFail($id);
        $barang->update($validated);

        return redirect()->route('inventory.index')
                         ->with('success', 'Item berhasil diperbarui!');
    }

    /**
     * Hapus barang
     */
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('inventory.index')
                         ->with('success', 'Item berhasil dihapus!');
    }

    /**
     * Validasi request & set status otomatis
     */
    private function validateRequest(Request $request)
    {
        $validated = $request->validate([
            'Nama_Bahan' => 'required|string|max:100',
            'Stok' => 'required|numeric|min:0',
            'Jenis' => 'required|in:Bahan_Baku,Produk',
            'kategori_Id_Kategori' => 'required|exists:kategori,Id_Kategori',
            'EOQ' => 'nullable|numeric|min:0',
            'ROP' => 'nullable|numeric|min:0',
            'Unit' => 'required|string|max:20',
            'Berat' => 'required|numeric|min:0',
            'Satuan' => 'required|string|max:10',
        ]);

        // Default value kalau kosong
        $validated['EOQ'] = $validated['EOQ'] ?? 0;
        $validated['ROP'] = $validated['ROP'] ?? 100;

        // Hitung status otomatis
        $validated['Status'] = $this->getStatus($validated['Stok'], $validated['ROP']);

        return $validated;
    }

    /**
     * Tentukan status stok berdasarkan Reorder Point
     */
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

    /**
     * Tampilkan status inventory (opsional)
     */
    public function inventoryStatus()
    {
        $barangList = DB::table('barang')->get();
        foreach ($barangList as $barang) {
            $barang->Status = $this->getStatus($barang->Stok, $barang->ROP);
        }

        return view('inventory.status', compact('barangList'));
    }
}
