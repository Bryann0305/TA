@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2>Buat Pesanan Produksi</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('pesanan-produksi.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="pelanggan_Id_Pelanggan" class="form-label">Pelanggan</label>
            <select name="pelanggan_Id_Pelanggan" id="pelanggan_Id_Pelanggan" class="form-select" required>
                <option value="">-- Pilih Pelanggan --</option>
                @foreach ($pelanggans as $pelanggan)
                    <option value="{{ $pelanggan->Id_Pelanggan }}" {{ old('pelanggan_Id_Pelanggan') == $pelanggan->Id_Pelanggan ? 'selected' : '' }}>
                        {{ $pelanggan->Nama_Pelanggan }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="user_Id_User" class="form-label">User</label>
            <select name="user_Id_User" id="user_Id_User" class="form-select" required>
                <option value="">-- Pilih User --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->Id_User }}" {{ old('user_Id_User') == $user->Id_User ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="Jumlah_Pesanan" class="form-label">Jumlah Pesanan</label>
            <input type="number" name="Jumlah_Pesanan" id="Jumlah_Pesanan" class="form-control" value="{{ old('Jumlah_Pesanan') }}" required>
        </div>
        <div class="mb-3">
            <label for="Tanggal_Pesanan" class="form-label">Tanggal Pesanan</label>
            <input type="date" name="Tanggal_Pesanan" id="Tanggal_Pesanan" class="form-control" value="{{ old('Tanggal_Pesanan') }}" required>
        </div>
        <div class="mb-3">
            <label for="Status" class="form-label">Status</label>
            <select name="Status" id="Status" class="form-select" required>
                <option value="On Progress" {{ old('Status') == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                <option value="Completed" {{ old('Status') == 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="Surat_Perintah_Produksi" class="form-label">Surat Perintah Produksi (Opsional)</label>
            <textarea name="Surat_Perintah_Produksi" id="Surat_Perintah_Produksi" class="form-control" rows="3">{{ old('Surat_Perintah_Produksi') }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('pesanan-produksi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection 