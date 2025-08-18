@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2>New Purchase Order</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('procurement.store') }}" method="POST">
        @csrf

        {{-- Supplier --}}
        <div class="mb-3">
            <label for="supplier_Id_Supplier" class="form-label">Supplier</label>
            <select name="supplier_Id_Supplier" id="supplier_Id_Supplier" class="form-select" required>
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->Id_Supplier }}">{{ $supplier->Nama_Supplier }}</option>
                @endforeach
            </select>
        </div>

        {{-- Gudang --}}
        <div class="mb-3">
            <label for="gudang_Id_Gudang" class="form-label">Gudang</label>
            <select name="gudang_Id_Gudang" id="gudang_Id_Gudang" class="form-select" required>
                <option value="">-- Select Gudang --</option>
                @foreach($gudangs as $gudang)
                    <option value="{{ $gudang->Id_Gudang }}">{{ $gudang->Nama_Gudang }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nama Barang --}}
        <div class="mb-3">
            <label class="form-label">Nama Barang</label>
            <select name="Nama_Barang[]" class="form-select" multiple required>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->Id_Bahan }}">{{ $barang->Nama_Bahan }}</option>
                @endforeach
            </select>
            <small class="text-muted">Hold Ctrl (Windows) / Cmd (Mac) to select multiple</small>
        </div>

        {{-- Tanggal Pemesanan --}}
        <div class="mb-3">
            <label for="Tanggal_Pemesanan" class="form-label">Tanggal Pemesanan</label>
            <input type="date" name="Tanggal_Pemesanan" id="Tanggal_Pemesanan" class="form-control" required>
        </div>

        {{-- Tanggal Kedatangan --}}
        <div class="mb-3">
            <label for="Tanggal_Kedatangan" class="form-label">Tanggal Kedatangan</label>
            <input type="date" name="Tanggal_Kedatangan" id="Tanggal_Kedatangan" class="form-control">
        </div>

        {{-- Total Biaya --}}
        <div class="mb-3">
            <label for="Total_Biaya" class="form-label">Total Biaya</label>
            <input type="number" name="Total_Biaya" id="Total_Biaya" class="form-control" required>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="mb-3">
            <label for="Metode_Pembayaran" class="form-label">Metode Pembayaran</label>
            <input type="text" name="Metode_Pembayaran" id="Metode_Pembayaran" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Purchase Order</button>
    </form>
</div>
@endsection
