@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Sales Order</h2>
        <a href="{{ route('pesanan_produksi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Pesanan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="pesananProduksiTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Pelanggan</th>
                    <th>Jumlah Pesanan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>SPP</th>
                    <th style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan as $item)
                    <tr>
                        <td>{{ $item->Id_Pesanan }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                        <td>{{ $item->pelanggan->Nama_Pelanggan ?? '-' }}</td>
                        <td>{{ $item->Jumlah_Pesanan }}</td>
                        <td>{{ $item->Tanggal_Pesanan ? \Carbon\Carbon::parse($item->Tanggal_Pesanan)->format('d M Y') : '-' }}</td>
                        <td>
                            @php
                                $badgeClass = match($item->Status) {
                                    'Menunggu' => 'warning',
                                    'Diproses' => 'primary',
                                    'Selesai' => 'success',
                                    default    => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $item->Status }}</span>
                        </td>
                        <td>
                            @if($item->Surat_Perintah_Produksi)
                                <a href="{{ asset('storage/'.$item->Surat_Perintah_Produksi) }}" target="_blank" class="btn btn-sm btn-secondary">
                                    Lihat
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('pesanan_produksi.show', $item->Id_Pesanan) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pesanan_produksi.edit', $item->Id_Pesanan) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pesanan_produksi.destroy', $item->Id_Pesanan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');" class="d-inline">
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
                        <td colspan="8" class="text-center text-muted">Tidak ada pesanan produksi.</td>
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
        $('#pesananProduksiTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Cari pesanan...",
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada pesanan yang cocok",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pesanan",
                infoEmpty: "Tidak ada data pesanan",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
