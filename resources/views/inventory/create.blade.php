@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-4">Add New Inventory Item</h2>

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Item Name</label>
            <input type="text" name="Nama_Bahan" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Category</label>
            <select name="kategori_Id_Kategori" class="form-select" required>
                <option value="">-- Select Category --</option>
                @foreach($kategori as $kat)
                    <option value="{{ $kat->Id_Kategori }}">{{ $kat->Nama_Kategori }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Type</label>
            <select name="Jenis" class="form-select" required>
                <option value="">-- Select Jenis --</option>
                <option value="Bahan_Baku">Bahan Baku</option>
                <option value="Produk">Produk</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="Status" class="form-select" required>
                <option value="">-- Select Status --</option>
                <option value="Aktif">Aktif</option>
                <option value="Tidak Aktif">Tidak Aktif</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="Stok" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Reorder Point</label>
            <input type="number" name="Reorder_Point" class="form-control">
        </div>

        <div class="mb-3">
            <label>EOQ</label>
            <input type="number" name="EOQ" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
