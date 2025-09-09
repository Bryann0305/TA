@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Production Order</h2>
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

    <form action="{{ route('production_order.update', $order->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Production Request (readonly) --}}
        <div class="mb-3">
            <label class="form-label">Production Request</label>
            <input type="text" class="form-control" 
                value="{{ $order->pesananProduksi->Nomor_Pesanan }} ({{ $order->pesananProduksi->Jumlah_Pesanan }})" 
                readonly>
        </div>

        {{-- Production Name --}}
        <div class="mb-3">
            <label for="Nama_Produksi" class="form-label">Production Name</label>
            <input type="text" name="Nama_Produksi" class="form-control" value="{{ $order->Nama_Produksi }}" required>
        </div>

        {{-- Production Date --}}
        <div class="mb-3">
            <label for="Tanggal_Produksi" class="form-label">Production Date</label>
            <input type="date" name="Tanggal_Produksi" class="form-control" 
                value="{{ \Carbon\Carbon::parse($order->Tanggal_Produksi)->format('Y-m-d') }}" required>
        </div>

        {{-- Start Date --}}
        <div class="mb-3">
            <label for="Tanggal_Mulai" class="form-label">Start Date</label>
            <input type="date" name="Tanggal_Mulai" class="form-control" 
                value="{{ $order->penjadwalan ? \Carbon\Carbon::parse($order->penjadwalan->Tanggal_Mulai)->format('Y-m-d') : '' }}" required>
        </div>

        {{-- End Date --}}
        <div class="mb-3">
            <label for="Tanggal_Selesai" class="form-label">End Date</label>
            <input type="date" name="Tanggal_Selesai" class="form-control" 
                value="{{ $order->penjadwalan ? \Carbon\Carbon::parse($order->penjadwalan->Tanggal_Selesai)->format('Y-m-d') : '' }}" required>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Update Order
        </button>
    </form>
</div>
@endsection
