@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Inventory Management</h2>
        <div>
            <a href="{{ route('inventory.create') }}" class="btn btn-primary me-2">New Item</a>
            <a href="{{ route('bill-of-materials.index') }}" class="btn btn-secondary">Kelola Bill of Materials</a>
        </div>
    </div>
    <p>Manage your inventory with EOQ calculations</p>

    <form method="GET" action="{{ route('inventory.index') }}">
        <div class="card p-4 shadow-sm mb-4 border-0 w-100">
            <div class="row gy-2 gx-3">

                {{-- Search by name --}}
                <div class="col-12 col-md-4">
                    <input type="text" name="search" class="form-control w-100" placeholder="Search by name"
                        value="{{ request('search') }}">
                </div>

                {{-- Category filter --}}
                <div class="col-12 col-md-3">
                    <select name="category" class="form-select w-100">
                        <option value="">All Categories</option>
                        @foreach(App\Models\Kategori::all() as $kategori)
                        <option value="{{ $kategori->Nama_Kategori }}" {{ request('category') ==
                            $kategori->Nama_Kategori ? 'selected' : '' }}>
                            {{ $kategori->Nama_Kategori }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis filter --}}
                <div class="col-12 col-md-2">
                    <select name="jenis" class="form-select w-100">
                        <option value="">All Types</option>
                        <option value="Bahan_Baku" {{ request('jenis') == 'Bahan_Baku' ? 'selected' : '' }}>Bahan Baku
                        </option>
                        <option value="Produk" {{ request('jenis') == 'Produk' ? 'selected' : '' }}>Produk</option>
                    </select>
                </div>

                {{-- Tombol aksi --}}
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">🔎</button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-danger w-100">⟲ Reset</a>
                    <a href="{{ route('inventory.exportPdf', request()->query()) }}"
                        class="btn btn-outline-dark w-100">⬇ PDF</a>
                </div>

            </div>
        </div>
    </form>



    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Jenis</th>
                    <th>Stock</th>
                    <th>Reorder Point</th>
                    <th>EOQ</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                @php
                $reorder = $item->Reorder_Point ?? 100;
                $eoq = $item->EOQ ?? 300;

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
                    <td>{{ $item->kategori->Nama_Kategori ?? 'Unknown' }}</td>
                    <td>{{ $item->Jenis ?? 'Unknown' }}</td>
                    <td>{{ $item->Stok }} unit</td>
                    <td>{{ $reorder }} unit</td>
                    <td>{{ $eoq }} unit</td>
                    <td><span class="badge bg-{{ $badge }}">{{ $status }}</span></td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('inventory.show', ['id' => $item->Id_Bahan]) }}"
                            class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('inventory.edit', ['id' => $item->Id_Bahan]) }}"
                            class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('inventory.destroy', ['id' => $item->Id_Bahan]) }}" method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus item ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">No items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection