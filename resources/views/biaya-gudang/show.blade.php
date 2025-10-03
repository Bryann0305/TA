@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Warehouse Cost Details</h2>
        <a href="{{ route('biaya-gudang.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i> Back
        </a>
    </div>

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th style="width: 200px;">Warehouse</th>
                    <td>{{ $biayaGudang->gudang->Nama_Gudang }}</td>
                </tr>
                <tr>
                    <th>Cost Date</th>
                    <td>{{ $biayaGudang->tanggal_biaya->format('d F Y') }}</td>
                </tr>
                <tr>
                    <th>Rent Cost</th>
                    <td class="text-end fw-bold">Rp {{ number_format($biayaGudang->biaya_sewa, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Electricity Cost</th>
                    <td class="text-end fw-bold">Rp {{ number_format($biayaGudang->biaya_listrik, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Water Cost</th>
                    <td class="text-end fw-bold">Rp {{ number_format($biayaGudang->biaya_air, 0, ',', '.') }}</td>
                </tr>
                <tr class="table-primary">
                    <th>Total Cost</th>
                    <td class="text-end fw-bold fs-5">Rp {{ number_format($biayaGudang->total_biaya, 0, ',', '.') }}</td>
                </tr>
                @if($biayaGudang->keterangan)
                <tr>
                    <th>Notes</th>
                    <td>{{ $biayaGudang->keterangan }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="d-flex gap-2">
        <a href="{{ route('biaya-gudang.edit', $biayaGudang->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil-square me-2"></i> Edit
        </a>
        <form action="{{ route('biaya-gudang.destroy', $biayaGudang->id) }}" method="POST" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this cost record?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash me-2"></i> Delete
            </button>
        </form>
    </div>
</div>
@endsection
