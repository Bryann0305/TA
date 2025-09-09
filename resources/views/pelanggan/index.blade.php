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
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
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

                                {{-- Deactivate Button --}}
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

@push('scripts')
<script>
    $(document).ready(function () {
        $('#pelangganTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
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
