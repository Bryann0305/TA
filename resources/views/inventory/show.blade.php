@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Detail Item</h2>
        <a href="{{ route('inventory.showGudang', $item->gudang_Id_Gudang) }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th>Item Name</th>
                    <td>{{ $item->Nama_Bahan }}</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td>{{ $item->Jenis }}</td>
                </tr>
                <tr>
                    <th>Category</th>
                    <td>{{ $item->kategori->Nama_Kategori ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Stock</th>
                    <td>{{ $item->Stok }}</td>
                </tr>
                <tr>
                    <th>EOQ</th>
                    <td>{{ $item->EOQ ?? 'Belum dihitung' }}</td>
                </tr>
                <tr>
                    <th>ROP</th>
                    <td>{{ $item->ROP ?? 'Belum dihitung' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @php
                            [$statusText, $badge] = match ($item->Status) {
                                'Critical Low' => ['Critical Low', 'danger'],
                                'Below Reorder Point' => ['Below Reorder Point', 'warning'],
                                default => ['In Stock', 'success'],
                            };
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ $statusText }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Action Button --}}
    <div class="d-flex gap-2">
        <a href="{{ route('inventory.edit', $item->Id_Bahan) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit
        </a>
    </div>
</div>
@endsection
