@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2><strong>Production Management</strong></h2>
    <p>Plan, schedule, and track production orders</p>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#planned">Planned</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#current">Current</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#completed">Completed</a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- PLANNED --}}
        <div class="tab-pane fade show active" id="planned">
            <div class="d-flex justify-content-between mb-3">
                <span class="fw-semibold">Planned Production Orders</span>
                <a href="{{ route('production.create') }}" class="btn btn-primary">New Production Order</a>
            </div>

            @forelse($produksiPlanned as $p)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>{{ $p->productionOrder->Nama_Produksi ?? 'Produksi #' . $p->Id_Produksi }}</h5>
                        <p>Status: <span class="badge bg-warning">{{ ucfirst($p->Status) }}</span></p>
                        <p>Tanggal Produksi: {{ $p->Tanggal_Produksi }}</p>
                        <p>Penjadwalan: {{ $p->penjadwalan->Nama_Jadwal ?? '-' }}</p>
                        @php
                            $target = $p->productionOrder->pesananProduksi->Jumlah_Pesanan ?? 0;
                            $progress = $target > 0 ? ($p->Jumlah_Berhasil / $target) * 100 : 0;
                        @endphp
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%">
                                {{ number_format($progress,0) }}%
                            </div>
                        </div>
                        {{-- BOM & Barang --}}
                        @if($p->details->count() > 0)
                            <ul>
                                @foreach($p->details as $detail)
                                    <li>
                                        {{ $detail->billOfMaterial->Nama_BOM ?? '-' }} - 
                                        {{ $detail->barang->Nama_Bahan ?? '-' }} 
                                        ({{ $detail->jumlah }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('production.show', $p->Id_Produksi) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('production.approve', $p->Id_Produksi) }}" class="btn btn-sm btn-success">Approve</a>
                    </div>
                </div>
            @empty
                <p class="text-muted">Tidak ada produksi planned.</p>
            @endforelse
        </div>

        {{-- CURRENT --}}
        <div class="tab-pane fade" id="current">
            <div class="d-flex justify-content-between mb-3">
                <span class="fw-semibold">Currently Running Production Orders</span>
            </div>

            @forelse($produksiCurrent as $p)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>{{ $p->productionOrder->Nama_Produksi ?? 'Produksi #' . $p->Id_Produksi }}</h5>
                        <p>Status: <span class="badge bg-primary">{{ ucfirst($p->Status) }}</span></p>
                        <p>Tanggal Produksi: {{ $p->Tanggal_Produksi }}</p>
                        <!-- <p>Penjadwalan: {{ $p->penjadwalan->Nama_Jadwal ?? '-' }}</p>
                        @php
                            $target = $p->productionOrder->pesananProduksi->Jumlah_Pesanan ?? 0;
                            $progress = $target > 0 ? ($p->Jumlah_Berhasil / $target) * 100 : 0;
                        @endphp
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%">
                                {{ number_format($progress,0) }}%
                            </div>
                        </div> -->
                        {{-- BOM & Barang --}}
                        @if($p->details->count() > 0)
                            <ul>
                                @foreach($p->details as $detail)
                                    <li>
                                        {{ $detail->billOfMaterial->Nama_BOM ?? '-' }} - 
                                        {{ $detail->barang->Nama_Bahan ?? '-' }} 
                                        ({{ $detail->jumlah }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('production.show', $p->Id_Produksi) }}" class="btn btn-sm btn-info">Detail</a>
                        <form action="{{ route('production.complete', $p->Id_Produksi) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="Jumlah_Berhasil" value="{{ $p->Jumlah_Berhasil ?? 0 }}">
                            <button type="submit" class="btn btn-sm btn-success">Complete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-muted">Tidak ada produksi sedang berjalan.</p>
            @endforelse
        </div>

        {{-- COMPLETED --}}
        <div class="tab-pane fade" id="completed">
            @forelse($produksiCompleted as $p)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>{{ $p->productionOrder->Nama_Produksi ?? 'Produksi #' . $p->Id_Produksi }}</h5>
                        <p>Status: <span class="badge bg-success">{{ ucfirst($p->Status) }}</span></p>
                        <p>Tanggal Produksi: {{ $p->Tanggal_Produksi }}</p>
                        <p>Penjadwalan: {{ $p->penjadwalan->Nama_Jadwal ?? '-' }}</p>
                        <p>Jumlah Berhasil: {{ $p->Jumlah_Berhasil }} | Jumlah Gagal: {{ $p->Jumlah_Gagal }}</p>
                        @php
                            $target = $p->productionOrder->pesananProduksi->Jumlah_Pesanan ?? 0;
                            $progress = $target > 0 ? ($p->Jumlah_Berhasil / $target) * 100 : 0;
                        @endphp
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%">
                                {{ number_format($progress,0) }}%
                            </div>
                        </div>
                        {{-- BOM & Barang --}}
                        @if($p->details->count() > 0)
                            <ul>
                                @foreach($p->details as $detail)
                                    <li>
                                        {{ $detail->billOfMaterial->Nama_BOM ?? '-' }} - 
                                        {{ $detail->barang->Nama_Bahan ?? '-' }} 
                                        ({{ $detail->jumlah }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('production.show', $p->Id_Produksi) }}" class="btn btn-sm btn-info">Detail</a>
                    </div>
                </div>
            @empty
                <p class="text-muted">Tidak ada produksi selesai.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
