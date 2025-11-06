@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Inventory at {{ $gudang->Nama_Gudang }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Add Item
            </a>
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('bom.index') }}" class="btn btn-secondary">
                <i class="fas fa-boxes me-1"></i> Manage BOM
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">Manage your inventory with EOQ and Reorder Point calculations for raw materials.</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Raw Materials --}}
    <h4 class="mb-3">Raw Materials (Bahan Baku)</h4>
    <div class="table-responsive mb-5">
        <table id="rawMaterialsTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Satuan</th>
                    <th>Reorder Point</th>
                    <th>EOQ</th>
                    <th>Status</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items->where('Jenis', 'Bahan_Baku') as $index => $item)
                    @php
                        $reorder = $item->ROP ?? 0;
                        $eoq = $item->EOQ ?? 0;
                        $badge = match ($item->Status) {
                            'Critical Low' => 'bg-danger',
                            'Below Reorder Point' => 'bg-warning',
                            'Out of Stock' => 'bg-danger',
                            default => 'bg-success',
                        };
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->Nama_Bahan }}</td>
                        <td>{{ $item->kategori->Nama_Kategori ?? '-' }}</td>
                        <td class="text-end" data-bs-toggle="tooltip" title="{{ number_format($item->Stok, 0, ',', '.') }}">
                            {{ number_format($item->Stok, 0, ',', '.') }}
                        </td>
                        <td class="text-center">{{ $item->Satuan ?? '-' }}</td>
                        <td class="text-end" data-bs-toggle="tooltip" title="{{ number_format($reorder, 0, ',', '.') }}">
                            {{ number_format($reorder, 0, ',', '.') }}
                        </td>
                        <td class="text-end" data-bs-toggle="tooltip" title="{{ number_format($eoq, 0, ',', '.') }}">
                            {{ number_format($eoq, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $badge }}">
                                {{ $item->Status ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('inventory.show', $item->Id_Bahan) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('inventory.edit', $item->Id_Bahan) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('inventory.destroy', $item->Id_Bahan) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
    <h4 class="mb-3">Finished Goods (Produk)</h4>
    <div class="table-responsive">
        <table id="finishedGoodsTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items->where('Jenis', 'Produk') as $index => $item)
                    @php
                        $badge = match ($item->Status) {
                            'Critical Low' => 'bg-danger',
                            'Out of Stock' => 'bg-danger',
                            default => 'bg-success',
                        };
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->Nama_Bahan }}</td>
                        <td>{{ $item->kategori->Nama_Kategori ?? '-' }}</td>
                        <td class="text-end" data-bs-toggle="tooltip" title="{{ number_format($item->Stok, 0, ',', '.') }}">
                            {{ number_format($item->Stok, 0, ',', '.') }}
                        </td>
                        <td class="text-center">{{ $item->Satuan ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $badge }}">
                                {{ $item->Status ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('inventory.show', $item->Id_Bahan) }}" class="btn btn-sm btn-info" title="View"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('inventory.edit', $item->Id_Bahan) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('inventory.destroy', $item->Id_Bahan) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
        $('#rawMaterialsTable, #finishedGoodsTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { targets: [0], className: 'text-center' },
                { targets: [3,5,6], className: 'text-end' },
                { targets: [4], className: 'text-center' }
            ],
            order: [[1, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search items...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching items found",
                info: "Showing _START_ to _END_ of _TOTAL_ items",
                infoEmpty: "No items available",
                paginate: { previous: "‹", next: "›" }
            }
        });

        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
