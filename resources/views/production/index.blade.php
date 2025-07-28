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

    {{-- Tab Navigation --}}
    <ul class="nav nav-tabs mb-3" id="productionTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="current-tab" data-bs-toggle="tab" href="#current" role="tab">Current</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="planned-tab" data-bs-toggle="tab" href="#planned" role="tab">Planned</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab">Completed</a>
        </li>
    </ul>

    {{-- Tab Content --}}
    <div class="tab-content" id="productionTabsContent">
        {{-- CURRENT --}}
        <div class="tab-pane fade show active" id="current" role="tabpanel" aria-labelledby="current-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-semibold">Currently Running Production Orders</span>
                                <a href="{{ route('production.create') }}" class="btn btn-primary">New Production Order</a>
            </div>
            @include('production.partials.list', ['produksi' => $produksiBerjalan])
        </div>

        {{-- PLANNED --}}
        <div class="tab-pane fade" id="planned" role="tabpanel" aria-labelledby="planned-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-semibold">Planned Production Orders</span>
                <a href="{{ route('production.create') }}" class="btn btn-primary">New Production Order</a>
            </div>
            @include('production.partials.list', ['produksi' => $produksiDirencanakan])
        </div>

        {{-- COMPLETED --}}
        <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-semibold">Completed Production Orders</span>
            </div>
            @include('production.partials.list', ['produksi' => $produksiSelesai])
        </div>
    </div>

    <hr>

    {{-- BOM List --}}
    <h4 class="mt-4">Daftar BOM</h4>
    @forelse($boms as $bom)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $bom->Nama_bill_of_material ?? 'BOM #' . $bom->Id_bill_of_material }}</h5>
                <p class="card-text text-muted">
                    Status:
                    <span class="badge bg-{{ 
                        $bom->Status == 'approved' ? 'success' : 
                        ($bom->Status == 'draft' ? 'warning' : 'danger') 
                    }}">
                        {{ ucfirst($bom->Status ?? '-') }}
                    </span>
                </p>

                @if($bom->barang->isEmpty())
                    <p class="text-muted">Tidak ada bahan baku yang terhubung.</p>
                @else
                    <ul class="list-group list-group-flush mt-2">
                        @foreach($bom->barang as $barang)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $barang->Nama_Bahan }}
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
    @empty
        <p class="text-muted">Belum ada data BOM.</p>
    @endforelse
</div>
@endsection
