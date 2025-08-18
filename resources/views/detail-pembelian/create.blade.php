@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h4>Tambah Detail Pembelian - PO #{{ $pembelian->Id_Pembelian }}</h4>

    <a href="{{ route('procurement.show', $pembelian->Id_Pembelian) }}" class="btn btn-secondary mb-3">Kembali ke PO</a>

    <form action="{{ route('detail_pembelian.store', $pembelian->Id_Pembelian) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="bahan_baku_Id_Bahan" class="form-label">Barang / Bahan Baku</label>
            <select name="bahan_baku_Id_Bahan" id="bahan_baku_Id_Bahan" class="form-control" required>
                <option value="">-- Pilih Barang --</option>
                @foreach($barang as $b)
                    <option value="{{ $b->Id_Bahan }}">{{ $b->Nama_Bahan }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="gudang_Id_Gudang" class="form-label">Gudang</label>
            <select name="gudang_Id_Gudang" id="gudang_Id_Gudang" class="form-control" required>
                <option value="">-- Pilih Gudang --</option>
                @foreach($gudang as $g)
                    <option value="{{ $g->Id_Gudang }}">{{ $g->Nama_Gudang }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="Jumlah" class="form-label">Jumlah</label>
            <input type="number" name="Jumlah" id="Jumlah" class="form-control" min="1" required>
        </div>

        <div class="mb-3">
            <label for="Harga_Keseluruhan" class="form-label">Harga Keseluruhan</label>
            <input type="number" name="Harga_Keseluruhan" id="Harga_Keseluruhan" class="form-control" min="0" required>
        </div>

        <div class="mb-3">
            <label for="Keterangan" class="form-label">Keterangan</label>
            <textarea name="Keterangan" id="Keterangan" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Detail</button>
    </form>
</div>
@endsection
