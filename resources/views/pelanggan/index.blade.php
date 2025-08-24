@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Data Pelanggan</h2>
        <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Customer
        </a>
    </div>

    <p class="text-muted mb-4">Kelola data pelanggan yang terdaftar di sistem.</p>

    {{-- Table --}}
    <div class="table-responsive">
        <table id="pelangganTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Nomor Telp</th>
                    <th>Alamat</th>
                    <th style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggan as $p)
                    <tr>
                        <td>{{ $p->Id_Pelanggan }}</td>
                        <td>{{ $p->Nama_Pelanggan }}</td>
                        <td>{{ $p->Nomor_Telp }}</td>
                        <td>{{ $p->Alamat }}</td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('pelanggan.edit', $p->Id_Pelanggan) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pelanggan.destroy', $p->Id_Pelanggan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?');" class="d-inline">
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
                        <td colspan="5" class="text-center text-muted">Tidak ada data pelanggan.</td>
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
        $('#pelangganTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari pelanggan...",
                lengthMenu: "Tampilkan _MENU_ entri per halaman",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pelanggan",
                infoEmpty: "Tidak ada data pelanggan",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
