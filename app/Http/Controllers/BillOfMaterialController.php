<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillOfMaterial;
use App\Models\BarangHasBillOfMaterial;
use App\Models\Barang;

class BillOfMaterialController extends Controller
{
    // Tampilkan semua BOM
    public function index()
    {
        $boms = BillOfMaterial::with('barangs')->get();
        return view('bill-of-materials.index', compact('boms'));
    }

    // Form tambah BOM
    public function create()
    {
        $barangs = Barang::all();
        return view('bill-of-materials.create', compact('barangs'));
    }

    // Simpan BOM baru (status default: pending)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nama_bill_of_material' => 'required|string|max:100',
            'barang' => 'required|array',
            'barang.*.barang_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'barang.*.Jumlah_Bahan' => 'required|numeric|min:0',
        ]);

        $bom = BillOfMaterial::create([
            'Nama_bill_of_material' => $validated['Nama_bill_of_material'],
            'Status' => 'pending', // default pending
        ]);

        foreach ($validated['barang'] as $b) {
            BarangHasBillOfMaterial::create([
                'barang_Id_Bahan' => $b['barang_Id_Bahan'],
                'bill_of_material_Id_bill_of_material' => $bom->Id_bill_of_material,
                'Jumlah_Bahan' => $b['Jumlah_Bahan']
            ]);
        }

        return redirect()->route('bom.index')->with('success', 'BOM berhasil dibuat, menunggu persetujuan admin!');
    }

    // Tampilkan detail BOM
    public function show($id)
    {
        $bom = BillOfMaterial::with('barangs')->findOrFail($id);
        return view('bill-of-materials.show', compact('bom'));
    }

    // Form edit BOM
    public function edit($id)
    {
        $bom = BillOfMaterial::with('barangs')->findOrFail($id);
        $barangs = Barang::all();
        return view('bill-of-materials.edit', compact('bom', 'barangs'));
    }

    // Update BOM
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'Nama_bill_of_material' => 'required|string|max:100',
            'barang' => 'required|array',
            'barang.*.barang_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'barang.*.Jumlah_Bahan' => 'required|numeric|min:0',
        ]);

        $bom = BillOfMaterial::findOrFail($id);
        $bom->update([
            'Nama_bill_of_material' => $validated['Nama_bill_of_material'],
        ]);

        // Hapus mapping lama
        BarangHasBillOfMaterial::where('bill_of_material_Id_bill_of_material', $id)->delete();

        // Tambahkan mapping baru
        foreach ($validated['barang'] as $b) {
            BarangHasBillOfMaterial::create([
                'barang_Id_Bahan' => $b['barang_Id_Bahan'],
                'bill_of_material_Id_bill_of_material' => $bom->Id_bill_of_material,
                'Jumlah_Bahan' => $b['Jumlah_Bahan']
            ]);
        }

        return redirect()->route('bom.index')->with('success', 'BOM berhasil diperbarui!');
    }

    // Hapus BOM
    public function destroy($id)
    {
        BarangHasBillOfMaterial::where('bill_of_material_Id_bill_of_material', $id)->delete();
        BillOfMaterial::findOrFail($id)->delete();

        return redirect()->route('bom.index')->with('success', 'BOM berhasil dihapus!');
    }

    // Approve BOM (ubah status pending → confirmed)
    public function approve($id)
    {
        $bom = BillOfMaterial::findOrFail($id);
        $bom->Status = 'confirmed';
        $bom->save();

        return redirect()->route('bom.index')->with('success', 'BOM berhasil dikonfirmasi!');
    }
}
