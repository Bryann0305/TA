@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3 fw-bold">Production Detail</h2>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info Produksi --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h5 class="card-title mb-0">{{ $produksi->productionOrder->Nama_Produksi ?? 'Produksi #' . $produksi->Id_Produksi }}</h5>
                <span class="badge 
                    @if($produksi->Status == 'planned') bg-warning
                    @elseif($produksi->Status == 'current') bg-primary
                    @else bg-success
                    @endif">
                    {{ ucfirst($produksi->Status) }}
                </span>
            </div>

            <p><strong>Tanggal Produksi:</strong> {{ $produksi->Tanggal_Produksi }}</p>
            <p><strong>Penjadwalan:</strong> {{ $produksi->penjadwalan->Nama_Jadwal ?? '-' }}</p>
            <p><strong>Jumlah Berhasil:</strong> {{ $produksi->Jumlah_Berhasil }} | <strong>Gagal:</strong> {{ $produksi->Jumlah_Gagal }}</p>

            @php
                $target = $produksi->productionOrder->pesananProduksi->Jumlah_Pesanan ?? 0;
                $progress = $target > 0 ? ($produksi->Jumlah_Berhasil / $target) * 100 : 0;
            @endphp
            <div class="progress rounded-pill mb-3" style="height: 12px;">
                <div id="progress-bar" class="progress-bar bg-success" style="width: 0%" data-progress="{{ $progress }}"></div>
            </div>

            <a href="{{ route('production.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Detail BOM & Barang --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-semibold">Bill of Materials & Items</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>BOM</th>
                            <th>Barang</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($produksi->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->billOfMaterial->Nama_BOM ?? '-' }}</td>
                                <td>{{ $detail->barang->Nama_Bahan ?? '-' }}</td>
                                <td>{{ $detail->jumlah }}</td>
                                <td>
                                    <span class="badge 
                                        @if($detail->status == 'pending') bg-secondary
                                        @elseif($detail->status == 'completed') bg-success
                                        @else bg-info
                                        @endif">
                                        {{ ucfirst($detail->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Gagal Produksi --}}
    @if($produksi->gagalProduksi->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-semibold">Failed Production Records</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Total Gagal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($produksi->gagalProduksi as $index => $gagal)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $gagal->Total_Gagal }}</td>
                                    <td>{{ $gagal->Keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    (function(){
        var el = document.getElementById('progress-bar');
        if (el) {
            var value = parseFloat(el.getAttribute('data-progress')) || 0;
            value = Math.max(0, Math.min(100, value));
            el.style.width = value + '%';
        }
    })();
</script>
@endpush
