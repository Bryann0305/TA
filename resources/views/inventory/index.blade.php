@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Inventory Management</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Item
            </a>
            <a href="{{ route('inventory.exportPdf', request()->query()) }}" class="btn btn-outline-dark">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            <a href="{{ route('bill-of-materials.index') }}" class="btn btn-secondary">
                <i class="fas fa-boxes me-1"></i> Kelola Bill of Materials
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">Manage your inventory with EOQ calculations.</p>

    {{-- Table --}}
    <div class="table-responsive">
        <table id="inventoryTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Item Name</th>
                    <th>Jenis</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder Point</th>
                    <th>EOQ</th>
                    <th>Status</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $reorder = $item->Reorder_Point ?? 100;
                        $eoq = $item->EOQ ?? 300;

                        [$status, $badge] = match (true) {
                            $item->Stok <= $reorder / 2 => ['Critical Low', 'danger'],
                            $item->Stok < $reorder => ['Below Reorder Point', 'warning'],
                            default => ['In Stock', 'success'],
                        };
                    @endphp
                    <tr>
                        <td><strong>{{ $item->Nama_Bahan }}</strong></td>
                        <td>{{ $item->Jenis ?? 'Unknown' }}</td>
                        <td>{{ $item->kategori->Nama_Kategori ?? 'Unknown' }}</td>
                        <td>{{ $item->Stok }} unit</td>
                        <td>{{ $reorder }} unit</td>
                        <td>{{ $eoq }} unit</td>
                        <td><span class="badge bg-{{ $badge }}">{{ $status }}</span></td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('inventory.show', ['inventory' => $item->Id_Bahan]) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.edit', ['inventory' => $item->Id_Bahan]) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventory.destroy', ['inventory' => $item->Id_Bahan]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus item ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#inventoryTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search table...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching items found",
                info: "Showing _START_ to _END_ of _TOTAL_ items",
                infoEmpty: "No items available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush