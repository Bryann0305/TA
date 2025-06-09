<?php

namespace App\Http\Controllers;

use App\Models\BillOfMaterial;
use App\Models\Produksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillOfMaterialController extends Controller
{
    public function index()
    {
        $boms = BillOfMaterial::with('produksi')->latest()->get();
        return view('bill-of-materials.index', compact('boms'));
    }

    public function create()
    {
        return view('bill-of-materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_bill_of_material' => 'required|string|max:255',
            'Status' => 'required|in:draft,approved,rejected'
        ]);

        try {
            DB::beginTransaction();

            // Create BOM
            $bom = BillOfMaterial::create([
                'Nama_bill_of_material' => $request->Nama_bill_of_material,
                'Status' => $request->Status
            ]);

            DB::commit();
            return redirect()->route('bill-of-materials.index')
                ->with('success', 'Bill of Materials berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $bom = BillOfMaterial::with('produksi')->findOrFail($id);
        return response()->json($bom);
    }

    public function edit($id)
    {
        $bom = BillOfMaterial::findOrFail($id);
        return view('bill-of-materials.edit', compact('bom'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_bill_of_material' => 'required|string|max:255',
            'Status' => 'required|in:draft,approved,rejected'
        ]);

        try {
            DB::beginTransaction();

            $bom = BillOfMaterial::findOrFail($id);
            
            // Check if BOM is used in production
            if ($bom->produksi()->exists()) {
                return redirect()->back()
                    ->with('error', 'BOM tidak dapat diubah karena sudah digunakan dalam produksi')
                    ->withInput();
            }
            
            // Update BOM
            $bom->update([
                'Nama_bill_of_material' => $request->Nama_bill_of_material,
                'Status' => $request->Status
            ]);

            DB::commit();
            return redirect()->route('bill-of-materials.index')
                ->with('success', 'Bill of Materials berhasil diperbarui');
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

            $bom = BillOfMaterial::findOrFail($id);

            // Check if BOM is used in production
            if ($bom->produksi()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'BOM tidak dapat dihapus karena sudah digunakan dalam produksi'
                ]);
            }

            // Delete BOM
            $bom->delete();

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
            'Status' => 'required|in:draft,approved,rejected'
        ]);

        try {
            $bom = BillOfMaterial::findOrFail($id);

            // Check if BOM is used in production
            if ($bom->produksi()->exists() && $request->Status === 'draft') {
                return redirect()->back()
                    ->with('error', 'BOM tidak dapat diubah ke status draft karena sudah digunakan dalam produksi');
            }

            $bom->update(['Status' => $request->Status]);
            
            return redirect()->route('bill-of-materials.index')
                ->with('success', 'Status Bill of Materials berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 