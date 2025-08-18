@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2><strong>Buat Pesanan Produksi</strong></h2>
    <p>Form untuk menambahkan pesanan produksi baru</p>

    {{-- Error Handling --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali inputan:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Create --}}
    <form action="{{ route('pesanan_produksi.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- User --}}
        <div class="mb-3">
            <label for="user_Id_User" class="form-label">User</label>
            <select name="user_Id_User" id="user_Id_User" class="form-select" required>
                <option value="">-- Pilih User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->Id_User }}" {{ old('user_Id_User') == $user->Id_User ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Pelanggan --}}
        <div class="mb-3">
            <label for="pelanggan_Id_Pelanggan" class="form-label">Pelanggan</label>
            <select name="pelanggan_Id_Pelanggan" id="pelanggan_Id_Pelanggan" class="form-select" required>
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($pelanggans as $pelanggan)
                    <option value="{{ $pelanggan->Id_Pelanggan }}" {{ old('pelanggan_Id_Pelanggan') == $pelanggan->Id_Pelanggan ? 'selected' : '' }}>
                        {{ $pelanggan->Nama_Pelanggan }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Jumlah Pesanan --}}
        <div class="mb-3">
            <label for="Jumlah_Pesanan" class="form-label">Jumlah Pesanan</label>
            <input type="number" name="Jumlah_Pesanan" id="Jumlah_Pesanan" class="form-control" 
                   value="{{ old('Jumlah_Pesanan') }}" required min="1">
        </div>

        {{-- Tanggal Pesanan --}}
        <div class="mb-3">
            <label for="Tanggal_Pesanan" class="form-label">Tanggal Pesanan</label>
            <input type="date" name="Tanggal_Pesanan" id="Tanggal_Pesanan" class="form-control" 
                   value="{{ old('Tanggal_Pesanan') }}" required>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="Status" class="form-label">Status</label>
            <select name="Status" id="Status" class="form-select" required>
                <option value="Menunggu" {{ old('Status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="Diproses" {{ old('Status') == 'Diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="Selesai" {{ old('Status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>

        {{-- Surat Perintah Produksi (optional file upload) --}}
        <div class="mb-3">
            <label for="Surat_Perintah_Produksi" class="form-label">Surat Perintah Produksi</label>
            <input type="file" name="Surat_Perintah_Produksi" id="Surat_Perintah_Produksi" class="form-control" accept=".pdf,.jpg,.png">
            <small class="text-muted">Opsional. Format: PDF, JPG, atau PNG</small>
        </div>

        {{-- Tombol Submit --}}
        <div class="d-flex justify-content-end">
            <a href="{{ route('pesanan_produksi.index') }}" class="btn btn-secondary me-2">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
        </div>
    </form>
</div>
@endsection
