@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-2">Purchase Orders</h2>
    <p class="text-muted">Manage purchase order records</p>
    

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Total Orders</h6>
                    <h4>{{ $orders->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Pending Orders</h6>
                    <h4>{{ $orders->where('Status', 'Pending')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Completed Orders</h6>
                    <h4>{{ $orders->where('Status', 'Completed')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="procurementTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('procurement.index') }}">Purchase Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('procurement.supplier') }}">Suppliers</a>
        </li>
    </ul>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID PO</th>
                <th>Status Pesanan</th>
                <th>Total Biaya</th>
                <th>Tgl Pemesanan</th>
                <th>Tgl Kedatangan</th>
                <th>Status Pembayaran</th>
                <th>User</th>
                <th>Supplier</th>
                <th style="width: 140px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
            <tr>
                <td><strong>PO-{{ $order->Id_Pembelian }}</strong></td>
                <td>
                    @php
                        $status = strtolower($order->Status);
                        $badgeStatus = match($status) {
                            'pending' => 'warning',
                            'approved' => 'primary',
                            'completed' => 'success',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeStatus }}">{{ $order->Status }}</span>
                </td>
                <td>Rp {{ number_format($order->Total_Biaya, 0, ',', '.') }}</td>
                <td>{{ $order->Tanggal_Pemesanan ? \Carbon\Carbon::parse($order->Tanggal_Pemesanan)->format('d M Y') : '-' }}</td>
                <td>{{ $order->Tanggal_Kedatangan ? \Carbon\Carbon::parse($order->Tanggal_Kedatangan)->format('d M Y') : '-' }}</td>
                <td>
                    @php
                        $pay = strtolower($order->Status_Pembayaran);
                        $badgePayment = match($pay) {
                            'pending' => 'warning text-dark',
                            'confirmed' => 'info',
                            'delivered' => 'success',
                            default => 'secondary'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgePayment }}">{{ $order->Status_Pembayaran }}</span>
                </td>
                <td>{{ $order->user->name ?? 'Unknown' }}</td>
                <td>{{ $order->supplier->Nama_Supplier ?? 'Unknown' }}</td>
                <td>
                    <a href="{{ route('procurement.show_po', ['id' => $order->Id_Pembelian]) }}" 
                       class="btn btn-info btn-sm" title="Show">
                        <i class="fas fa-eye"></i>
                    </a>

                    <a href="{{ route('procurement.edit_purchaseOrder', ['id' => $order->Id_Pembelian]) }}" 
                       class="btn btn-warning btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    <form action="{{ route('procurement.destroy_purchaseOrder', ['id' => $order->Id_Pembelian]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-muted">No purchase orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('procurement.create_purchaseOrder') }}" class="btn btn-primary mt-3">New Purchase Order</a>
</div>
@endsection
