<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\BillOfMaterial;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    public function index()
    {
        $produksi = Produksi::with('billOfMaterial')
            ->orderBy('Tanggal_Produksi', 'desc')
            ->get();

        //Tampilkan semua BOM, tidak hanya yang approved
        $boms = BillOfMaterial::with('barang')->orderBy('Id_bill_of_material', 'desc')->get();

        return view('production.index', compact('produksi', 'boms'));
    }

       public function create()
{
    $boms = BillOfMaterial::all(); // ambil semua data BOM
    return view('production.create', compact('boms'));
}


    public function store(Request $request)
{
    $request->validate([
        'Nama_Produksi' => 'required|string',
        'Jumlah_Produksi' => 'required|numeric|min:1',
        'Status' => 'required|string',
        'bill_of_material_Id_bill_of_material' => 'required|exists:bill_of_material,Id_bill_of_material',
    ]);

    try {
        $produksi = Produksi::create([
            'Nama_Produksi' => $request->Nama_Produksi,
            'Jumlah_Produksi' => $request->Jumlah_Produksi,
            'Jumlah_Berhasil' => 0,
            'Status' => $request->Status,
            'Tanggal_Produksi' => now(),
            'bill_of_material_Id_bill_of_material' => $request->bill_of_material_Id_bill_of_material
        ]);

        return redirect()->route('production.index')->with('success', 'Produksi berhasil ditambahkan');
    } catch (\Exception $e) {
        return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
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
            if ($produksi->Status === 'completed') {
                return redirect()->back()->with('error', 'Produksi yang sudah selesai tidak dapat diubah')->withInput();
            }

            $bom = BillOfMaterial::findOrFail($request->bill_of_material_Id_bill_of_material);
            if ($bom->Status !== 'approved') {
                return redirect()->back()->with('error', 'BOM harus dalam status approved')->withInput();
            }

            if ($produksi->Jumlah_Berhasil > $request->Jumlah_Produksi) {
                return redirect()->back()->with('error', 'Jumlah produksi tidak boleh lebih kecil dari jumlah berhasil')->withInput();
            }

            $produksi->update([
                'Nama_Produksi' => $request->Nama_Produksi,
                'bill_of_material_Id_bill_of_material' => $request->bill_of_material_Id_bill_of_material,
                'Jumlah_Produksi' => $request->Jumlah_Produksi,
                'Status' => $request->Status
            ]);

            DB::commit();
            return redirect()->route('production.index')->with('success', 'Produksi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $produksi = Produksi::with('billOfMaterial')->findOrFail($id);
        return response()->json($produksi);

        $produksi = Produksi::create([
    'Nama_Produksi' => $request->Nama_Produksi,
    'bill_of_material_Id_bill_of_material' => $request->bill_of_material_Id_bill_of_material,
    'Jumlah_Produksi' => $request->Jumlah_Produksi,
    'Jumlah_Berhasil' => 0,
    'Status' => $request->Status,
    'Tanggal_Produksi' => now()
]);

// Simpan bahan baku yang dicentang ke tabel pivot
if ($request->has('bahan_baku')) {
    foreach ($request->bahan_baku as $idBahan => $data) {
        if (isset($data['selected']) && $data['selected'] == 1) {
            DB::table('produksi_has_barang')->insert([
                'produksi_id' => $produksi->id,
                'barang_id' => $idBahan,
                'jumlah' => $data['jumlah'],
            ]);
        }
    }
}

    }

    

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'Status' => 'required|in:planned,in_progress,completed,cancelled'
        ]);

        try {
            $produksi = Produksi::findOrFail($id);
            if ($produksi->Status === 'completed') {
                return redirect()->back()->with('error', 'Status produksi yang sudah selesai tidak dapat diubah');
            }

            $produksi->update(['Status' => $request->Status]);
            return redirect()->route('production.index')->with('success', 'Status produksi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
