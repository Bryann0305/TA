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
                    <th class="text-center">PO ID</th>
                    <th class="text-center">Supplier</th>
                    <th class="text-center">Order Date</th>
                    <th class="text-center">Arrival Date</th>
                    <th class="text-center">Total Cost</th>
                    <th class="text-center">Payment Method</th>
                    <th class="text-center">Payment Status</th>
                    <th class="text-center">Receiving Status</th>
                    <th class="text-center" style="width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                <tr>
                    <td class="text-center">PO-{{ $order->Id_Pembelian }}</td>
                    <td class="text-center">{{ $order->supplier->Nama_Supplier ?? '-' }}</td>
                    <td class="text-center">{{ $order->Tanggal_Pemesanan ? $order->Tanggal_Pemesanan->format('d M Y') : '-' }}</td>
                    <td class="text-center">{{ $order->Tanggal_Kedatangan ? $order->Tanggal_Kedatangan->format('d M Y') : '-' }}</td>
                    <td class="text-end">Rp {{ number_format($order->Total_Biaya,0,',','.') }}</td>
                    <td class="text-center">{{ $order->Metode_Pembayaran ?? '-' }}</td>

                    {{-- Payment Status --}}
                    <td class="text-center">
                        <span class="badge 
                            @if($order->Status_Pembayaran === 'Pending') bg-warning text-dark
                            @elseif($order->Status_Pembayaran === 'Confirmed') bg-success
                            @else bg-secondary @endif">
                            {{ $order->Status_Pembayaran ?? '-' }}
                        </span>
                    </td>

                    {{-- Receiving Status --}}
                    <td class="text-center">
                        @php
                            $statuses = $order->detailPembelian->pluck('Status_Penerimaan')->unique();
                            $allPending = $statuses->count() === 1 && $statuses->first() === 'Pending';
                            $allReceived = $statuses->count() === 1 && $statuses->first() === 'Diterima';
                            $mixed = $statuses->count() > 1;
                        @endphp
                        
                        @if($allReceived)
                            {{-- Status Complete - tidak bisa diubah --}}
                            <div class="d-inline-block">
                                <span class="badge bg-success px-3 py-2" style="font-size: 14px;">
                                    ✅ Completed
                                </span>
                                <small class="text-muted d-block mt-1">Status cannot be changed</small>
                            </div>
                        @else
                            {{-- Status Pending atau Mixed - bisa diubah --}}
                            <form action="{{ route('procurement.updateReceivingStatus', $order->Id_Pembelian) }}" method="POST" class="receiving-status-form d-inline-block">
                                @csrf
                                @method('PATCH')
                                <select name="receiving_status" class="form-select form-select-sm receiving-status-select" onchange="this.form.submit()" style="border: 1px solid #28a745; border-radius: 8px; padding: 8px 16px; background-color: #f8f9fa; color: #212529; min-width: 140px; font-size: 14px;">
                                    <option value="Pending" {{ $allPending ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="Diterima" {{ $allReceived ? 'selected' : '' }}>✅ Completed</option>
                                </select>
                            </form>
                        @endif
                        
                        @if($mixed)
                            <small class="text-muted d-block mt-1">Mixed Status</small>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="text-center">
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

@push('styles')
<style>
    /* Table styling improvements */
    #purchaseOrderTable {
        font-size: 14px;
        min-width: 100% !important;
        table-layout: auto !important;
    }
    
    #purchaseOrderTable thead th {
        background-color: #f8f9fa !important;
        border-bottom: 2px solid #dee2e6 !important;
        font-weight: normal !important; /* hilangkan bold */
        padding: 12px 8px !important;
        vertical-align: middle !important;
        white-space: nowrap !important;
        text-overflow: ellipsis !important;
        overflow: hidden !important;
    }
    
    #purchaseOrderTable tbody td {
        font-weight: normal !important; /* hilangkan bold */
        padding: 12px 8px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
    
    #purchaseOrderTable tbody tr:hover {
        background-color: #f8f9fa !important;
    }

    /* Dropdown style */
    .receiving-status-select {
        font-weight: normal !important; /* hilangkan bold */
    }

    /* Badge styling */
    .badge {
        font-weight: normal !important; /* hilangkan bold */
        font-size: 0.75rem !important;
        padding: 0.5em 0.75em !important;
    }

    /* Action buttons */
    .btn-sm {
        font-weight: normal !important;
        padding: 0.375rem 0.5rem !important;
        font-size: 0.75rem !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        $('#purchaseOrderTable').DataTable({
            pageLength: 10,
            responsive: false,
            autoWidth: false,
            scrollX: true,
            order: [[0, 'desc']],
            columnDefs: [
                { targets: [7], orderable: false } // Disable sorting on Receiving Status column
            ],
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

        // Add loading state to receiving status dropdowns
        $(document).on('change', '.receiving-status-select', function() {
            const $select = $(this);
            $select.prop('disabled', true).addClass('loading');
            $select.after('<span class="spinner-border spinner-border-sm ms-2" role="status"><span class="visually-hidden">Loading...</span></span>');
        });
    });
</script>
@endpush
