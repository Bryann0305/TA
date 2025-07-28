<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BillOfMaterial;
use App\Models\BarangHasBillOfMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillOfMaterialController extends Controller
{
    public function index()
    {
        $boms = BillOfMaterial::with('barangHasBill')->get(); // agar bisa hitung jumlah bahan
        return view('bill-of-materials.index', compact('boms'));
    }

    public function create()
    {
        $barangs = Barang::where('Jenis', 'Bahan_Baku')->get(); // hanya bahan baku
        return view('bill-of-materials.create', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_bill_of_material' => 'required|string',
            'Status' => 'required|string',
            'bahan_baku' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            // Simpan BOM
            $bom = BillOfMaterial::create([
                'Nama_bill_of_material' => $request->Nama_bill_of_material,
                'Status' => $request->Status,
            ]);

            // Simpan bahan-baku ke pivot
            foreach ($request->bahan_baku as $idBahan => $data) {
                if (isset($data['selected']) && $data['selected'] == 1) {
                    BarangHasBillOfMaterial::create([
                        'barang_Id_Bahan' => $idBahan,
                        'bill_of_material_Id_bill_of_material' => $bom->Id_bill_of_material,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('bill-of-materials.index')->with('success', 'BOM successfully saved!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to save: ' . $e->getMessage());
        }
    }
}
