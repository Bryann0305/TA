<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\ProduksiDetail;
use App\Models\ProductionOrder;
use App\Models\BillOfMaterial;
use App\Models\GagalProduksi;
use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductionController extends Controller
{
    // ===========================
    // Daftar Produksi (Planned, Current, Completed)
    // ===========================
    public function index(Request $request)
    {
        $produksiPlanned = Produksi::where('Status', 'planned')
            ->with(['details.barang','productionOrder.pesananProduksi','penjadwalan'])
            ->get();

        $produksiCurrent = Produksi::where('Status', 'current')
            ->with(['details.barang','productionOrder.pesananProduksi','penjadwalan'])
            ->get();

        $produksiCompleted = Produksi::where('Status', 'completed')
            ->with(['details.barang','productionOrder.pesananProduksi','penjadwalan'])
            ->get();

        $tab = $request->query('tab','planned');

        return view('production.index', compact('produksiPlanned','produksiCurrent','produksiCompleted','tab'));
    }

    // ===========================
    // Form Create Produksi
    // ===========================
    public function create()
    {
        $usedOrderIds = Produksi::pluck('production_order_id')->filter()->unique()->toArray();

        $orders = ProductionOrder::with(['pesananProduksi.detail','penjadwalan'])
            ->whereNotIn('id',$usedOrderIds)
            ->orderBy('id','desc')
            ->get();

        $boms = BillOfMaterial::with('barangs')->get();
        $barangs = Barang::all();

        return view('production.create', compact('orders','boms','barangs'));
    }

    // ===========================
    // AJAX: Ambil detail barang dari SPP
    // ===========================
    public function ajaxOrderDetails($orderId)
    {
        $order = ProductionOrder::with('pesananProduksi')->find($orderId);

        if (!$order) {
            return response()->json(['error'=>'Order tidak ditemukan'],404);
        }

        return response()->json([
            'order' => $order,
            'pesanan' => $order->pesananProduksi
        ]);
    }

    // ===========================
    // Simpan Produksi Baru
    // ===========================
    public function store(Request $request)
    {
        $request->validate([
            'production_order_id'=>'required|exists:production_order,id',
        ]);

        $order = ProductionOrder::with(['penjadwalan','pesananProduksi.detail'])->findOrFail($request->production_order_id);

        if(Produksi::where('production_order_id',$order->id)->exists()){
            return redirect()->back()->with('error','SPP sudah pernah dipakai untuk produksi.');
        }

        if(!$order->penjadwalan){
            return redirect()->back()->with('error','SPP ini belum memiliki jadwal, tidak bisa membuat produksi.');
        }

        $jadwalId = $order->penjadwalan->Id_Jadwal;


        DB::transaction(function() use ($order,$jadwalId,$request){
            $produksi = Produksi::create([
                'Hasil_Produksi' => $order->Nama_Produksi ?? 'Produksi #'.$order->id,
                'Tanggal_Produksi' => now(),
                'Status' => 'planned',
                'Jumlah_Berhasil' => 0,
                'Jumlah_Gagal' => 0,
                'pesanan_produksi_Id_Pesanan' => $order->pesananProduksi->Id_Pesanan ?? null,
                'penjadwalan_Id_Jadwal' => $jadwalId,
                'production_order_id' => $order->id,
            ]);

            // Detail bahan baku
            foreach($order->pesananProduksi->detail ?? [] as $detail){
                if (empty($detail->barang_Id_Bahan)) {
                    throw new \Exception('Barang pada detail pesanan tidak boleh kosong!');
                }
                ProduksiDetail::create([
                    'produksi_id' => $produksi->Id_Produksi,
                    'bill_of_material_id' => $detail->bill_of_material_id ?? 1, 
                    'barang_id' => $detail->barang_Id_Bahan,
                    'jumlah' => $detail->Jumlah ?? 0,
                    'status' => 'pending',
                ]);
            }

            // Tambahkan detail produk jadi (hasil produksi) berdasarkan pilihan pada form
            $produkIds = (array) $request->input('produk_ids', []);
            $bomIds = (array) $request->input('bom_ids', []);
            foreach ($produkIds as $idx => $produkId) {
                if (!empty($produkId)) {
                    $bomId = $bomIds[$idx] ?? null;
                    ProduksiDetail::create([
                        'produksi_id' => $produksi->Id_Produksi,
                        'bill_of_material_id' => $bomId ?? 1,
                        'barang_id' => $produkId,
                        'jumlah' => 0, // jumlah berhasil diinput saat complete
                        'status' => 'pending',
                    ]);
                }
            }
        });

        return redirect()->route('production.index',['tab'=>'planned'])
                         ->with('success','Produksi berhasil dibuat, status: planned.');
    }

    // ===========================
    // Input Hasil Produksi per Produk (Sesuai ketentuan)
    // ===========================

    public function complete(Request $request, $id)
{
    try {
        $redirectTab = 'all';
        DB::transaction(function() use ($request, $id, &$redirectTab) {
            $produksi = Produksi::with(['details', 'productionOrder'])->findOrFail($id);
            $hasilArr = $request->input('hasil', []);
            $gagalArr = $request->input('gagal', []);
            $totalBerhasil = 0;
            $totalGagal = 0;
            $adaGagal = false;

            foreach ($produksi->details as $detail) {
                $detailId = $detail->id;
                $jumlahBerhasil = isset($hasilArr[$detailId]) ? (int)$hasilArr[$detailId] : 0;
                $jumlahGagal = isset($gagalArr[$detailId]['jumlah']) ? (int)$gagalArr[$detailId]['jumlah'] : 0;
                $keteranganGagal = isset($gagalArr[$detailId]['keterangan']) ? $gagalArr[$detailId]['keterangan'] : '';
                $jumlahRencana = $jumlahBerhasil + $jumlahGagal; // konsumsi bahan berdasarkan rencana (berhasil+gagal)

                // Hanya proses detail yang dikirim oleh form (produk jadi). Abaikan detail lain.
                if (!array_key_exists($detailId, $hasilArr) && !array_key_exists($detailId, $gagalArr)) {
                    continue;
                }

                // Update ProduksiDetail
                $detail->jumlah = $jumlahBerhasil;
                $detail->status = 'completed';
                $detail->save();

                // Tambah stok produk jadi sesuai jumlah berhasil
                if ($jumlahBerhasil > 0 && $detail->barang_id) {
                    $barangJadi = Barang::find($detail->barang_id);
                    if ($barangJadi) {
                        $barangJadi->Stok = ($barangJadi->Stok ?? 0) + $jumlahBerhasil;
                        $barangJadi->save();
                    }
                }

                // Kurangi stok bahan baku berdasarkan BOM x jumlah rencana (berhasil+gagal)
                // Jalankan untuk detail produk yang diproses (karena hanya produk yang dikirim dari form)
                if ($jumlahRencana > 0) {
                    // Tentukan BOM: pakai yang ter-assign di detail, jika null ambil dari relasi barang->boms (pertama)
                    $bomId = $detail->bill_of_material_id;
                    if (!$bomId && $detail->barang_id) {
                        $barangForBom = Barang::with('boms')->find($detail->barang_id);
                        $bomId = optional($barangForBom->boms->first())->Id_bill_of_material;
                    }

                    if ($bomId) {
                        // 1) Coba lewat relasi Eloquent BOM->barangs (pivot 'Jumlah_Bahan' atau 'jumlah_bahan')
                        $bomRel = BillOfMaterial::with('barangs')->find($bomId);
                        $turunkanViaRelasi = false;
                        if ($bomRel && $bomRel->barangs && $bomRel->barangs->count()) {
                            foreach ($bomRel->barangs as $bahan) {
                                $kebutuhanPerUnit = (int) ($bahan->pivot->Jumlah_Bahan ?? $bahan->pivot->jumlah_bahan ?? 0);
                                if ($kebutuhanPerUnit <= 0) continue;
                                $totalKebutuhan = $kebutuhanPerUnit * $jumlahRencana;
                                $barangBaku = Barang::find($bahan->Id_Bahan);
                                if ($barangBaku) {
                                    $stokSekarang = (int) ($barangBaku->Stok ?? 0);
                                    $barangBaku->Stok = max(0, $stokSekarang - $totalKebutuhan);
                                    $barangBaku->save();
                                    $turunkanViaRelasi = true;
                                }
                            }
                        }

                        // 2) Jika relasi tidak memberi kuantitas, fallback ke query pivot mentah (skema dinamis)
                        if (!$turunkanViaRelasi) {
                            $pivotTable = 'barang_has_bill_of_material';
                            $colBomLegacy = 'bill_of_material_Id_bill_of_material';
                            $colBomNew = 'bill_of_material_id';
                            $colBarangLegacy = 'barang_Id_Bahan';
                            $colBarangNew = 'barang_id';

                            $bomColumn = Schema::hasColumn($pivotTable, $colBomLegacy)
                                ? $colBomLegacy
                                : (Schema::hasColumn($pivotTable, $colBomNew) ? $colBomNew : null);

                            $barangColumn = Schema::hasColumn($pivotTable, $colBarangLegacy)
                                ? $colBarangLegacy
                                : (Schema::hasColumn($pivotTable, $colBarangNew) ? $colBarangNew : null);

                            if ($bomColumn && $barangColumn) {
                                $qtyColumn = Schema::hasColumn($pivotTable, 'Jumlah_Bahan')
                                    ? 'Jumlah_Bahan'
                                    : (Schema::hasColumn($pivotTable, 'jumlah_bahan') ? 'jumlah_bahan' : null);

                                $pivotRows = DB::table($pivotTable)->where($bomColumn, $bomId)->get();
                                foreach ($pivotRows as $pivot) {
                                    $barangId = $pivot->{$barangColumn} ?? null;
                                    $kebutuhanPerUnit = (int) ($qtyColumn ? ($pivot->{$qtyColumn} ?? 0) : 0);
                                    if (!$barangId || $kebutuhanPerUnit <= 0) continue;
                                    $totalKebutuhan = $kebutuhanPerUnit * $jumlahRencana;
                                    $barangBaku = Barang::find($barangId);
                                    if ($barangBaku) {
                                        $stokSekarang = (int) ($barangBaku->Stok ?? 0);
                                        $barangBaku->Stok = max(0, $stokSekarang - $totalKebutuhan);
                                        $barangBaku->save();
                                    }
                                }
                            }
                        }
                    }
                }

                // Simpan gagal produksi per-item
                if ($jumlahGagal > 0) {
                    $adaGagal = true;
                    GagalProduksi::create([
                        'Total_Gagal' => $jumlahGagal,
                        'Keterangan' => $keteranganGagal,
                        'produksi_Id_Produksi' => $produksi->Id_Produksi,
                    ]);
                }

                $totalBerhasil += $jumlahBerhasil;
                $totalGagal += $jumlahGagal;
            }

            // Update produksi utama
            $produksi->update([
                'Jumlah_Berhasil' => $totalBerhasil,
                'Jumlah_Gagal' => $totalGagal,
                'Status' => 'completed'
            ]);

            // Jika ada gagal, buat produksi ulang
            if ($adaGagal) {
                $newProduksi = Produksi::create([
                    'Hasil_Produksi' => 'Produksi Ulang #' . $produksi->Id_Produksi,
                    'Tanggal_Produksi' => now(),
                    'Status' => 'current',
                    'Jumlah_Berhasil' => 0,
                    'Jumlah_Gagal' => 0,
                    'pesanan_produksi_Id_Pesanan' => $produksi->pesanan_produksi_Id_Pesanan,
                    'penjadwalan_Id_Jadwal' => $produksi->penjadwalan_Id_Jadwal,
                    'production_order_id' => $produksi->production_order_id,
                ]);
                $redirectTab = 'current';
            } else {
                $redirectTab = 'all';
            }
        });
        return response()->json([
            'success' => 'Hasil produksi berhasil disimpan.',
            'redirect_tab' => $redirectTab
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Gagal menyimpan hasil produksi. ' . $e->getMessage()
        ]);
    }
}




    // ===========================
    // Detail Produksi
    // ===========================
    public function show($id)
    {
        $produksi = Produksi::with([
            'details.barang',
            'gagalProduksi',
            'productionOrder.pesananProduksi',
            'penjadwalan'
        ])->findOrFail($id);

        $tab = request('tab', 'planned');

        return view('production.show', compact('produksi','tab'));
    }

    // ===========================
    // Edit Produksi
    // ===========================
    public function edit($id)
    {
        $produksi = Produksi::with(['details.barang','productionOrder.pesananProduksi','penjadwalan'])->findOrFail($id);
        // Tambahkan data lain jika perlu
        return view('production.edit', compact('produksi'));
    }

    // ===========================
    // Hapus Produksi
    // ===========================
    public function destroy($id)
    {
        $produksi = Produksi::findOrFail($id);

        if ($produksi->Status != 'planned') {
            return redirect()->route('production.index', ['tab' => 'planned'])
                             ->with('error', 'Hanya bisa dihapus sebelum diapprove.');
        }

        DB::transaction(function() use ($produksi) {
            ProduksiDetail::where('produksi_id', $produksi->Id_Produksi)->delete();
            $produksi->delete();
        });

        return redirect()->route('production.index', ['tab' => 'planned'])
                         ->with('success', 'Produksi berhasil dihapus.');
    }

    // ===========================
    // Approve Produksi (Planned -> Current)
    // ===========================
    public function approve($id)
    {
        $produksi = Produksi::findOrFail($id);
        $produksi->Status = 'current';
        $produksi->save();
        return redirect()->route('production.index', ['tab' => 'current'])
            ->with('success', 'Produksi berhasil di-approve dan masuk ke tab Current.');
    }

    // ===========================
    // Approve Selesai Produksi (Current -> Completed)
    // ===========================
    public function approveCompleted($id)
    {
        $produksi = Produksi::findOrFail($id);
        $produksi->Status = 'completed';
        $produksi->save();
        return redirect()->route('production.index', ['tab' => 'completed'])
            ->with('success', 'Produksi berhasil diselesaikan dan masuk ke tab Completed.');
    }

    // ===========================
    // Pindahkan Produksi ke Completed (Current -> Completed)
    // ===========================
    public function moveToCompleted($id)
    {
        $produksi = Produksi::findOrFail($id);
        $produksi->Status = 'completed';
        $produksi->save();
        return redirect()->route('production.index', ['tab' => 'completed'])
            ->with('success', 'Produksi berhasil dipindahkan ke tab Completed.');
    }
}