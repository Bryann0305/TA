@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-4">📦 Inventory Item Details</h2>

    <div class="card p-4">
        <h4>{{ $item->Nama_Bahan }} <small class="text-muted">({{ $item->Id_Bahan }})</small></h4>
        <p>Category: <strong>{{ $item->kategori->Nama_Kategori ?? 'Unknown' }}</strong></p>
        <p>Stock: <strong>{{ $item->Stok }} unit</strong></p>
        <p>Reorder Point: <strong>{{ $item->Reorder_Point }} unit</strong></p>
        <p>EOQ (Economic Order Quantity): <strong>{{ $item->EOQ }} unit</strong></p>
        
        @php
            if ($item->Stok <= $item->Reorder_Point / 2) {
                $status = '⚠️ Critical Low Stock';
                $color = 'danger';
            } elseif ($item->Stok < $item->Reorder_Point) {
                $status = '🔸 Below Reorder Point';
                $color = 'warning';
            } else {
                $status = '✅ In Stock';
                $color = 'success';
            }
        @endphp
        <p>Status: <span class="badge bg-{{ $color }}">{{ $status }}</span></p>

        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-warning">Edit</a>
        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>
@endsection
