<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produksi;
use App\Models\ProduksiDetail;
use App\Models\ProductionOrder;
use App\Models\BillOfMaterial;
use App\Models\GagalProduksi;
use App\Models\Barang;
use DB;

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


        DB::transaction(function() use ($order,$jadwalId){
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
                ProduksiDetail::create([
                    'produksi_id' => $produksi->Id_Produksi,
                    'barang_id' => $detail->barang_id ?? null,
                    'jumlah' => $detail->Jumlah ?? 0,
                    'status' => 'pending',
                ]);
            }

            // Tambahkan detail produk jadi (hasil produksi)
            if ($order->produk_id ?? false) {
                ProduksiDetail::create([
                    'produksi_id' => $produksi->Id_Produksi,
                    'barang_id' => $order->produk_id,
                    'jumlah' => 0, // jumlah berhasil diinput saat complete
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
            $produksi = Produksi::with('productionOrder')->findOrFail($id);

            $jumlahBerhasil = (int) $request->input('jumlah_berhasil', 0);
            $jumlahGagal = (int) $request->input('jumlah_gagal', 0);
            $keteranganGagal = $request->input('keterangan_gagal', '');

            // Update produksi utama
            $produksi->update([
                'Jumlah_Berhasil' => $jumlahBerhasil,
                'Jumlah_Gagal' => $jumlahGagal,
                'Status' => 'completed'
            ]);

            // Update stok barang hasil utama
            $produkId = $produksi->productionOrder->produk_id ?? null;
            if ($produkId) {
                $barang = Barang::find($produkId);
                if ($barang && $jumlahBerhasil > 0) {
                    $barang->Stok = ($barang->Stok ?? 0) + $jumlahBerhasil;
                    $barang->save();
                }
            }

            // Simpan gagal produksi
            if ($jumlahGagal > 0) {
                GagalProduksi::create([
                    'Total_Gagal' => $jumlahGagal,
                    'Keterangan' => $keteranganGagal,
                    'produksi_Id_Produksi' => $produksi->Id_Produksi,
                ]);

                // Buat produksi ulang
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
}
