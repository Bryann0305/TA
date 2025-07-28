@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2><strong>Tambah Produksi</strong></h2>
    <p>Input pesanan, jadwal, dan produksi dalam satu halaman</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('production.store') }}" method="POST">
        @csrf

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="productionFormTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pesanan-tab" data-bs-toggle="tab" href="#pesanan" role="tab">Pesanan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="jadwal-tab" data-bs-toggle="tab" href="#jadwal" role="tab">Jadwal</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="produksi-tab" data-bs-toggle="tab" href="#produksi" role="tab">Produksi</a>
            </li>
        </ul>

        {{-- Tab Content --}}
        <div class="tab-content" id="productionFormTabsContent">
            {{-- Tab Pesanan --}}
            <div class="tab-pane fade show active" id="pesanan" role="tabpanel">
                <div class="mb-3">
                    <label>Jumlah Pesanan</label>
                    <input type="number" name="Jumlah_Pesanan" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Tanggal Pesanan</label>
                    <input type="date" name="Tanggal_Pesanan" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Surat Perintah Produksi</label>
                    <input type="text" name="Surat_Perintah_Produksi" class="form-control">
                </div>
            </div>

            {{-- Tab Jadwal --}}
            <div class="tab-pane fade" id="jadwal" role="tabpanel">
                <div class="mb-3">
                    <label>Tanggal Mulai</label>
                    <input type="date" name="Tanggal_Mulai" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Tanggal Selesai</label>
                    <input type="date" name="Tanggal_Selesai" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Status Jadwal</label>
                    <select name="Status_Jadwal" class="form-control">
                        <option value="Direncanakan">Direncanakan</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Tertunda">Tertunda</option>
                    </select>
                </div>
            </div>

            {{-- Tab Produksi --}}
            <div class="tab-pane fade" id="produksi" role="tabpanel">
                <div class="mb-3">
                    <label>Hasil Produksi</label>
                    <input type="text" name="Hasil_Produksi" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Status Produksi</label>
                    <select name="Status" class="form-control" required>
                        <option value="Menunggu">Menunggu</option>
                        <option value="Berjalan">Berjalan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Keterangan</label>
                    <textarea name="Keterangan" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label>Bill of Material</label>
                    <select name="bill_of_material_Id_bill_of_material" class="form-control" required>
                        @foreach($boms as $bom)
                            <option value="{{ $bom->Id_bill_of_material }}">{{ $bom->Nama_bill_of_material }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label>Bahan Baku</label>
                    <select name="bahan_baku_Id_Bahan" class="form-control">
                        @foreach($barang as $b)
                            <option value="{{ $b->Id_Bahan }}">{{ $b->Nama_Bahan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan Produksi</button>
            <a href="{{ route('production.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
