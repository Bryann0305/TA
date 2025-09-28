@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Warehouse Data</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Add Item
            </a>
            <a href="{{ route('gudang.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New Warehouse
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">Manage warehouses registered in the system.</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive shadow-sm rounded">
        <table id="gudangTable" class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Warehouse Name</th>
                    <th class="text-center">Location</th>
                    <th class="text-center">Capacity</th>
                    <th class="text-center">Total Stock</th>
                    <th class="text-center">Filled (%)</th>
                    <th class="text-center" style="width: 180px;">Actions</th>
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
                        <td class="text-center"></td>
                        <td class="text-center">{{ $g->Nama_Gudang }}</td>
                        <td class="text-center">{{ $g->alamat ?? '-' }}</td>
                        <td class="text-end">{{ number_format($g->Kapasitas, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format($totalBarang, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge 
                                {{ $persentase >= 100 ? 'bg-danger' : ($persentase >= 75 ? 'bg-warning' : 'bg-success') }}">
                                {{ number_format($persentase, 0, ',', '.') }}%
                            </span>
                        </td>
                        <td class="text-center" style="white-space: nowrap;">
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

@push('styles')
<style>
    /* Table styling improvements */
    #gudangTable {
        font-size: 14px;
        min-width: 100% !important;
        table-layout: fixed !important;
        width: 100% !important;
    }
    
    #gudangTable thead th {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: 600 !important;
        padding: 12px 8px !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
        overflow: hidden !important;
        line-height: 1.2 !important;
        height: auto !important;
        max-height: 50px !important;
    }
    
    #gudangTable tbody td {
        padding: 12px 8px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
    
    #gudangTable tbody tr:hover {
        background-color: #f8f9fa !important;
    }
    
    /* Badge styling */
    .badge {
        font-size: 0.75rem !important;
        padding: 0.5em 0.75em !important;
    }
    
    /* Action buttons */
    .btn-sm {
        padding: 0.375rem 0.5rem !important;
        font-size: 0.75rem !important;
    }
    
    /* Force single line headers */
    #gudangTable thead th {
        white-space: nowrap !important;
        word-wrap: normal !important;
        word-break: keep-all !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        line-height: 1.2 !important;
        height: 50px !important;
        max-height: 50px !important;
    }
    
    /* Column width constraints */
    #gudangTable th:nth-child(1), /* No */
    #gudangTable td:nth-child(1) {
        min-width: 40px !important;
        max-width: 50px !important;
        width: 40px !important;
    }
    
    #gudangTable th:nth-child(2), /* Warehouse Name */
    #gudangTable td:nth-child(2) {
        min-width: 140px !important;
        max-width: 160px !important;
        width: 140px !important;
    }
    
    #gudangTable th:nth-child(3), /* Location */
    #gudangTable td:nth-child(3) {
        min-width: 180px !important;
        max-width: 180px !important;
        width: 180px !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }
    
    #gudangTable th:nth-child(4), /* Capacity */
    #gudangTable td:nth-child(4) {
        min-width: 100px !important;
        max-width: 120px !important;
        width: 100px !important;
    }
    
    #gudangTable th:nth-child(5), /* Total Stock */
    #gudangTable td:nth-child(5) {
        min-width: 100px !important;
        max-width: 120px !important;
        width: 100px !important;
    }
    
    #gudangTable th:nth-child(6), /* Filled (%) */
    #gudangTable td:nth-child(6) {
        min-width: 100px !important;
        max-width: 120px !important;
        width: 100px !important;
    }
    
    #gudangTable th:nth-child(7), /* Actions */
    #gudangTable td:nth-child(7) {
        min-width: 120px !important;
        max-width: 180px !important;
        width: 120px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        var t = $('#gudangTable').DataTable({
            pageLength: 10,
            responsive: false,
            autoWidth: false,
            scrollX: true,
            columnDefs: [
                { targets: 0, orderable: false, searchable: false, width: "40px" }, // No
                { targets: 1, width: "140px" }, // Warehouse Name
                { targets: 2, width: "180px" }, // Location
                { targets: 3, width: "100px" }, // Capacity
                { targets: 4, width: "100px" }, // Total Stock
                { targets: 5, width: "100px" }, // Filled (%)
                { targets: 6, width: "120px" }  // Actions
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
        
        // Force column widths after DataTables initialization
        setTimeout(function() {
            $('#gudangTable th:nth-child(1)').css('width', '40px');
            $('#gudangTable td:nth-child(1)').css('width', '40px');
            $('#gudangTable th:nth-child(2)').css('width', '140px');
            $('#gudangTable td:nth-child(2)').css('width', '140px');
            $('#gudangTable th:nth-child(3)').css('width', '180px');
            $('#gudangTable td:nth-child(3)').css('width', '180px');
            $('#gudangTable th:nth-child(4)').css('width', '100px');
            $('#gudangTable td:nth-child(4)').css('width', '100px');
            $('#gudangTable th:nth-child(5)').css('width', '100px');
            $('#gudangTable td:nth-child(5)').css('width', '100px');
            $('#gudangTable th:nth-child(6)').css('width', '100px');
            $('#gudangTable td:nth-child(6)').css('width', '100px');
            $('#gudangTable th:nth-child(7)').css('width', '120px');
            $('#gudangTable td:nth-child(7)').css('width', '120px');
        }, 100);
    });
</script>
@endpush
