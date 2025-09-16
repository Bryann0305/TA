@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Warehouse Data</h2>
        <a href="{{ route('gudang.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Warehouse
        </a>
    </div>

    <p class="text-muted mb-4">Manage warehouses registered in the system.</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="gudangTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Warehouse Name</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Total Stock</th>
                    <th>Filled (%)</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gudangs as $g)
                    @php
                        $totalBarang = $g->barangs->sum('Stok') ?? 0;
                        $persentase = $g->Kapasitas > 0 
                            ? round(($totalBarang / $g->Kapasitas) * 100) 
                            : 0;
                    @endphp
                    <tr>
                        <td></td>
                        <td>{{ $g->Nama_Gudang }}</td>
                        <td>{{ $g->alamat ?? '-' }}</td>
                        <td>{{ number_format($g->Kapasitas, 0, ',', '.') }}</td>
                        <td>{{ number_format($totalBarang, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge 
                                {{ $persentase >= 100 ? 'bg-danger' : ($persentase >= 75 ? 'bg-warning' : 'bg-success') }}">
                                {{ number_format($persentase, 0, ',', '.') }}%
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                {{-- View --}}
                                <a href="{{ route('inventory.showGudang', $g->Id_Gudang) }}" class="btn btn-sm btn-info" title="View Inventory">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No warehouse data available.</td>
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
        var t = $('#gudangTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { targets: 0, orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search warehouses...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching records found",
                info: "Showing _START_ to _END_ of _TOTAL_ warehouses",
                infoEmpty: "No warehouse data available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });

        // Re-number first column on each draw
        t.on('order.dt search.dt draw.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    });
</script>
@endpush
