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
use Illuminate\Support\Facades\Log;

class ProductionController extends Controller
{
    // ===========================
    // Daftar Produksi (Planned, Current, Completed)
    // ===========================
    public function index(Request $request)
    {
        $produksiPlanned = Produksi::where('Status', 'planned')
            ->with(['details.barang','details.billOfMaterial.barangs','productionOrder.pesananProduksi','penjadwalan'])
            ->get();

        $produksiCurrent = Produksi::where('Status', 'current')
            ->with(['details.barang','details.billOfMaterial.barangs','productionOrder.pesananProduksi','penjadwalan'])
            ->get();

        $produksiCompleted = Produksi::where('Status', 'completed')
            ->with(['details.barang','details.billOfMaterial.barangs','productionOrder.pesananProduksi','penjadwalan','gagalProduksi'])
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

        $orders = ProductionOrder::with(['pesananProduksi.detail.barang','penjadwalan'])
            ->whereNotIn('id',$usedOrderIds)
            ->orderBy('id','desc')
            ->get();

        // Get BOM data for Cat Tembok
        $bomData = [];
        $stockData = [];
        
        $bom = BillOfMaterial::where('Nama_bill_of_material', 'BOM - Cat Tembok')->with('barangs')->first();
        if ($bom) {
            $bomData = $bom->barangs->map(function($material) {
                return [
                    'material_id' => $material->Id_Bahan,
                    'material_name' => $material->Nama_Bahan,
                    'bom_quantity' => $material->pivot->Jumlah_Bahan,
                    'unit' => $material->Satuan,
                    'current_stock' => $material->Stok ?? 0
                ];
            })->toArray();
        }

        // Debug: Log orders data
        Log::info('Production orders loaded', [
            'orders_count' => $orders->count(),
            'bom_data' => $bomData,
            'orders_data' => $orders->map(function($order) {
                return [
                    'id' => $order->id,
                    'nama_produksi' => $order->Nama_Produksi,
                    'pesanan_produksi' => $order->pesananProduksi ? $order->pesananProduksi->toArray() : null,
                    'detail_count' => $order->pesananProduksi && $order->pesananProduksi->detail ? $order->pesananProduksi->detail->count() : 0
                ];
            })->toArray()
        ]);

        return view('production.create', compact('orders', 'bomData'));
    }

    // Method untuk mengambil data BOM berdasarkan produk
    public function getBomData(Request $request)
    {
        $productName = $request->input('product_name');
        $quantity = $request->input('quantity', 1);
        
        // Debug: Log the request
        Log::info('=== getBomData called ===', [
            'product_name' => $productName,
            'quantity' => $quantity,
            'request_url' => $request->fullUrl(),
            'request_method' => $request->method()
        ]);
        
        // Cari produk berdasarkan nama
        $product = Barang::where('Nama_Bahan', $productName)->where('Jenis', 'Produk')->first();
        
        Log::info('Product search result', [
            'product_found' => $product ? true : false,
            'product_name' => $productName,
            'product_data' => $product ? $product->toArray() : null
        ]);
        
        if (!$product) {
            // Debug: List all products
            $allProducts = Barang::where('Jenis', 'Produk')->get(['Nama_Bahan']);
            Log::info('All products in database', [
                'products' => $allProducts->pluck('Nama_Bahan')->toArray()
            ]);
            
            return response()->json(['error' => 'Product not found: ' . $productName], 404);
        }
        
        // Cari BOM berdasarkan nama produk (format: "BOM - [Nama Produk]")
        $bomName = 'BOM - ' . $productName;
        $bom = BillOfMaterial::where('Nama_bill_of_material', $bomName)->with('barangs')->first();
        
        Log::info('BOM search result', [
            'bom_name_searched' => $bomName,
            'bom_found' => $bom ? true : false,
            'bom_data' => $bom ? $bom->toArray() : null
        ]);
        
        if (!$bom) {
            // Debug: List all BOMs
            $allBoms = BillOfMaterial::all(['Nama_bill_of_material', 'Status']);
            Log::info('All BOMs in database', [
                'boms' => $allBoms->toArray()
            ]);
            
            return response()->json(['error' => 'BOM not found for product: ' . $productName], 404);
        }
        
        $materials = [];
        
        // Debug: Log BOM barangs
        Log::info('BOM barangs debug', [
            'bom_id' => $bom->Id_bill_of_material,
            'barangs_count' => $bom->barangs ? $bom->barangs->count() : 0,
            'barangs_data' => $bom->barangs ? $bom->barangs->toArray() : null
        ]);
        
        if ($bom->barangs && $bom->barangs->count() > 0) {
            foreach ($bom->barangs as $material) {
                $materials[] = [
                    'material_id' => $material->Id_Bahan,
                    'material_name' => $material->Nama_Bahan,
                    'bom_quantity' => $material->pivot->Jumlah_Bahan,
                    'total_quantity' => $material->pivot->Jumlah_Bahan * $quantity,
                    'unit' => $material->Satuan
                ];
            }
        } else {
            // Jika tidak ada barangs, coba ambil dari pivot table langsung
            Log::info('No barangs found, trying direct pivot query');
            $pivotData = DB::table('barang_has_bill_of_material')
                ->where('bill_of_material_Id_bill_of_material', $bom->Id_bill_of_material)
                ->join('barang', 'barang_has_bill_of_material.barang_Id_Bahan', '=', 'barang.Id_Bahan')
                ->get();
                
            Log::info('Direct pivot query result', [
                'pivot_data' => $pivotData->toArray()
            ]);
            
            foreach ($pivotData as $item) {
                $materials[] = [
                    'material_id' => $item->barang_Id_Bahan,
                    'material_name' => $item->Nama_Bahan,
                    'bom_quantity' => $item->Jumlah_Bahan,
                    'total_quantity' => $item->Jumlah_Bahan * $quantity,
                    'unit' => $item->Satuan
                ];
            }
        }
        
        Log::info('BOM materials found', [
            'materials_count' => count($materials),
            'materials' => $materials
        ]);
        
        return response()->json([
            'bom_name' => $bom->Nama_bill_of_material,
            'materials' => $materials
        ]);
    }

    // Method untuk mengambil data stock bahan baku
    public function getStockData(Request $request)
    {
        $productName = $request->input('product_name');
        
        // Debug: Log the request
        Log::info('getStockData called', [
            'product_name' => $productName
        ]);
        
        // Cari produk berdasarkan nama
        $product = Barang::where('Nama_Bahan', $productName)->where('Jenis', 'Produk')->first();
        
        if (!$product) {
            return response()->json(['error' => 'Product not found: ' . $productName], 404);
        }
        
        // Cari BOM berdasarkan nama produk (format: "BOM - [Nama Produk]")
        $bomName = 'BOM - ' . $productName;
        $bom = BillOfMaterial::where('Nama_bill_of_material', $bomName)->with('barangs')->first();
        
        if (!$bom) {
            return response()->json(['error' => 'BOM not found for product: ' . $productName], 404);
        }
        
        $stockData = [];
        foreach ($bom->barangs as $material) {
            $stockData[] = [
                'material_id' => $material->Id_Bahan,
                'material_name' => $material->Nama_Bahan,
                'current_stock' => $material->Stok ?? 0,
                'unit' => $material->Satuan
            ];
        }
        
        Log::info('Stock data found', [
            'stock_data' => $stockData
        ]);
        
        return response()->json($stockData);
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

            // Detail produk dari Production Request (sudah terhubung dengan BOM)
            foreach($order->pesananProduksi->detail ?? [] as $detail){
                if (empty($detail->barang_Id_Bahan)) {
                    throw new \Exception('Barang pada detail pesanan tidak boleh kosong!');
                }
                
                // Cari BOM yang sesuai dengan produk ini
                $barang = Barang::find($detail->barang_Id_Bahan);
                $bom = null;
                if ($barang) {
                    // Cari BOM berdasarkan nama yang sesuai dengan produk
                    // Format: "BOM - [Nama Produk]"
                    $bomName = 'BOM - ' . $barang->Nama_Bahan;
                    $bom = BillOfMaterial::where('Nama_bill_of_material', $bomName)->first();
                    
                    // Jika tidak ditemukan dengan nama, cari BOM yang memiliki produk ini sebagai finished good
                    if (!$bom) {
                        $bom = BillOfMaterial::whereHas('barangs', function($query) use ($barang) {
                            $query->where('barang_Id_Bahan', $barang->Id_Bahan)
                                  ->where('Jumlah_Bahan', 1); // Finished good quantity is always 1
                        })->first();
                    }
                }
                
                // Jika tidak ada BOM yang ditemukan, buat error yang jelas
                if (!$bom) {
                    throw new \Exception("Tidak ada BOM yang tersedia untuk produk: " . ($barang->Nama_Bahan ?? 'Unknown') . ". Pastikan BOM dengan nama 'BOM - " . ($barang->Nama_Bahan ?? 'Unknown') . "' sudah dibuat.");
                }
                
                ProduksiDetail::create([
                    'produksi_id' => $produksi->Id_Produksi,
                    'bill_of_material_id' => $bom->Id_bill_of_material,
                    'barang_id' => $detail->barang_Id_Bahan,
                    'jumlah' => $detail->Jumlah ?? 0,
                    'status' => 'pending',
                ]);
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

            // Jika ada gagal, buat produksi ulang dengan detail yang sama
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

                // Update Jumlah Dipesan di PesananProduksi (kurangi dengan yang berhasil)
                $pesananProduksi = $produksi->pesananProduksi;
                if ($pesananProduksi) {
                    $jumlahBerhasilTotal = $totalBerhasil;
                    $jumlahDipesanSekarang = $pesananProduksi->Jumlah_Pesanan;
                    $jumlahDipesanBaru = max(0, $jumlahDipesanSekarang - $jumlahBerhasilTotal);
                    
                    $pesananProduksi->update([
                        'Jumlah_Pesanan' => $jumlahDipesanBaru
                    ]);
                }

                // Copy detail produksi dari produksi asli ke produksi ulang
                // Hanya untuk detail yang memiliki gagal produksi
                foreach ($produksi->details as $detail) {
                    $detailId = $detail->id;
                    $jumlahGagal = isset($gagalArr[$detailId]['jumlah']) ? (int)$gagalArr[$detailId]['jumlah'] : 0;
                    
                    // Hanya buat detail ulang jika ada yang gagal
                    if ($jumlahGagal > 0) {
                        ProduksiDetail::create([
                            'produksi_id' => $newProduksi->Id_Produksi,
                            'barang_id' => $detail->barang_id,
                            'bill_of_material_id' => $detail->bill_of_material_id,
                            'jumlah' => $jumlahGagal, // Jumlah yang gagal untuk diulang
                            'status' => 'current'
                        ]);
                    }
                }

                // Log untuk debugging
                Log::info('Produksi ulang dibuat', [
                    'produksi_asli_id' => $produksi->Id_Produksi,
                    'produksi_ulang_id' => $newProduksi->Id_Produksi,
                    'jumlah_berhasil' => $totalBerhasil,
                    'jumlah_gagal' => $totalGagal,
                    'jumlah_dipesan_baru' => $jumlahDipesanBaru ?? 0
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
            'details.billOfMaterial.barangs',
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