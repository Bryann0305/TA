@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Biaya Gudang</h2>
        <a href="{{ route('biaya-gudang.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Biaya
        </a>
    </div>

    <p class="text-muted mb-4">Kelola biaya sewa, listrik, dan air untuk setiap gudang.</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="biayaGudangTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 60px;">No</th>
                    <th class="text-center">Gudang</th>
                    <th class="text-center">Tanggal Biaya</th>
                    <th class="text-center">Biaya Sewa</th>
                    <th class="text-center">Biaya Listrik</th>
                    <th class="text-center">Biaya Air</th>
                    <th class="text-center">Total Biaya</th>
                    <th class="text-center" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($biayaGudang as $biaya)
                <tr>
                    <td></td>
                    <td>{{ $biaya->gudang->Nama_Gudang }}</td>
                    <td>{{ $biaya->tanggal_biaya->format('d/m/Y') }}</td>
                    <td class="text-end">Rp {{ number_format($biaya->biaya_sewa, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($biaya->biaya_listrik, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($biaya->biaya_air, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($biaya->total_biaya, 0, ',', '.') }}</td>
                    <td style="white-space: nowrap;">
                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                            {{-- View --}}
                            <a href="{{ route('biaya-gudang.show', $biaya->id) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('biaya-gudang.edit', $biaya->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Delete --}}
                            <form action="{{ route('biaya-gudang.destroy', $biaya->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this cost record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-dark" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No cost data available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    #biayaGudangTable th {
        white-space: nowrap !important;
        text-align: center;
        vertical-align: middle;
        padding: 8px 4px !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
    }
    
    #biayaGudangTable th.text-center {
        text-align: center !important;
    }
    
    /* Ensure table headers don't wrap */
    .table thead th {
        white-space: nowrap !important;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: keep-all !important;
        hyphens: none !important;
    }
    
    /* Actions column styling */
    #biayaGudangTable td:last-child {
        white-space: nowrap !important;
        padding: 4px !important;
    }
    
    #biayaGudangTable td:last-child .btn {
        padding: 4px 6px !important;
        font-size: 12px !important;
        margin: 0 1px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        var t = $('#biayaGudangTable').DataTable({
            pageLength: 10,
            responsive: false,
            autoWidth: false,
            scrollX: true,
            columnDefs: [
                { targets: 0, orderable: false, searchable: false },
                { targets: '_all', className: 'text-center' },
                { targets: [0], width: '60px' },
                { targets: [7], width: '120px' }
            ],
            order: [[1, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search costs...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching records found",
                info: "Showing _START_ to _END_ of _TOTAL_ costs",
                infoEmpty: "No cost data available",
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
