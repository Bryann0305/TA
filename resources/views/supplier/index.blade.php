@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Suppliers</h2>
        <a href="{{ route('supplier.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Supplier
        </a>
    </div>

    <p class="text-muted mb-4">Manage supplier information in the system.</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="supplierTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Supplier Name</th>
                    <th>Employee Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Note</th>
                    <th style="width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->Id_Supplier }}</td>
                    <td>{{ $supplier->Nama_Supplier }}</td>
                    <td>{{ $supplier->Nama_Pegawai }}</td>
                    <td>{{ $supplier->Email ?? '-' }}</td>
                    <td>{{ $supplier->Kontak ?? '-' }}</td>
                    <td>{{ $supplier->Alamat ?? '-' }}</td>
                    <td>
                        @if($supplier->Status === 'Aktif')
                            <span class="badge bg-success">Active</span>
                        @elseif($supplier->Status === 'Non Aktif')
                            <span class="badge bg-danger">Inactive</span>
                        @elseif($supplier->Status === 'Pending')
                            <span class="badge bg-warning text-dark">Pending</span>
                        @else
                            <span class="badge bg-secondary">{{ $supplier->Status }}</span>
                        @endif
                    </td>
                    <td>{{ $supplier->keterangan ?? '-' }}</td>
                    <td style="white-space: nowrap;">
                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                            {{-- View --}}
                            <a href="{{ route('supplier.show', $supplier->Id_Supplier) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('supplier.edit', $supplier->Id_Supplier) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Approve / Activate --}}
                            @if($supplier->Status !== 'Aktif')
                                <form action="{{ route('supplier.approve', $supplier->Id_Supplier) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" title="{{ $supplier->Status === 'Pending' ? 'Approve' : 'Activate' }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif

                            {{-- Deactivate --}}
                            @if($supplier->Status === 'Aktif')
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal{{ $supplier->Id_Supplier }}">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @endif

                            {{-- Delete --}}
                            <form action="{{ route('supplier.destroy', $supplier->Id_Supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-dark" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- Modal Deactivate --}}
                <div class="modal fade" id="deactivateModal{{ $supplier->Id_Supplier }}" tabindex="-1" aria-labelledby="deactivateModalLabel{{ $supplier->Id_Supplier }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('supplier.deactivate', $supplier->Id_Supplier) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Deactivate Supplier</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="keterangan{{ $supplier->Id_Supplier }}" class="form-label">Reason</label>
                                        <textarea class="form-control" id="keterangan{{ $supplier->Id_Supplier }}" name="keterangan" rows="3" required>{{ old('keterangan', $supplier->keterangan) }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-danger">Deactivate</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">No suppliers available.</td>
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
        $('#supplierTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search suppliers...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching suppliers found",
                info: "Showing _START_ to _END_ of _TOTAL_ suppliers",
                infoEmpty: "No suppliers available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
