@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>BOM Details #{{ $bom->Id_bill_of_material }}</h2>
        <a href="{{ route('bom.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- BOM Info --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>BOM Name:</strong> {{ str_replace('BOM - ', '', $bom->Nama_bill_of_material) }}</p>
            <p>
                <strong>Status:</strong>
                <span class="badge 
                    @if($bom->Status=='pending') bg-warning text-dark
                    @elseif($bom->Status=='confirmed') bg-success
                    @else bg-secondary
                    @endif
                ">{{ ucfirst($bom->Status) }}</span>
            </p>
        </div>
    </div>

    {{-- Finished Good Info --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">Finished Good (Produk)</h6>
        </div>
        <div class="card-body">
            @php
                // Extract product name from BOM name (remove "BOM - " prefix)
                $productName = str_replace('BOM - ', '', $bom->Nama_bill_of_material);
            @endphp
            <h5 class="text-primary">{{ $productName }}</h5>
            <p class="text-muted mb-0">This BOM is for producing: <strong>{{ $productName }}</strong></p>
        </div>
    </div>

    {{-- Raw Materials --}}
    <h5>Raw Materials (Bahan Baku)</h5>
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Satuan</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bom->barangs as $index => $material)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $material->Nama_Bahan }}</td>
                    <td>{{ $material->kategori->Nama_Kategori ?? '-' }}</td>
                    <td>{{ $material->Satuan ?? '-' }}</td>
                    <td>{{ $material->pivot->Jumlah_Bahan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No raw materials available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
