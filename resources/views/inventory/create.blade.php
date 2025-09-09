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

        {{-- Stock --}}
        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="Stok" class="form-control" required min="0">
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

        {{-- Unit --}}
        <div class="mb-3">
            <label class="form-label">Unit</label>
            <select name="Unit" class="form-select" required>
                <option value="">-- Select Unit --</option>
                <option value="drum">Drum</option>
                <option value="pcs">Pcs</option>
                <option value="karung">Bag</option>
            </select>
        </div>

        {{-- Weight per Unit --}}
        <div class="mb-3">
            <label class="form-label">Weight per Unit</label>
            <input type="number" step="0.01" name="Berat" class="form-control" required>
        </div>

        {{-- Fixed Unit --}}
        <div class="mb-3">
            <label class="form-label">Unit Type</label>
            <input type="text" name="Satuan" class="form-control" value="kg/liter" readonly>
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
