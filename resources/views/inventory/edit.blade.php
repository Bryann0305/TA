@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Item: {{ $item->Nama_Bahan }}</h2>
        <a href="{{ route('inventory.showGudang', $item->gudang_Id_Gudang) }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

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

    <form action="{{ route('inventory.update', $item->Id_Bahan) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Item Name --}}
        <div class="mb-3">
            <label class="form-label">Item Name</label>
            <input type="text" name="Nama_Bahan" class="form-control" 
                   value="{{ old('Nama_Bahan', $item->Nama_Bahan) }}" required>
        </div>

        {{-- Stock --}}
        <div class="mb-3">
            <label class="form-label">Stock</label>
            <input type="number" name="Stok" class="form-control" 
                   value="{{ old('Stok', $item->Stok) }}" required min="0" step="0.01">
        </div>

        {{-- EOQ --}}
        <div class="mb-3">
            <label class="form-label">EOQ</label>
            <input type="number" name="EOQ" class="form-control"
                   value="{{ old('EOQ', $item->EOQ ?? 0) }}" min="0" step="0.01">
        </div>

        {{-- Reorder Point --}}
        <div class="mb-3">
            <label class="form-label">Reorder Point (ROP)</label>
            <input type="number" name="ROP" class="form-control"
                   value="{{ old('ROP', $item->ROP ?? 0) }}" min="0" step="0.01">
        </div>

        {{-- Type --}}
        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="Jenis" class="form-select" required>
                <option value="">-- Select Type --</option>
                <option value="Bahan_Baku" {{ old('Jenis', $item->Jenis) == 'Bahan_Baku' ? 'selected' : '' }}>
                    Raw Material
                </option>
                <option value="Produk" {{ old('Jenis', $item->Jenis) == 'Produk' ? 'selected' : '' }}>
                    Finished Product
                </option>
            </select>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="kategori_Id_Kategori" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->Id_Kategori }}" 
                        {{ old('kategori_Id_Kategori', $item->kategori_Id_Kategori) == $k->Id_Kategori ? 'selected' : '' }}>
                        {{ $k->Nama_Kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Warehouse --}}
        <div class="mb-3">
            <label class="form-label">Warehouse</label>
            <select name="gudang_Id_Gudang" class="form-select" required>
                <option value="">-- Select Warehouse --</option>
                @foreach($gudangs as $g)
                    <option value="{{ $g->Id_Gudang }}" 
                        {{ old('gudang_Id_Gudang', $item->gudang_Id_Gudang) == $g->Id_Gudang ? 'selected' : '' }}>
                        {{ $g->Nama_Gudang }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Satuan --}}
        <div class="mb-3">
            <label class="form-label">Satuan</label>
            <select name="Satuan" class="form-select" required>
                <option value="">-- Select Satuan --</option>
                @foreach($satuanOptions as $value => $label)
                    <option value="{{ $value }}" {{ old('Satuan', $item->Satuan) == $value ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i> Update Item
            </button>
        </div>
    </form>
</div>
@endsection
