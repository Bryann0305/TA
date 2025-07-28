@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pesanan Produksi</h2>
        <a href="{{ route('pesanan-produksi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Pesanan Baru
        </a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>User</th>
                    <th>Jumlah Pesanan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan as $item)
                <tr>
                    <td>{{ $item->Id_Pesanan }}</td>
                    <td>{{ $item->pelanggan->Nama_Pelanggan ?? '-' }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->Jumlah_Pesanan }}</td>
                    <td>{{ $item->Tanggal_Pesanan ? \Carbon\Carbon::parse($item->Tanggal_Pesanan)->format('d M Y') : '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $item->Status == 'Menunggu' ? 'warning' : ($item->Status == 'Diproses' ? 'primary' : 'success') }}">
                            {{ $item->Status }}
                        </span>
                    </td>
                    <td style="white-space: nowrap;">
                        <a href="{{ route('pesanan-produksi.show', $item->Id_Pesanan) }}" class="btn btn-sm btn-info" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('pesanan-produksi.edit', $item->Id_Pesanan) }}" class="btn btn-sm btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('pesanan-produksi.destroy', $item->Id_Pesanan) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pesanan ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Tidak ada pesanan produksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 