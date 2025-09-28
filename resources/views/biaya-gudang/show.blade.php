@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detail Biaya Gudang</h2>
        <a href="{{ route('biaya-gudang.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th style="width: 200px;">Gudang</th>
                    <td>{{ $biayaGudang->gudang->Nama_Gudang }}</td>
                </tr>
                <tr>
                    <th>Tanggal Biaya</th>
                    <td>{{ $biayaGudang->tanggal_biaya->format('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Biaya Sewa</th>
                    <td class="text-end fw-bold">Rp {{ number_format($biayaGudang->biaya_sewa, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Biaya Listrik</th>
                    <td class="text-end fw-bold">Rp {{ number_format($biayaGudang->biaya_listrik, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Biaya Air</th>
                    <td class="text-end fw-bold">Rp {{ number_format($biayaGudang->biaya_air, 0, ',', '.') }}</td>
                </tr>
                <tr class="table-primary">
                    <th>Total Biaya</th>
                    <td class="text-end fw-bold fs-5">Rp {{ number_format($biayaGudang->total_biaya, 0, ',', '.') }}</td>
                </tr>
                @if($biayaGudang->keterangan)
                <tr>
                    <th>Keterangan</th>
                    <td>{{ $biayaGudang->keterangan }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex gap-2">
        <a href="{{ route('biaya-gudang.edit', $biayaGudang->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
        <form action="{{ route('biaya-gudang.destroy', $biayaGudang->id) }}" method="POST" class="d-inline" 
              onsubmit="return confirm('Are you sure you want to delete this cost record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash-alt me-1"></i> Hapus
            </button>
        </form>
    </div>
</div>
@endsection
