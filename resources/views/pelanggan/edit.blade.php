@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Pelanggan</h1>

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

    <form action="{{ route('pelanggan.update', $pelanggan->Id_Pelanggan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="Nama_Pelanggan" class="form-label">Nama Pelanggan</label>
            <input type="text" name="Nama_Pelanggan" class="form-control" 
                   value="{{ old('Nama_Pelanggan', $pelanggan->Nama_Pelanggan) }}" required>
        </div>

        <div class="mb-3">
            <label for="Alamat" class="form-label">Alamat</label>
            <textarea name="Alamat" class="form-control" required>{{ old('Alamat', $pelanggan->Alamat) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="Nomor_Telp" class="form-label">No Telepon</label>
            <input type="text" name="Nomor_Telp" class="form-control" 
                   value="{{ old('Nomor_Telp', $pelanggan->Nomor_Telp) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
