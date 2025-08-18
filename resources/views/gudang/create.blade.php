@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2>Tambah Gudang</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('gudang.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="Nama_Gudang" class="form-label">Nama Gudang</label>
            <input type="text" class="form-control" id="Nama_Gudang" name="Nama_Gudang" value="{{ old('Nama_Gudang') }}" required>
        </div>
        <div class="mb-3">
            <label for="Lokasi" class="form-label">Lokasi</label>
            <input type="text" class="form-control" id="Lokasi" name="Lokasi" value="{{ old('Lokasi') }}" required>
        </div>
        <div class="mb-3">
            <label for="Kapasitas" class="form-label">Kapasitas</label>
            <input type="number" class="form-control" id="Kapasitas" name="Kapasitas" value="{{ old('Kapasitas') }}" min="0" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
