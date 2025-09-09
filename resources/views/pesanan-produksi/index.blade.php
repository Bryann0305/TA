@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Production Orders</h2>
        <a href="{{ route('pesanan_produksi.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Order
        </a>
    </div>

    <p class="text-muted mb-4">Manage production orders in the system.</p>

    {{-- Table --}}
    <div class="table-responsive">
        <table id="pesananProduksiTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Order Number</th>
                    <th>Customer</th>
                    <th>Product / Material</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th style="width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesanan as $item)
                    <tr>
                        <td>{{ $item->Id_Pesanan }}</td>
                        <td>{{ $item->Nomor_Pesanan ?? '-' }}</td>
                        <td>{{ $item->pelanggan->Nama_Pelanggan ?? '-' }}</td>
                        <td>
                            @if($item->detail && $item->detail->count())
                                <ul class="mb-0 ps-3">
                                    @foreach($item->detail as $d)
                                        <li>{{ $d->barang->Nama_Bahan ?? '-' }} ({{ $d->Jumlah }})</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->Tanggal_Pesanan ? \Carbon\Carbon::parse($item->Tanggal_Pesanan)->format('d M Y') : '-' }}</td>
                        <td>
                            @if($item->Status == 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($item->Status == 'confirmed')
                                <span class="badge bg-primary">Confirmed</span>
                            @elseif($item->Status == 'in_progress')
                                <span class="badge bg-info text-dark">In Progress</span>
                            @elseif($item->Status == 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($item->Status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                {{-- View --}}
                                <a href="{{ route('pesanan_produksi.show', $item->Id_Pesanan) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('pesanan_produksi.edit', $item->Id_Pesanan) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Approve / Confirm --}}
                                @if($item->Status == 'pending')
                                    <form action="{{ route('pesanan_produksi.toggle_status', $item->Id_Pesanan) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form action="{{ route('pesanan_produksi.destroy', $item->Id_Pesanan) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this order?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-dark" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No production orders available.</td>
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
                searchPlaceholder: "Search orders...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching orders found",
                info: "Showing _START_ to _END_ of _TOTAL_ orders",
                infoEmpty: "No orders available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
