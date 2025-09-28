@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Customer Data</h2>
        <a href="{{ route('pelanggan.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Customer
        </a>
    </div>

    <p class="text-muted mb-4">Manage customers registered in the system.</p>

    {{-- Table --}}
    <div class="table-responsive">
        <table id="pelangganTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 60px;">ID</th>
                    <th class="text-center">Customer Name</th>
                    <th class="text-center">Phone Number</th>
                    <th class="text-center">Address</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelanggans as $p)
                    <tr>
                        <td>{{ $p->Id_Pelanggan }}</td>
                        <td>{{ $p->Nama_Pelanggan }}</td>
                        <td>{{ $p->Nomor_Telp }}</td>
                        <td>{{ $p->Alamat }}</td>
                        <td>
                            @if($p->status === 'active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                {{-- Show Button --}}
                                <a href="{{ route('pelanggan.show', $p->Id_Pelanggan) }}" 
                                   class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Edit Button --}}
                                <a href="{{ route('pelanggan.edit', $p->Id_Pelanggan) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Deactivate / Activate Button --}}
                                @if($p->status === 'active')
                                <form action="{{ route('pelanggan.deactivate', $p->Id_Pelanggan) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to deactivate this customer?');" 
                                      class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-secondary" title="Deactivate">
                                        <i class="fas fa-user-slash"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('pelanggan.toggle-status', $p->Id_Pelanggan) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Activate this customer?');" 
                                      class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-success" title="Activate">
                                        <i class="fas fa-user-check"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No customer data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
<style>
    #pelangganTable th {
        white-space: nowrap !important;
        text-align: center;
        vertical-align: middle;
        padding: 8px 4px !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
    }
    
    #pelangganTable th.text-center {
        text-align: center !important;
    }
    
    /* Ensure table headers don't wrap */
    .table thead th {
        white-space: nowrap !important;
        overflow: hidden;
        text-overflow: ellipsis;
        word-break: keep-all !important;
        hyphens: none !important;
    }
    
    /* Specific styling for customer table headers */
    #pelangganTable thead th {
        white-space: nowrap !important;
        word-wrap: normal !important;
        word-break: keep-all !important;
        max-width: none !important;
    }
    
    /* Force single line for specific columns */
    #pelangganTable th:nth-child(2), /* Customer Name */
    #pelangganTable th:nth-child(3) { /* Phone Number */
        white-space: nowrap !important;
        word-break: keep-all !important;
        overflow: visible !important;
        text-overflow: unset !important;
    }
    
    /* Actions column styling */
    #pelangganTable td:last-child {
        white-space: nowrap !important;
        padding: 4px !important;
    }
    
    #pelangganTable td:last-child .btn {
        padding: 4px 6px !important;
        font-size: 12px !important;
        margin: 0 1px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function () {
        $('#pelangganTable').DataTable({
            pageLength: 10,
            responsive: false, // Disable responsive to prevent column wrapping
            autoWidth: false,
            scrollX: true, // Enable horizontal scroll if needed
            columnDefs: [
                { targets: '_all', className: 'text-center' },
                { targets: [1, 2], className: 'text-center', width: '150px' }, // Customer Name & Phone Number
                { targets: [0], width: '60px' }, // ID column
                { targets: [5], width: '120px' } // Actions column
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search customers...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching records found",
                info: "Showing _START_ to _END_ of _TOTAL_ customers",
                infoEmpty: "No customer data available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
