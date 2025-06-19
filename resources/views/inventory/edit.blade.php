@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-4">Edit Inventory Item</h2>

    <form action="{{ route('inventory.update', $item->Id_Bahan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Item Name</label>
            <input type="text" name="Nama_Bahan" class="form-control"
                   value="{{ old('Nama_Bahan', $item->Nama_Bahan) }}" required>
        </div>

        <div class="mb-3">
            <label>Category</label>
            <select name="kategori_Id_Kategori" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach($kategori as $kat)
                    <option value="{{ $kat->Id_Kategori }}" 
                        {{ old('kategori_Id_Kategori', $item->kategori_Id_Kategori) == $kat->Id_Kategori ? 'selected' : '' }}>
                        {{ $kat->Nama_Kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Type</label>
            <select name="Jenis" class="form-select" required>
                <option value="">-- Select Jenis --</option>
                <option value="Bahan_Baku" {{ old('Jenis', $item->Jenis) == 'Bahan_Baku' ? 'selected' : '' }}>Bahan Baku</option>
                <option value="Produk" {{ old('Jenis', $item->Jenis) == 'Produk' ? 'selected' : '' }}>Produk</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="Status" class="form-select" required>
                <option value="">-- Select Status --</option>
                <option value="Aktif" {{ old('Status', $item->Status) == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Tidak Aktif" {{ old('Status', $item->Status) == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="Stok" class="form-control"
                   value="{{ old('Stok', $item->Stok) }}" required>
        </div>

        <div class="mb-3">
            <label>Reorder Point</label>
            <input type="number" name="Reorder_Point" class="form-control"
                   value="{{ old('Reorder_Point', $item->Reorder_Point) }}">
        </div>

        <div class="mb-3">
            <label>EOQ</label>
            <input type="number" name="EOQ" class="form-control"
                   value="{{ old('EOQ', $item->EOQ) }}">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
