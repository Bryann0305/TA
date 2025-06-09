<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Barang;
use App\Models\Produksi;
use App\Models\Pembelian;
use App\Models\PesananProduksi;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_barang' => \App\Models\Barang::count(),
            'total_produksi' => \App\Models\Produksi::count(),
            'total_pembelian' => \App\Models\Pembelian::count(),
            'total_pesanan_produksi' => \App\Models\PesananProduksi::count(),
        ];

        $alerts = \App\Models\Barang::all()->map(function ($item) {
            return [
                'material' => $item->Nama_Bahan,
                'stock' => $item->Stok,
                'rop' => $item->Reorder_Point ?? '-',
                'eoq' => $item->EOQ ?? '-',
                'status' => ($item->Stok < ($item->Reorder_Point ?? 0)) ? 'below' : 'near',
            ];
        });

        return view('dashboard.admin', [
            'user' => $user,
            'stats' => $stats,
            'alerts' => $alerts
        ]);
    }
}
