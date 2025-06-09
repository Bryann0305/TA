<?php

namespace App\Http\Controllers;

use App\Models\BarangHasBillOfMaterial;
use App\Models\BillOfMaterial;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangHasBillOfMaterialController extends Controller
{
    public function index()
    {
        return BarangHasBillOfMaterial::all();
    }

    public function show($id)
    {
        return BarangHasBillOfMaterial::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_Id_Bahan' => 'required|integer',
            'bill_of_material_Id_bill_of_material' => 'required|integer',
        ]);
        return BarangHasBillOfMaterial::create($data);
    }

    public function update(Request $request, $id)
    {
        $item = BarangHasBillOfMaterial::findOrFail($id);
        $item->update($request->all());
        return $item;
    }

    public function destroy($id)
    {
        BarangHasBillOfMaterial::destroy($id);
        return response()->noContent();
    }

    public function addBahan(Request $request, $bomId)
    {
        $request->validate([
            'Id_barang' => 'required|exists:barang,Id_barang',
            'jumlah' => 'required|numeric|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Check if BOM exists
            $bom = BillOfMaterial::findOrFail($bomId);

            // Check if material already exists in BOM
            $exists = BarangHasBillOfMaterial::where('Id_bill_of_material', $bomId)
                ->where('Id_barang', $request->Id_barang)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bahan sudah ada dalam BOM ini'
                ]);
            }

            // Add material to BOM
            BarangHasBillOfMaterial::create([
                'Id_bill_of_material' => $bomId,
                'Id_barang' => $request->Id_barang,
                'jumlah' => $request->jumlah
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Bahan berhasil ditambahkan ke BOM'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function updateJumlah(Request $request, $bomId, $barangId)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1'
        ]);

        try {
            DB::beginTransaction();

            // Update jumlah bahan
            $bomDetail = BarangHasBillOfMaterial::where('Id_bill_of_material', $bomId)
                ->where('Id_barang', $barangId)
                ->firstOrFail();

            $bomDetail->update([
                'jumlah' => $request->jumlah
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jumlah bahan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function removeBahan($bomId, $barangId)
    {
        try {
            DB::beginTransaction();

            // Remove material from BOM
            BarangHasBillOfMaterial::where('Id_bill_of_material', $bomId)
                ->where('Id_barang', $barangId)
                ->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Bahan berhasil dihapus dari BOM'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function getBahanBom($bomId)
    {
        try {
            $bahan = BarangHasBillOfMaterial::with('barang')
                ->where('Id_bill_of_material', $bomId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bahan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
} 