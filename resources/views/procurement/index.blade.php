@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Purchase Orders</h2>
        <a href="{{ route('procurement.create_purchaseOrder') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Purchase Order
        </a>
    </div>

    <p class="text-muted mb-4">Manage your purchase order records below.</p>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h6>Total Orders</h6>
                    <h4>{{ $orders->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h6>Pending Orders</h6>
                    <h4>{{ $orders->where('Status', 'Pending')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h6>Completed Orders</h6>
                    <h4>{{ $orders->where('Status', 'Completed')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="procurementTabs">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('procurement.index') }}">Purchase Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('procurement.supplier') }}">Suppliers</a>
        </li>
    </ul>

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
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><strong>PO-{{ $order->Id_Pembelian }}</strong></td>
                        <td>{{ $order->supplier->Nama_Supplier ?? 'Unknown' }}</td>
                        <td>
                            @if($order->barangs && $order->barangs->count())
                                <ul class="mb-0 ps-3">
                                    @foreach($order->barangs as $barang)
                                        <li>{{ $barang->Nama_Bahan }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $order->Tanggal_Pemesanan ? \Carbon\Carbon::parse($order->Tanggal_Pemesanan)->format('d M Y') : '-' }}</td>
                        <td>{{ $order->Tanggal_Kedatangan ? \Carbon\Carbon::parse($order->Tanggal_Kedatangan)->format('d M Y') : '-' }}</td>
                        <td>Rp {{ number_format($order->Total_Biaya, 0, ',', '.') }}</td>
                        <td>
    <form action="{{ route('procurement.toggle_status', $order->Id_Pembelian) }}" method="POST">
        @csrf
        @method('PATCH')
        <button type="submit" class="badge border-0 
            {{ $order->Status == 'Pending' ? 'bg-warning text-dark' : 'bg-success' }}"
            style="cursor: pointer;">
            {{ $order->Status }}
        </button>
    </form>
</td>



<td>
    <form action="{{ route('procurement.toggle_payment', $order->Id_Pembelian) }}" method="POST">
        @csrf
        @method('PATCH')
        <button type="submit" class="badge border-0 
            @if($order->Status_Pembayaran == 'Pending') bg-warning text-dark
            @elseif($order->Status_Pembayaran == 'Confirmed') bg-info text-white
            @else bg-secondary text-white
            @endif"
            style="cursor: pointer;">
            {{ $order->Status_Pembayaran }}
        </button>
    </form>
</td>

                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('procurement.show_po', $order->Id_Pembelian) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('procurement.edit_purchaseOrder', $order->Id_Pembelian) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('procurement.destroy_purchaseOrder', $order->Id_Pembelian) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus PO ini?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash-alt"></i></button>
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