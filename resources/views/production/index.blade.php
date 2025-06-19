@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2><strong>Production Management</strong></h2>
    <p>Plan, schedule, and track production orders</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#">Current Production</a>
        </li>
        <li class="nav-item"><a class="nav-link disabled">Planned Orders</a></li>
        <li class="nav-item"><a class="nav-link disabled">Completed Orders</a></li>
    </ul>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="fw-semibold">Currently Running Production Orders</span>
        <a href="{{ route('production.create') }}" class="btn btn-primary">New Production Order</a>
    </div>

    @foreach($produksi as $order)
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $order->Nama_Produksi }}</h5>
            <p class="card-text mb-1">
                <i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($order->Tanggal_Produksi)->format('M d, Y') }}
                &nbsp; • &nbsp; Batch: {{ $order->Id_Produksi }}
                &nbsp; • &nbsp; {{ $order->Jumlah_Produksi }} L
            </p>
            <p class="card-text text-muted"><i class="bi bi-clock"></i> Status: {{ ucfirst($order->Status) }}</p>

            <span class="badge bg-light text-primary border border-primary">{{ ucfirst($order->Status) }}</span>

            <div class="progress mt-2" style="height: 6px;">
                @php
                    $progress = $order->Jumlah_Produksi > 0 ? ($order->Jumlah_Berhasil / $order->Jumlah_Produksi) * 100 : 0;
                @endphp
                <div class="progress-bar bg-primary" role="progressbar" data-width="{{ $progress }}" aria-valuenow="{{ $order->Jumlah_Berhasil }}" aria-valuemin="0" aria-valuemax="{{ $order->Jumlah_Produksi }}"></div>
                <script>
                    document.querySelector('[data-width="{{ $progress }}"]').style.width = '{{ $progress }}%';
                </script>
            </div>
            <small class="text-muted">{{ round($progress) }}% Complete</small>

            <div class="mt-3 d-flex gap-2">
                <a href="{{ route('production.edit', $order->Id_Produksi) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                <form method="POST" action="{{ route('production.update-status', $order->Id_Produksi) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="Status" value="completed">
                    <button class="btn btn-success btn-sm" {{ $order->Status === 'completed' ? 'disabled' : '' }}>
                        <i class="bi bi-check-circle"></i> Mark Complete
                    </button>
                </form>
                <a href="{{ route('production.show', $order->Id_Produksi) }}" class="btn btn-link btn-sm">View Details</a>
            </div>
        </div>
    </div>
    @endforeach

    @foreach($boms as $bom)
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $bom->Nama_BOM ?? '-' }}</h5>
            <p class="card-text text-muted">
                Status: 
                <span class="badge bg-{{ 
                    $bom->Status == 'approved' ? 'success' : 
                    ($bom->Status == 'draft' ? 'warning' : 'danger') 
                }}">
                    {{ ucfirst($bom->Status ?? '-') }}
                </span>
            </p>

            {{-- Tampilkan daftar bahan dan jumlahnya --}}
            @if($bom->barang->isEmpty())
                <p class="text-muted">Tidak ada bahan baku yang terhubung.</p>
            @else
                <ul class="list-group list-group-flush mt-2">
                    @foreach($bom->barang as $barang)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $barang->Nama_Barang }}
                            <span class="badge bg-primary rounded-pill">
                                {{ $barang->pivot->Jumlah ?? 0 }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <a href="{{ route('bill-of-materials.show', $bom->Id_bill_of_material) }}" class="btn btn-outline-primary btn-sm mt-3">
                Lihat Detail BOM
            </a>
        </div>
    </div>
@endforeach
</div>
@endsection
