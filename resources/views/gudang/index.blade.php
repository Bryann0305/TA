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
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gudangs as $g)
                    <tr>
                        <td></td>
                        <td>{{ $g->Nama_Gudang }}</td>
                        <td>{{ $g->alamat ?? '-' }}</td>
                        <td class="text-end">{{ number_format($g->Kapasitas, 0, ',', '.') }}</td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                {{-- View --}}
                                <a href="{{ route('gudang.show', $g->Id_Gudang) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('gudang.edit', $g->Id_Gudang) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('gudang.destroy', $g->Id_Gudang) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this warehouse?')">
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
                        <td colspan="5" class="text-center text-muted">No warehouse data available.</td>
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
                { targets: 0, orderable: false, searchable: false },
                { targets: 3, className: 'text-end' } // align capacity column right
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
