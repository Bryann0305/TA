<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\BillOfMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        $produksi = Produksi::with('billOfMaterial')->latest()->get();
        $boms = BillOfMaterial::where('Status', 'approved')->get();
        return view('production.index', compact('produksi', 'boms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_Produksi' => 'required|string|max:255',
            'bill_of_material_Id_bill_of_material' => 'required|exists:bill_of_material,Id_bill_of_material',
            'Jumlah_Produksi' => 'required|numeric|min:1',
            'Status' => 'required|in:planned,in_progress,completed,cancelled'
        ]);

        try {
            DB::beginTransaction();

            // Check if BOM is approved
            $bom = BillOfMaterial::findOrFail($request->bill_of_material_Id_bill_of_material);
            if ($bom->Status !== 'approved') {
                return redirect()->back()
                    ->with('error', 'BOM harus dalam status approved untuk digunakan dalam produksi')
                    ->withInput();
            }

            // Create production order
            $produksi = Produksi::create([
                'Nama_Produksi' => $request->Nama_Produksi,
                'bill_of_material_Id_bill_of_material' => $request->bill_of_material_Id_bill_of_material,
                'Jumlah_Produksi' => $request->Jumlah_Produksi,
                'Status' => $request->Status
            ]);

            DB::commit();
            return redirect()->route('production.index')
                ->with('success', 'Produksi berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $produksi = Produksi::with('billOfMaterial')->findOrFail($id);
        return response()->json($produksi);
    }

    public function edit($id)
    {
        $produksi = Produksi::findOrFail($id);
        $boms = BillOfMaterial::where('Status', 'approved')->get();
        return view('production.edit', compact('produksi', 'boms'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Produksi' => 'required|string|max:255',
            'bill_of_material_Id_bill_of_material' => 'required|exists:bill_of_material,Id_bill_of_material',
            'Jumlah_Produksi' => 'required|numeric|min:1',
            'Status' => 'required|in:planned,in_progress,completed,cancelled'
        ]);

        try {
            DB::beginTransaction();

            $produksi = Produksi::findOrFail($id);

            // Check if production is already completed
            if ($produksi->Status === 'completed') {
                return redirect()->back()
                    ->with('error', 'Produksi yang sudah selesai tidak dapat diubah')
                    ->withInput();
            }

            // Check if BOM is approved
            $bom = BillOfMaterial::findOrFail($request->bill_of_material_Id_bill_of_material);
            if ($bom->Status !== 'approved') {
                return redirect()->back()
                    ->with('error', 'BOM harus dalam status approved untuk digunakan dalam produksi')
                    ->withInput();
            }

            // Update production
            $produksi->update([
                'Nama_Produksi' => $request->Nama_Produksi,
                'bill_of_material_Id_bill_of_material' => $request->bill_of_material_Id_bill_of_material,
                'Jumlah_Produksi' => $request->Jumlah_Produksi,
                'Status' => $request->Status
            ]);

            DB::commit();
            return redirect()->route('production.index')
                ->with('success', 'Produksi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $produksi = Produksi::findOrFail($id);

            // Check if production is already completed
            if ($produksi->Status === 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Produksi yang sudah selesai tidak dapat dihapus'
                ]);
            }

            $produksi->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'Status' => 'required|in:planned,in_progress,completed,cancelled'
        ]);

        try {
            $produksi = Produksi::findOrFail($id);

            // Check if trying to change from completed status
            if ($produksi->Status === 'completed') {
                return redirect()->back()
                    ->with('error', 'Status produksi yang sudah selesai tidak dapat diubah');
            }

            $produksi->update(['Status' => $request->Status]);
            
            return redirect()->route('production.index')
                ->with('success', 'Status produksi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
