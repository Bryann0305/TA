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
            <p><strong>BOM Name:</strong> {{ $bom->Nama_bill_of_material }}</p>
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

    {{-- Detail Items --}}
    <h5>Items in BOM</h5>
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bom->barangs as $index => $b)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $b->Nama_Bahan }}</td>
                    <td>{{ $b->kategori->Nama_Kategori ?? '-' }}</td>
                    <td>{{ $b->pivot->Jumlah_Bahan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No items available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
