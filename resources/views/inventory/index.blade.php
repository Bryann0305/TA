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
            <a href="{{ route('bom.index') }}" class="btn btn-secondary">
                <i class="fas fa-boxes me-1"></i> Manage Bill of Materials
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">Manage your inventory with EOQ calculations.</p>

    {{-- Raw Materials --}}
    <h4>Raw Materials</h4>
    <div class="table-responsive mb-5">
        <table id="rawMaterialTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder Point</th>
                    <th>EOQ</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items->where('Jenis', 'Bahan_Baku') as $item)
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
                        <td>{{ $item->Nama_Bahan }}</td>
                        <td>{{ $item->kategori->Nama_Kategori ?? '-' }}</td>
                        <td>{{ number_format($item->Stok, 2) }}</td>
                        <td>{{ number_format($reorder, 2) }}</td>
                        <td>{{ number_format($eoq, 2) }}</td>
                        <td><span class="badge bg-{{ $badge }}">{{ $status }}</span></td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('inventory.show', $item->Id_Bahan) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.edit', $item->Id_Bahan) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventory.destroy', $item->Id_Bahan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-dark" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Finished Goods --}}
    <h4>Finished Goods</h4>
    <div class="table-responsive">
        <table id="finishedGoodsTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Reorder Point</th>
                    <th>EOQ</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items->where('Jenis', 'Produk') as $item)
                    @php
                        $reorder = $item->Reorder_Point ?? 50;
                        $eoq = $item->EOQ ?? 200;
                        [$status, $badge] = match (true) {
                            $item->Stok <= $reorder / 2 => ['Critical Low', 'danger'],
                            $item->Stok < $reorder => ['Below Reorder Point', 'warning'],
                            default => ['In Stock', 'success'],
                        };
                    @endphp
                    <tr>
                        <td>{{ $item->Nama_Bahan }}</td>
                        <td>{{ $item->kategori->Nama_Kategori ?? '-' }}</td>
                        <td>{{ number_format($item->Stok, 2) }}</td>
                        <td>{{ number_format($reorder, 2) }}</td>
                        <td>{{ number_format($eoq, 2) }}</td>
                        <td><span class="badge bg-{{ $badge }}">{{ $status }}</span></td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('inventory.show', $item->Id_Bahan) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.edit', $item->Id_Bahan) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('inventory.destroy', $item->Id_Bahan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-dark" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#rawMaterialTable, #finishedGoodsTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search items...",
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
