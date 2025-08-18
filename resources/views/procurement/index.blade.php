@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Purchase Orders</h2>
        <a href="{{ route('procurement.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Purchase Order
        </a>
    </div>

    <p class="text-muted mb-4">Manage your purchase order records below.</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="purchaseOrderTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID PO</th>
                    <th>Supplier</th>
                    <th>Nama Barang</th>
                    <th>Tgl Pemesanan</th>
                    <th>Tgl Kedatangan</th>
                    <th>Total Biaya</th>
                    <th>Metode Pembayaran</th>
                    <th>Status Pembayaran</th>
                    <th style="width: 260px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><strong>PO-{{ $order->Id_Pembelian }}</strong></td>
                        <td>{{ $order->supplier->Nama_Supplier ?? 'Unknown' }}</td>
                        <td>
                            @if($order->detailPembelian && $order->detailPembelian->count())
                                <ul class="mb-0 ps-3">
                                    @foreach($order->detailPembelian as $detail)
                                        <li>{{ $detail->barang->Nama_Bahan ?? '-' }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $order->Tanggal_Pemesanan ? $order->Tanggal_Pemesanan->format('d M Y') : '-' }}</td>
                        <td>{{ $order->Tanggal_Kedatangan ? $order->Tanggal_Kedatangan->format('d M Y') : '-' }}</td>
                        <td>Rp {{ number_format($order->Total_Biaya, 0, ',', '.') }}</td>
                        <td>{{ $order->Metode_Pembayaran ?? '-' }}</td>
                        <td>
                            <span class="badge 
                                @if($order->Status_Pembayaran === 'Pending') bg-warning text-dark
                                @elseif($order->Status_Pembayaran === 'Confirmed') bg-success text-white
                                @else bg-secondary @endif">
                                {{ $order->Status_Pembayaran }}
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                {{-- Show --}}
                                <a href="{{ route('procurement.show', $order->Id_Pembelian) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('procurement.edit', $order->Id_Pembelian) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Approve Payment --}}
                                @if($order->Status_Pembayaran === 'Pending')
                                    <form action="{{ route('procurement.toggle_payment', $order->Id_Pembelian) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form action="{{ route('procurement.destroy', $order->Id_Pembelian) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this PO?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No purchase orders found.</td>
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
        $('#purchaseOrderTable').DataTable({
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
