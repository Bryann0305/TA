<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembelian;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\Gudang;
use App\Models\DetailPembelian;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcurementController extends Controller
{
    /**
     * List semua Purchase Order
     */
    public function index()
    {
        $orders = Pembelian::with(['supplier', 'detailPembelian', 'detailPembelian.barang'])->get();
        return view('procurement.index', compact('orders'));
    }

    /**
     * Form tambah PO
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $barangs   = Barang::all();
        $gudangs   = Gudang::all();
        return view('procurement.create', compact('suppliers', 'barangs', 'gudangs'));
    }

    /**
     * Simpan PO baru
     */
    public function store(Request $request)
    {
        // Convert currency format to numeric before validation
        $details = $request->input('details', []);
        if(!empty($details)){
            foreach($details as $key => $detail){
                if(isset($detail['Harga'])){
                    $details[$key]['Harga'] = str_replace(['Rp ', 'Rp', '.'], '', $detail['Harga']);
                }
            }
            $request->merge(['details' => $details]);
        }
        
        // Convert Biaya_Pengiriman currency format to numeric
        if($request->has('Biaya_Pengiriman')){
            $request->merge(['Biaya_Pengiriman' => str_replace(['Rp ', 'Rp', '.'], '', $request->Biaya_Pengiriman)]);
        }
        
        // Convert Total_Biaya currency format to numeric
        if($request->has('Total_Biaya')){
            $request->merge(['Total_Biaya' => str_replace(['Rp ', 'Rp', '.'], '', $request->Total_Biaya)]);
        }

        $validated = $request->validate([
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'Tanggal_Pemesanan'    => 'required|date',
            'Tanggal_Kedatangan'   => 'nullable|date|after_or_equal:Tanggal_Pemesanan',
            'Metode_Pembayaran'    => 'nullable|string|max:50',
            'Biaya_Pengiriman'     => 'nullable|numeric|min:0',
            'details.*.bahan_baku_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'details.*.Jumlah'              => 'required|integer|min:1',
            'details.*.Harga'               => 'required|numeric|min:0',
            'details.*.Keterangan'          => 'nullable|string|max:255',
        ]);

        $totalBiaya = 0;
        foreach ($request->details as $d) {
            $totalBiaya += $d['Jumlah'] * $d['Harga'];
        }

        DB::transaction(function() use ($request, $totalBiaya) {
            // Simpan header pembelian
            $pembelian = Pembelian::create([
                'supplier_Id_Supplier' => $request->supplier_Id_Supplier,
                'Tanggal_Pemesanan'    => $request->Tanggal_Pemesanan,
                'Tanggal_Kedatangan'   => $request->Tanggal_Kedatangan,
                'Metode_Pembayaran'    => $request->Metode_Pembayaran,
                'Biaya_Pengiriman'     => $request->Biaya_Pengiriman ?? 0,
                'Total_Biaya'          => $totalBiaya,
                'Status_Pembayaran'    => 'Pending',
                'user_Id_User'         => auth()->id(),
            ]);

            // Simpan detail pembelian
            foreach ($request->details as $d) {
                DetailPembelian::create([
                    'pembelian_Id_Pembelian' => $pembelian->Id_Pembelian,
                    'bahan_baku_Id_Bahan'    => $d['bahan_baku_Id_Bahan'],
                    'Jumlah'                 => $d['Jumlah'],
                    'Harga_Keseluruhan'      => $d['Jumlah'] * $d['Harga'],
                    'gudang_Id_Gudang'       => $request->gudang_Id_Gudang,
                    'Keterangan'             => $d['Keterangan'] ?? '-',
                    'Status_Penerimaan'      => 'Pending',
                ]);
            }
        });

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dibuat!');
    }

    /**
     * Detail PO
     */
    public function show($id)
    {
        $order = Pembelian::with(['supplier', 'detailPembelian', 'detailPembelian.barang'])->findOrFail($id);
        return view('procurement.show', compact('order'));
    }

    /**
     * Form edit PO
     */
    public function edit($id)
    {
        try {
            $pembelian = Pembelian::with(['detailPembelian.barang', 'supplier'])->findOrFail($id);
            $suppliers = Supplier::all();
            $barangs   = Barang::all();
            $gudangs   = Gudang::all();

            Log::info('Procurement edit accessed', [
                'procurement_id' => $id,
                'suppliers_count' => $suppliers->count(),
                'barangs_count' => $barangs->count(),
                'gudangs_count' => $gudangs->count()
            ]);

            return view('procurement.edit', compact('pembelian', 'suppliers', 'barangs', 'gudangs'));
        } catch (\Exception $e) {
            Log::error('Error accessing procurement edit', [
                'procurement_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('procurement.index')
                ->with('error', 'Terjadi kesalahan saat mengakses halaman edit: ' . $e->getMessage());
        }
    }

    /**
     * Update PO dan detail
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_Id_Supplier' => 'required|exists:supplier,Id_Supplier',
            'Tanggal_Pemesanan'    => 'required|date',
            'Tanggal_Kedatangan'   => 'nullable|date|after_or_equal:Tanggal_Pemesanan',
            'Metode_Pembayaran'    => 'nullable|string|max:50',
            'details.*.bahan_baku_Id_Bahan' => 'required|exists:barang,Id_Bahan',
            'details.*.Jumlah'              => 'required|integer|min:1',
            'details.*.Harga'               => 'required|numeric|min:0',
            'details.*.Keterangan'          => 'nullable|string|max:255',
        ]);

        $pembelian = Pembelian::findOrFail($id);

        DB::transaction(function() use ($request, $pembelian) {
            // Check if procurement is already completed
            $currentStatuses = $pembelian->detailPembelian->pluck('Status_Penerimaan')->unique();
            $isCurrentlyComplete = $currentStatuses->count() === 1 && $currentStatuses->first() === 'Diterima';

            // If completed, reduce stock from old details before deleting
            if ($isCurrentlyComplete) {
                foreach ($pembelian->detailPembelian as $oldDetail) {
                    $barang = Barang::find($oldDetail->bahan_baku_Id_Bahan);
                    if ($barang && $barang->Stok >= $oldDetail->Jumlah) {
                        $barang->Stok -= $oldDetail->Jumlah;
                        $barang->save();
                    }
                }
            }

            // Update header
            $totalBiaya = 0;
            foreach ($request->details as $d) {
                $totalBiaya += $d['Jumlah'] * $d['Harga'];
            }

            $pembelian->update([
                'supplier_Id_Supplier' => $request->supplier_Id_Supplier,
                'Tanggal_Pemesanan'    => $request->Tanggal_Pemesanan,
                'Tanggal_Kedatangan'   => $request->Tanggal_Kedatangan,
                'Metode_Pembayaran'    => $request->Metode_Pembayaran,
                'Total_Biaya'          => $totalBiaya,
            ]);

            // Hapus detail lama
            $pembelian->detailPembelian()->delete();

            // Simpan detail baru dengan status Pending
            foreach ($request->details as $d) {
                DetailPembelian::create([
                    'pembelian_Id_Pembelian' => $pembelian->Id_Pembelian,
                    'bahan_baku_Id_Bahan'    => $d['bahan_baku_Id_Bahan'],
                    'Jumlah'                 => $d['Jumlah'],
                    'Harga_Keseluruhan'      => $d['Jumlah'] * $d['Harga'],
                    'gudang_Id_Gudang'       => $request->gudang_Id_Gudang,
                    'Keterangan'             => $d['Keterangan'] ?? '-',
                    'Status_Penerimaan'      => 'Pending',
                ]);
            }
        });

        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil diperbarui!');
    }

    /**
     * Hapus PO
     */
    public function destroy($id)
{
    // Ambil PO beserta detail pembelian
    $order = Pembelian::with('detailPembelian')->findOrFail($id);

    // Cek apakah PO masih memiliki detail pembelian
    if ($order->detailPembelian()->count() > 0) {
        return redirect()->route('procurement.index')
            ->with('error', 'Purchase Order tidak bisa dihapus karena masih memiliki item terkait.');
    }

    try {
        $order->delete();
        return redirect()->route('procurement.index')->with('success', 'Purchase Order berhasil dihapus!');
    } catch (QueryException $e) {
        // Menangani kemungkinan relasi lain (misal ada foreign key lain di database)
        return redirect()->route('procurement.index')
            ->with('error', 'Purchase Order tidak bisa dihapus karena ada relasi lain di database.');
    }
}



    /**
     * Download PDF
     */
    public function downloadPdf($id)
    {
        $order = Pembelian::with(['supplier', 'detailPembelian.barang'])->findOrFail($id);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('procurement.pdf', compact('order'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Purchase_Order_' . $order->Id_Pembelian . '_' . date('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Toggle status pembayaran
     */
    public function togglePayment($id)
    {
        $order = Pembelian::findOrFail($id);
        $order->Status_Pembayaran = $order->Status_Pembayaran === 'Pending' ? 'Confirmed' : 'Pending';
        $order->save();

        return redirect()->route('procurement.index')->with('success', 'Status pembayaran berhasil diperbarui!');
    }

    /**
     * Update receiving status
     */
    public function updateReceivingStatus(Request $request, $id)
    {
        try {
            $order = Pembelian::findOrFail($id);
            $receivingStatus = $request->receiving_status;

            // Log for debugging
            Log::info('Updating receiving status', [
                'order_id' => $id,
                'receiving_status' => $receivingStatus,
                'detail_count' => $order->detailPembelian()->count()
            ]);

            // Validate the status
            if (!in_array($receivingStatus, ['Pending', 'Diterima'])) {
                return redirect()->route('procurement.index')->with('error', 'Status tidak valid!');
            }

            // Prevent changing from "Diterima" (Complete) back to "Pending"
            $currentStatuses = $order->detailPembelian->pluck('Status_Penerimaan')->unique();
            $isCurrentlyComplete = $currentStatuses->count() === 1 && $currentStatuses->first() === 'Diterima';
            
            if ($isCurrentlyComplete && $receivingStatus === 'Pending') {
                return redirect()->route('procurement.index')->with('error', 'Tidak dapat mengubah status dari Complete kembali ke Pending!');
            }

            // Update all detail pembelian with the new status
            $updated = $order->detailPembelian()->update(['Status_Penerimaan' => $receivingStatus]);

            Log::info('Update result', ['updated_count' => $updated]);

            // Handle stock updates based on status change
            if ($updated > 0) {
                if ($receivingStatus === 'Diterima') {
                    // Add stock when status becomes "Diterima"
                    $this->updateInventoryStock($order, 'add');
                } elseif ($receivingStatus === 'Pending') {
                    // Check if previously was "Diterima" to reduce stock
                    $wasCompleted = $order->detailPembelian()->where('Status_Penerimaan', 'Diterima')->exists();
                    if ($wasCompleted) {
                        $this->updateInventoryStock($order, 'subtract');
                    }
                }
            }

            if ($updated > 0) {
                $message = $receivingStatus === 'Diterima' 
                    ? 'Receiving status berhasil diperbarui dan stock inventory telah diupdate!'
                    : 'Receiving status berhasil diperbarui!';
                return redirect()->route('procurement.index')->with('success', $message);
            } else {
                return redirect()->route('procurement.index')->with('error', 'Tidak ada data yang diperbarui!');
            }
        } catch (\Exception $e) {
            Log::error('Error updating receiving status', ['error' => $e->getMessage()]);
            return redirect()->route('procurement.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Update inventory stock when receiving status is completed
     */
    private function updateInventoryStock($order, $operation = 'add')
    {
        try {
            Log::info('Updating inventory stock for order', [
                'order_id' => $order->Id_Pembelian,
                'operation' => $operation
            ]);

            foreach ($order->detailPembelian as $detail) {
                $barang = $detail->barang;
                if ($barang) {
                    // Get current stock
                    $currentStock = $barang->Stok ?? 0;
                    $quantity = $detail->Jumlah;
                    
                    // Calculate new stock based on operation
                    if ($operation === 'add') {
                        $newStock = $currentStock + $quantity;
                        $logMessage = 'Stock added';
                    } else {
                        $newStock = max(0, $currentStock - $quantity); // Prevent negative stock
                        $logMessage = 'Stock subtracted';
                    }
                    
                    // Update stock
                    $barang->update(['Stok' => $newStock]);
                    
                    Log::info($logMessage, [
                        'barang_id' => $barang->Id_Bahan,
                        'barang_name' => $barang->Nama_Bahan,
                        'old_stock' => $currentStock,
                        'quantity' => $quantity,
                        'new_stock' => $newStock,
                        'operation' => $operation
                    ]);
                }
            }

            Log::info('Inventory stock update completed', ['operation' => $operation]);
        } catch (\Exception $e) {
            Log::error('Error updating inventory stock', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
