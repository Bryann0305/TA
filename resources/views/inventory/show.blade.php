@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Detail Item</h2>
        <a href="{{ route('inventory.showGudang', $item->gudang_Id_Gudang) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- Card Detail --}}
    <div class="card mb-4 shadow-sm rounded-3">
        <div class="card-body">
            <table class="table table-borderless align-middle mb-0">
                <tr>
                    <th style="width: 200px;">Nama Item</th>
                    <td>{{ $item->Nama_Bahan }}</td>
                </tr>
                <tr>
                    <th>Jenis</th>
                    <td>{{ $item->Jenis }}</td>
                </tr>
                <tr>
                    <th>Kategori</th>
                    <td>{{ $item->kategori->Nama_Kategori ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Gudang</th>
                    <td>{{ $item->gudang->Nama_Gudang ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Satuan</th>
                    <td>{{ $item->Satuan ?? '-' }}</td>
                </tr>
               
                <tr>
                    <th>Stok (Unit)</th>
                    <td>{{ number_format($item->Stok ?? 0) }}</td>
                </tr>
                <tr>
                    <th>Permintaan Tahunan</th>
                    <td>{{ number_format($annualDemand ?? 0) }}</td>
                </tr>
                <tr>
                    <th>EOQ (Unit)</th>
                    <td>{{ number_format($item->EOQ ?? 0) }}</td>
                </tr>
                <tr>
                    <th>ROP (Unit)</th>
                    <td>{{ number_format($item->ROP ?? 0) }}</td>
                </tr>
                <tr>
                    <th>Safety Stock</th>
                    <td>{{ number_format($item->Safety_Stock ?? 0) }}</td>
                </tr>
                <tr>
                    <th>Harga Pokok Produksi (HPP)</th>
                    <td>
                        @if(!is_null($item->HPP) && $item->HPP > 0)
                            Rp {{ number_format($item->HPP, 0, ',', '.') }}
                        @else
                            <span class="text-muted fst-italic">Belum ditentukan</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @php
                            $stok = $item->Stok ?? 0;
                            $rop = $item->ROP ?? 0;
                            $safety = $item->Safety_Stock ?? 0;

                            // Hitung status stok berdasarkan ROP + Safety Stock
                            $statusText = match (true) {
                                $stok == 0 => 'Out of Stock',
                                $stok <= $safety => 'Critical Low',
                                $stok < $rop => 'Below Reorder Point',
                                default => 'In Stock',
                            };

                            $badge = match ($statusText) {
                                'Out of Stock', 'Critical Low' => 'bg-danger',
                                'Below Reorder Point' => 'bg-warning',
                                'In Stock' => 'bg-success',
                            };
                        @endphp
                        <span class="badge {{ $badge }} px-3 py-2">{{ $statusText }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="d-flex gap-2">
        <a href="{{ route('inventory.edit', $item->Id_Bahan) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit Item
        </a>
    </div>
</div>
@endsection
