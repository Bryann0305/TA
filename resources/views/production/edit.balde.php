{{-- Nama Produksi --}}
<div class="mb-3">
    <label for="Nama_Produksi" class="form-label">Nama Produksi</label>
    <input type="text" name="Nama_Produksi" class="form-control" value="{{ old('Nama_Produksi', $produksi->Nama_Produksi) }}" required>
</div>

{{-- Jumlah Berhasil --}}
<div class="mb-3">
    <label for="Jumlah_Berhasil" class="form-label">Jumlah Berhasil</label>
    <input type="number" name="Jumlah_Berhasil" class="form-control" value="{{ old('Jumlah_Berhasil', $produksi->Jumlah_Berhasil) }}" min="0" required>
</div>

{{-- Jumlah Gagal --}}
<div class="mb-3">
    <label for="Jumlah_Gagal" class="form-label">Jumlah Gagal</label>
    <input type="number" name="Jumlah_Gagal" class="form-control" value="{{ old('Jumlah_Gagal', $produksi->Jumlah_Gagal) }}" min="0" required>
</div>
