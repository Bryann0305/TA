<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        $data = [
            'stockValue' => 152480,
            'itemsBelowROP' => 5,
            'criticalItems' => 2,
            'inventoryTurns' => 4.3,
            'inventoryGrowth' => 7.2,
            'categories' => [
                ['name' => 'Raw Materials', 'value' => 82300, 'percentage' => 54],
                ['name' => 'Additives', 'value' => 28750, 'percentage' => 19],
                ['name' => 'Pigments', 'value' => 24680, 'percentage' => 16],
                ['name' => 'Solvents', 'value' => 16750, 'percentage' => 11],
            ],
            'eoqSummary' => [
                ['material' => 'Raw Epoxy Resin', 'demand' => '2,400 kg', 'qty' => '500 kg', 'rop' => '150 kg', 'holding' => '$1,250', 'orderCost' => '$216', 'total' => '$1,466'],
                ['material' => 'Titanium Dioxide', 'demand' => '1,200 kg', 'qty' => '350 kg', 'rop' => '100 kg', 'holding' => '$700', 'orderCost' => '$103', 'total' => '$803'],
                // Tambahkan jika ada bahan lainnya
            ]
        ];

        return view('reports.index', compact('data'));
    }
}
