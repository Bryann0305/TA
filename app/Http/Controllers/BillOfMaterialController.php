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
        // Get Finished Goods (Produk) and Raw Materials (Bahan Baku) separately
        $finishedGoods = Barang::where('Jenis', 'Produk')->get();
        $rawMaterials = Barang::where('Jenis', 'Bahan_Baku')->get();
        
        return view('bill-of-materials.create', compact('finishedGoods', 'rawMaterials'));
    }

    // Simpan BOM baru (status default: pending)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'finished_good_id' => 'required|exists:barang,Id_Bahan',
            'raw_materials' => 'required|array|min:1',
            'raw_materials.*.barang_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'raw_materials.*.Jumlah_Bahan' => 'required|numeric|min:0',
        ]);

        // Get finished good name for BOM name
        $finishedGood = Barang::find($validated['finished_good_id']);
        $bomName = 'BOM - ' . $finishedGood->Nama_Bahan;

        $bom = BillOfMaterial::create([
            'Nama_bill_of_material' => $bomName,
            'Status' => 'pending', // default pending
        ]);

        // Add only raw materials (Finished Good is only used for BOM name)
        foreach ($validated['raw_materials'] as $rawMaterial) {
            BarangHasBillOfMaterial::create([
                'barang_Id_Bahan' => $rawMaterial['barang_Id_Bahan'],
                'bill_of_material_Id_bill_of_material' => $bom->Id_bill_of_material,
                'Jumlah_Bahan' => $rawMaterial['Jumlah_Bahan']
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
        $finishedGoods = Barang::where('Jenis', 'Produk')->get();
        $rawMaterials = Barang::where('Jenis', 'Bahan_Baku')->get();
        return view('bill-of-materials.edit', compact('bom', 'finishedGoods', 'rawMaterials'));
    }

    // Update BOM
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'finished_good_id' => 'required|exists:barang,Id_Bahan',
            'raw_materials' => 'required|array|min:1',
            'raw_materials.*.barang_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'raw_materials.*.Jumlah_Bahan' => 'required|numeric|min:0',
        ]);

        // Get finished good name for BOM name
        $finishedGood = Barang::find($validated['finished_good_id']);
        $bomName = 'BOM - ' . $finishedGood->Nama_Bahan;

        $bom = BillOfMaterial::findOrFail($id);
        $bom->update([
            'Nama_bill_of_material' => $bomName,
        ]);

        // Hapus mapping lama
        BarangHasBillOfMaterial::where('bill_of_material_Id_bill_of_material', $id)->delete();

        // Add only raw materials (Finished Good is only used for BOM name)
        foreach ($validated['raw_materials'] as $rawMaterial) {
            BarangHasBillOfMaterial::create([
                'barang_Id_Bahan' => $rawMaterial['barang_Id_Bahan'],
                'bill_of_material_Id_bill_of_material' => $bom->Id_bill_of_material,
                'Jumlah_Bahan' => $rawMaterial['Jumlah_Bahan']
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
