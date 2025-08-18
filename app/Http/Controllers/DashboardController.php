<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Produksi;

class DashboardController extends Controller
{
    public function index()
    {
        // Inventory items
        $inventoryCount = Barang::count();

        // Production output (misal total produksi)
        $productionCount = Produksi::sum('Jumlah_Berhasil');

        // Procurement cost (total biaya PO yang sudah dikonfirmasi)
        $procurementCost = Pembelian::where('Status_Pembayaran', 'Confirmed')
                                ->sum('Total_Biaya');

        // Pending orders
        $pendingOrders = Pembelian::where('Status_Pembayaran', 'Pending')->count();

        // Reorder alerts
        $reorderAlerts = Barang::with('kategori')
            ->get()
            ->map(function($item){
                $status = 'In Stock';
                if($item->Stok <= ($item->Reorder_Point/2)){
                    $status = 'Critical Low';
                } elseif($item->Stok < $item->Reorder_Point){
                    $status = 'Near Reorder Point';
                }
                return [
                    'Nama_Bahan' => $item->Nama_Bahan,
                    'Stok' => $item->Stok,
                    'Reorder_Point' => $item->Reorder_Point,
                    'EOQ' => $item->EOQ,
                    'Status' => $status,
                ];
            })
            ->sortBy(function($i){ // Prioritaskan status rendah
                return match($i['Status']){
                    'Critical Low' => 1,
                    'Near Reorder Point' => 2,
                    default => 3
                };
            });

        return view('dashboard', compact(
            'inventoryCount',
            'productionCount',
            'procurementCost',
            'pendingOrders',
            'reorderAlerts'
        ));
    }
}
