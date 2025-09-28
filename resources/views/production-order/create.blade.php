@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Production Order</h2>
        <a href="{{ route('production_order.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('production_order.store') }}" method="POST">
        @csrf

        {{-- Production Request --}}
        <div class="mb-3">
            <label for="pesanan_produksi_id" class="form-label">Production Request</label>
            <select name="pesanan_produksi_id" class="form-select" required>
                <option value="">-- Select Request --</option>
                @foreach($pesanan as $p)
                    <option value="{{ $p->Id_Pesanan }}">{{ $p->Nomor_Pesanan }} ({{ $p->Jumlah_Pesanan }})</option>
                @endforeach
            </select>
        </div>


        {{-- Production Name --}}
        <div class="mb-3">
            <label for="Nama_Produksi" class="form-label">Production Name</label>
            <input type="text" name="Nama_Produksi" class="form-control" required>
        </div>

        {{-- Production Date --}}
        <div class="mb-3">
            <label for="Tanggal_Produksi" class="form-label">Production Date</label>
            <input type="date" name="Tanggal_Produksi" class="form-control" required>
        </div>

        {{-- Start Date --}}
        <div class="mb-3">
            <label for="Tanggal_Mulai" class="form-label">Start Date</label>
            <input type="date" name="Tanggal_Mulai" class="form-control" required>
        </div>

        {{-- End Date --}}
        <div class="mb-3">
            <label for="Tanggal_Selesai" class="form-label">End Date</label>
            <input type="date" name="Tanggal_Selesai" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Save Order
        </button>
    </form>
</div>
@endsection
