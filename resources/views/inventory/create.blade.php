@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Item</h2>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf

        {{-- Item Name --}}
        <div class="mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" name="Nama_Bahan" class="form-control" required>
        </div>


        {{-- Type --}}
        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="Jenis" class="form-select" required>
                <option value="">-- Select Type --</option>
                <option value="Bahan_Baku">Raw Material</option>
                <option value="Produk">Finished Product</option>
            </select>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="kategori_Id_Kategori" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->Id_Kategori }}">{{ $k->Nama_Kategori }}</option>
                @endforeach
            </select>
        </div>

        {{-- Warehouse --}}
        <div class="mb-3">
            <label class="form-label">Warehouse</label>
            <select name="gudang_Id_Gudang" class="form-select" required>
                <option value="">-- Select Warehouse --</option>
                @foreach($gudangs as $gudang)
                    <option value="{{ $gudang->Id_Gudang }}">{{ $gudang->Nama_Gudang }}</option>
                @endforeach
            </select>
        </div>

        {{-- Satuan --}}
        <div class="mb-3">
            <label class="form-label">Satuan</label>
            <select name="Satuan" class="form-select" required>
                <option value="">-- Select Satuan --</option>
                @foreach($satuanOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i> Save Item
            </button>
        </div>
    </form>
</div>
@endsection