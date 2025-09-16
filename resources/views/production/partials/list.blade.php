@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2><strong>Production Management</strong></h2>
    <p>Plan, schedule, and track production orders</p>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#all"><i class="bi bi-list-ul me-1"></i>All Productions</a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="all">
            @forelse($produksi as $p)
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5>{{ $p->productionOrder->Nama_Produksi ?? 'Produksi #' . $p->Id_Produksi }}</h5>
                    <p>Status: 
                        <span class="badge bg-{{ $p->Status == 'completed' ? 'success' : ($p->Status == 'planned' ? 'warning' : 'primary') }}">
                            {{ ucfirst($p->Status) }}
                        </span>
                    </p>

                    {{-- Progress Bar --}}
                    @php
                        $target = $p->productionOrder->pesananProduksi->Jumlah_Pesanan ?? 0;
                        $progress = $target > 0 ? ($p->Jumlah_Berhasil / $target) * 100 : 0;
                    @endphp
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ $progress }}%"
                            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($progress, 0) }}%
                        </div>
                    </div>

                    <p>Jumlah Berhasil: {{ $p->Jumlah_Berhasil }} | Jumlah Gagal: {{ $p->Jumlah_Gagal }}</p>

                    {{-- Form Input Hasil per Item Bahan (hanya untuk status current) --}}
                    @if($p->Status != 'completed' && $p->details->count() > 0)
                    <form action="{{ route('production.updateHasil', $p->Id_Produksi) }}" method="POST">
                        @csrf
                        <h6>Input Hasil per Item:</h6>
                        @foreach($p->details as $detail)
                        <div class="mb-2 border p-2 rounded">
                            <p class="mb-1"><strong>{{ $detail->barang->Nama_Bahan ?? '-' }}</strong> (BOM: {{ $detail->billOfMaterial->Nama_BOM ?? '-' }})</p>
                            
                            <div class="mb-1">
                                <label>Jumlah Berhasil</label>
                                <input type="number" name="hasil[{{ $detail->Id_ProduksiDetail }}][berhasil]" class="form-control" required min="0" value="{{ old('hasil.'.$detail->Id_ProduksiDetail.'.berhasil', $detail->Jumlah_Berhasil ?? 0) }}">
                            </div>

                            <div class="mb-1">
                                <label>Jumlah Gagal</label>
                                <input type="number" name="hasil[{{ $detail->Id_ProduksiDetail }}][gagal]" class="form-control" min="0" value="{{ old('hasil.'.$detail->Id_ProduksiDetail.'.gagal', $detail->Jumlah_Gagal ?? 0) }}">
                            </div>

                            <div class="mb-1">
                                <label>Keterangan Gagal</label>
                                <input type="text" name="hasil[{{ $detail->Id_ProduksiDetail }}][keterangan]" class="form-control" placeholder="Keterangan" value="{{ old('hasil.'.$detail->Id_ProduksiDetail.'.keterangan', $detail->Keterangan_Gagal ?? '') }}">
                            </div>
                        </div>
                        @endforeach

                        <button type="submit" class="btn btn-success btn-sm mt-2">Simpan Hasil</button>
                    </form>
                    @endif

                    {{-- Action Detail --}}
                    <a href="{{ route('production.show', $p->Id_Produksi) }}" class="btn btn-sm btn-info mt-2">Detail</a>
                </div>
            </div>
            @empty
                <p class="text-muted">Tidak ada data produksi.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
