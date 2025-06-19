@extends('layouts.sidebar')

@section('content')
<form action="{{ route('production.store') }}" method="POST">
    @csrf
    <label>Nama Produksi:</label>
    <input type="text" name="Nama_Produksi" required>

    <label>Jumlah Produksi:</label>
    <input type="number" name="Jumlah_Produksi" required>

    <label>Status:</label>
    <select name="Status">
        <option value="Planning">Planning</option>
        <option value="In Progress">In Progress</option>
        <option value="Selesai">Selesai</option>
    </select>

    <label>Pilih BOM:</label>
    <select name="bill_of_material_Id_bill_of_material" required>
        @foreach ($boms as $bom)
            <option value="{{ $bom->Id_bill_of_material }}">{{ $bom->Nama_BOM }}</option>
        @endforeach
    </select>

    <button type="submit">Simpan</button>
</form>

@endsection
