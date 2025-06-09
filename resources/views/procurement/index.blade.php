@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-2">Purchase Orders</h2>
    <p class="text-muted">Manage purchase order records</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="procurementTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('procurement.index') }}">Purchase Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('procurement.supplier') }}">Suppliers</a>
        </li>
    </ul>

    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Status</th>
                <th>Total Biaya</th>
                <th>Tanggal Pemesanan</th>
                <th>Tanggal Kedatangan</th>
                <th>Status Pembayaran</th>
                <th>User</th>
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr>
                <td>PO-{{ $order->Id_Pembelian }}</td>
                <td>{{ $order->Status }}</td>
                <td>Rp {{ number_format($order->Total_Biaya, 0, ',', '.') }}</td>
                <td>{{ $order->Tanggal_Pemesanan ? $order->Tanggal_Pemesanan->format('d M Y') : '-' }}</td>
                <td>{{ $order->Tanggal_Kedatangan ? $order->Tanggal_Kedatangan->format('d M Y') : '-' }}</td>
                <td>
                    @if ($order->Status_Pembayaran === 'Pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @elseif ($order->Status_Pembayaran === 'Confirmed')
                        <span class="badge bg-info">Confirmed</span>
                    @elseif ($order->Status_Pembayaran === 'Delivered')
                        <span class="badge bg-success">Delivered</span>
                    @else
                        <span class="badge bg-secondary">{{ $order->Status_Pembayaran }}</span>
                    @endif
                </td>
                <td>{{ $order->user->name ?? 'Unknown' }}</td>
                <td>{{ $order->supplier->Nama_Supplier ?? 'Unknown' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No purchase orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('procurement.create_purchaseOrder') }}" class="btn btn-success mt-3">Create New Purchase Order</a>
</div>
@endsection
