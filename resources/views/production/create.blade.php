@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-4">Create New Production Order</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('production.store') }}">
        @csrf
        <div class="mb-3">
            <label for="Nama_Produksi" class="form-label">Nama Produksi</label>
            <input type="text" name="Nama_Produksi" id="Nama_Produksi" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="Jumlah_Produksi" class="form-label">Jumlah Produksi</label>
            <input type="number" name="Jumlah_Produksi" id="Jumlah_Produksi" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
            <label for="Tanggal_Produksi" class="form-label">Tanggal Produksi</label>
            <input type="date" name="Tanggal_Produksi" id="Tanggal_Produksi" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="Status" class="form-label">Status</label>
            <select name="Status" id="Status" class="form-select" required>
                <option value="planned">Planned</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="bill_of_material_Id_bill_of_material" class="form-label">Bill of Material</label>
            <select name="bill_of_material_Id_bill_of_material" id="bill_of_material_Id_bill_of_material" class="form-select" required>
                <option value="">-- Pilih BOM --</option>
                @foreach($boms as $bom)
                    <option value="{{ $bom->Id_bill_of_material }}">{{ $bom->Nama_bill_of_material }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('production.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
