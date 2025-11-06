@extends('layouts.sidebar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="fw-bold">Detail Produksi #{{ $produksi->Id_Produksi }}</h2>
    <a href="{{ route('production.index') }}" class="btn btn-secondary">
        Back
    </a>
</div>

{{-- Info Utama --}}
<div class="card mb-3">
    <div class="card-body">
        <h5>{{ $produksi->productionOrder->Nama_Produksi ?? 'Produksi' }}</h5>
        <p class="mb-1">
            <strong>Status:</strong>
            <span class="badge 
                @if($produksi->Status == 'planned') bg-warning text-dark
                @elseif($produksi->Status == 'current') bg-primary
                @elseif($produksi->Status == 'completed') bg-success
                @elseif($produksi->Status == 'approved') bg-info
                @else bg-secondary @endif">
                {{ ucfirst($produksi->Status) }}
            </span>
        </p>
        <p class="mb-1"><strong>Tanggal Produksi:</strong> {{ optional($produksi->Tanggal_Produksi)->format('d M Y') ?? '-' }}</p>
        
        {{-- SPP --}}
        <p class="mb-1"><strong>SPP:</strong> 
            {{ $produksi->productionOrder->Id ?? '-' }} 
            ({{ $produksi->productionOrder->Nama_Produksi ?? 'No Name' }})
        </p>

        {{-- Jadwal --}}
        @if($produksi->penjadwalan)
            <p class="mb-1"><strong>Jadwal:</strong> {{ $produksi->penjadwalan->Nama_Jadwal ?? $produksi->penjadwalan->nama_jadwal }}</p>
        @endif
    </div>
</div>

{{-- Permintaan Tahunan per Bahan Baku --}}
@php
    use Illuminate\Support\Facades\DB;
    $tahun = now()->year;
    $permintaanPerBahan = DB::table('detail_pesanan_produksi as dpp')
        ->join('barang as b', 'dpp.barang_Id_Bahan', '=', 'b.Id_Bahan')
        ->select('b.Nama_Bahan', DB::raw('SUM(dpp.Jumlah) as total_permintaan'))
        ->whereYear('dpp.created_at', $tahun)
        ->groupBy('b.Nama_Bahan')
        ->orderBy('b.Nama_Bahan')
        ->get();
@endphp

<div class="card mb-3">
    <div class="card-header fw-bold">Permintaan Tahunan per Bahan Baku ({{ $tahun }})</div>
    <div class="card-body">
        @if($permintaanPerBahan->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Nama Bahan</th>
                            <th>Jumlah Permintaan (Unit)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permintaanPerBahan as $p)
                            <tr>
                                <td>{{ $p->Nama_Bahan }}</td>
                                <td>{{ number_format($p->total_permintaan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted mb-0">Belum ada data permintaan bahan baku tahun {{ $tahun }}.</p>
        @endif
    </div>
</div>

{{-- Detail Produksi Per BOM --}}
<div class="card mb-3">
    <div class="card-header fw-bold">Detail Produksi (Per BOM)</div>
    <div class="card-body">
        @if($produksi->details && $produksi->details->count())
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>BOM</th>
                            <th>Jumlah Dipakai</th>
                            <th>Barang dari BOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produksi->details->groupBy('bill_of_material_id') as $bomId => $details)
                            @php
                                $bom = $details->first()->billOfMaterial;
                                $jumlahBahanBaku = $bom ? $bom->barangs->count() : 0;
                                $quantityPesanan = optional($produksi->productionOrder->pesananProduksi)->Jumlah_Pesanan ?? 1;
                            @endphp
                            <tr>
                                <td>{{ $bom->Nama_bill_of_material ?? 'BOM #'.$bomId }}</td>
                                <td>{{ $jumlahBahanBaku }} bahan baku</td>
                                <td>
                                    <ul class="mb-0">
                                        @foreach($bom->barangs ?? [] as $barang)
                                            <li>{{ $barang->Nama_Bahan ?? 'Barang #'.$barang->Id_Bahan }} 
                                                ({{ $barang->pivot->Jumlah_Bahan * $quantityPesanan }} {{ $barang->Satuan }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">Tidak ada detail produksi.</p>
        @endif
    </div>
</div>

{{-- Aksi --}}
<div class="d-flex gap-2">
    @if($produksi->Status !== 'approved')
        <a href="{{ route('production.edit', $produksi->Id_Produksi) }}" class="btn btn-warning">
            <i class="bi bi-pencil-square me-1"></i>
        </a>
    @endif
</div>
@endsection
