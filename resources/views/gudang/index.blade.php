@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Daftar Gudang</h2>
        <a href="{{ route('gudang.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Warehouse
        </a>
    </div>

    <p class="text-muted mb-4">Kelola data gudang yang tersedia di sistem.</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="gudangTable" class="table table-striped table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama Gudang</th>
                    <th>Lokasi</th>
                    <th>Kapasitas</th>
                    <th style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($gudangs as $g)
                    <tr>
                        <td>{{ $g->Id_Gudang }}</td>
                        <td>{{ $g->Nama_Gudang }}</td>
                        <td>{{ $g->Lokasi }}</td>
                        <td>{{ $g->Kapasitas }}</td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('gudang.edit', $g->Id_Gudang) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('gudang.destroy', $g->Id_Gudang) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus gudang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada data gudang.</td>
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
        $('#gudangTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari gudang...",
                lengthMenu: "Tampilkan _MENU_ entri per halaman",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ gudang",
                infoEmpty: "Tidak ada data gudang",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
