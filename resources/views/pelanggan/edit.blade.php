@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Pelanggan</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Oops!</strong> Ada kesalahan pada input:<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="Nama_Pelanggan" class="form-label">Nama Pelanggan</label>
            <input type="text" name="Nama_Pelanggan" class="form-control" value="{{ old('Nama_Pelanggan') }}" required>
        </div>

        <div class="mb-3">
            <label for="Alamat" class="form-label">Alamat</label>
            <textarea name="Alamat" class="form-control" required>{{ old('Alamat') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="Nomor_Telp" class="form-label">No Telepon</label>
            <input type="text" name="Nomor_Telp" class="form-control" value="{{ old('Nomor_Telp') }}" required>
        </div>

        <div class="mb-3">
            <label for="Status" class="form-label">Status</label>
            <select name="Status" class="form-control" required>
                <option value="Aktif" {{ old('Status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Tidak Aktif" {{ old('Status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
