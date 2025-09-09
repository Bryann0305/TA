@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Production Orders</h2>
        <a href="{{ route('production_order.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New SPP
        </a>
    </div>

    <p class="text-muted mb-4">Manage production orders in the system.</p>

    {{-- Table --}}
    <div class="table-responsive">
        <table id="productionOrderTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Production Name</th>
                    <th>Production Date</th>
                    <th>Status</th>
                    <th>Order Number</th>
                    <th>Schedule</th>
                    <th style="width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->Nama_Produksi }}</td>
                        <td>{{ $order->Tanggal_Produksi ? \Carbon\Carbon::parse($order->Tanggal_Produksi)->format('d M Y') : '-' }}</td>
                        <td>
                            @if($order->Status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($order->Status === 'confirmed')
                                <span class="badge bg-primary">Confirmed</span>
                            @elseif($order->Status === 'in_progress')
                                <span class="badge bg-info text-dark">In Progress</span>
                            @elseif($order->Status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($order->Status === 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @endif
                        </td>
                        <td>{{ $order->pesananProduksi->Nomor_Pesanan ?? '-' }}</td>
                        <td>
                            @if($order->penjadwalan)
                                {{ $order->penjadwalan->Tanggal_Mulai }} - {{ $order->penjadwalan->Tanggal_Selesai }}
                            @else
                                Not Scheduled
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                {{-- View --}}
                                <a href="{{ route('production_order.show', $order->id) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('production_order.edit', $order->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Approve --}}
                                @if($order->Status === 'pending')
                                    <form action="{{ route('production_order.update_status', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form action="{{ route('production_order.destroy', $order->id) }}" method="POST" 
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
        $('#productionOrderTable').DataTable({
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
