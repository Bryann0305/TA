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
                <p class="mb-0"><strong>Jadwal:</strong> {{ $produksi->penjadwalan->Nama_Jadwal ?? $produksi->penjadwalan->nama_jadwal }}</p>
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
                                @endphp
                                <tr>
                                    <td>{{ $bom->Nama_BOM ?? 'BOM #'.$bomId }}</td>
                                    <td>{{ $details->sum('jumlah') }}</td>
                                    <td>
                                        <ul class="mb-0">
                                            @foreach($bom->barangs ?? [] as $barang)
                                                <li>{{ $barang->Nama_Bahan ?? 'Barang #'.$barang->Id_Barang }}</li>
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
        {{-- Tombol Edit hanya muncul kalau belum di-approve --}}
        @if($produksi->Status !== 'approved')
            <a href="{{ route('production.edit', $produksi->Id_Produksi) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i>
            </a>
        @endif
        {{-- Tombol Hapus dihilangkan --}}
    </div>
</div>
@endsection
