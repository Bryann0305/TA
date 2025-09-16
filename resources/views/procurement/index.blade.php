@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Purchase Orders</h2>
        <a href="{{ route('procurement.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Purchase Order
        </a>
    </div>

    <p class="text-muted mb-4">Manage all purchase orders registered in the system.</p>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table --}}
    <div class="table-responsive shadow-sm rounded">
        <table id="purchaseOrderTable" class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>PO ID</th>
                    <th>Supplier</th>
                    <th>Items</th>
                    <th>Order Date</th>
                    <th>Arrival Date</th>
                    <th>Total Cost</th>
                    <th>Payment Method</th>
                    <th>Payment Status</th>
                    <th>Receiving Status</th>
                    <th style="width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                <tr>
                    <td><strong>PO-{{ $order->Id_Pembelian }}</strong></td>
                    <td>{{ $order->supplier->Nama_Supplier ?? '-' }}</td>
                    <td>
                        @if($order->detailPembelian && $order->detailPembelian->count())
                            <ul class="mb-0 ps-3">
                                @foreach($order->detailPembelian as $detail)
                                    <li>{{ $detail->barang->Nama_Bahan ?? '-' }} ({{ $detail->Jumlah }})</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $order->Tanggal_Pemesanan ? $order->Tanggal_Pemesanan->format('d M Y') : '-' }}</td>
                    <td>{{ $order->Tanggal_Kedatangan ? $order->Tanggal_Kedatangan->format('d M Y') : '-' }}</td>
                    <td>Rp {{ number_format($order->Total_Biaya,0,',','.') }}</td>
                    <td>{{ $order->Metode_Pembayaran ?? '-' }}</td>

                    {{-- Payment Status --}}
                    <td>
                        <span class="badge 
                            @if($order->Status_Pembayaran === 'Pending') bg-warning text-dark
                            @elseif($order->Status_Pembayaran === 'Confirmed') bg-success
                            @else bg-secondary @endif">
                            {{ $order->Status_Pembayaran ?? '-' }}
                        </span>
                    </td>

                    {{-- Receiving Status --}}
<td>
    @php
        $statuses = $order->detailPembelian->pluck('Status_Penerimaan')->unique();
        if ($statuses->count() === 1) {
            $receivingStatus = $statuses->first() === 'Diterima' ? 'Completed' : $statuses->first();
        } elseif ($statuses->count() > 1) {
            $receivingStatus = 'Partial';
        } else {
            $receivingStatus = '-';
        }
    @endphp

    <span class="badge 
        @if($receivingStatus === 'Pending') bg-warning text-dark
        @elseif($receivingStatus === 'Completed') bg-success
        @elseif($receivingStatus === 'Partial') bg-primary
        @else bg-secondary @endif">
        {{ $receivingStatus }}
    </span>
</td>


                    {{-- Actions --}}
                    <td>
                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                            {{-- View --}}
                            <a href="{{ route('procurement.show', $order->Id_Pembelian) }}" 
                               class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('procurement.edit', $order->Id_Pembelian) }}" 
                               class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Approve Payment --}}
                            @if($order->Status_Pembayaran === 'Pending')
                                <form action="{{ route('procurement.toggle_payment', $order->Id_Pembelian) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" title="Approve Payment">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- Delete --}}
                            <form action="{{ route('procurement.destroy', $order->Id_Pembelian) }}" 
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
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
                    <td colspan="10" class="text-center text-muted">No purchase orders available.</td>
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
            order: [[0, 'desc']], 
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search purchase orders...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching purchase orders found",
                info: "Showing _START_ to _END_ of _TOTAL_ purchase orders",
                infoEmpty: "No purchase orders available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
