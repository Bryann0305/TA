@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-4">Add New Inventory Item</h2>

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="Nama_Bahan" class="form-label">Item Name</label>
            <input type="text" name="Nama_Bahan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="Stok" class="form-label">Stock Quantity</label>
            <input type="number" name="Stok" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="Jenis" class="form-label">Type</label>
            <select name="Jenis" class="form-select" required>
            <option value="">-- Select Type --</option>
            <option value="Bahan_Baku">Bahan Baku</option>
            <option value="Produk">Produk</option>
        </select>

        </div>

        <div class="mb-3">
            <label for="Status" class="form-label">Status</label>
            <input type="text" name="Status" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="kategori_Id_Kategori" class="form-label">Category</label>
            <select name="kategori_Id_Kategori" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach(App\Models\Kategori::all() as $kategori)
                    <option value="{{ $kategori->Id_Kategori }}">{{ $kategori->Nama_Kategori }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">💾 Save Item</button>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">← Back</a>
    </form>
</div>
@endsection
