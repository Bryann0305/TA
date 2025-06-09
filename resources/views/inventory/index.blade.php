@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Inventory Management</h2>
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">➕ Add New Item</a>
        </div>
    <p>Manage your inventory with EOQ calculations</p>

    <div class="card p-3 mb-3">
        <div class="d-flex gap-2 align-items-center">
            <input type="text" class="form-control" placeholder="Search by name or SKU">
            <select class="form-select">
                <option value="">All Categories</option>
                <!-- Kalau mau kategori dynamic -->
                @foreach(App\Models\Kategori::all() as $kategori)
                    <option value="{{ $kategori->Nama_Kategori }}">{{ $kategori->Nama_Kategori }}</option>
                @endforeach
            </select>
            <a href="{{ route('inventory.exportPdf') }}" class="btn btn-outline-secondary">⬇ Export PDF</a>
            </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>SKU</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder Point</th>
                    <th>EOQ</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @php
                        $reorder = 100; // default reorder point (bisa disimpan di DB nanti)
                        $eoq = 300; // default EOQ (bisa dihitung atau simpan di DB)

                        if ($item->Stok <= $reorder / 2) {
                            $status = 'Critical Low';
                            $badge = 'danger';
                        } elseif ($item->Stok < $reorder) {
                            $status = 'Below Reorder Point';
                            $badge = 'warning';
                        } else {
                            $status = 'In Stock';
                            $badge = 'success';
                        }
                    @endphp
                    <tr>
                        <td><strong>{{ $item->Nama_Bahan }}</strong></td>
                        <td>{{ $item->Id_Bahan }}</td>
                        <td>{{ $item->kategori->Nama_Kategori ?? 'Unknown' }}</td>
                        <td>{{ $item->Stok }} unit</td>
                        <td>{{ $reorder }} unit</td>
                        <td>{{ $eoq }} unit</td>
                        <td><span class="badge bg-{{ $badge }}">{{ $status }}</span></td>
                        <td><a href="#">View EOQ Details</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
