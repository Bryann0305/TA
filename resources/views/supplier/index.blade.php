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

    <p class="text-muted mb-4">Manage supplier information</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="supplierTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID Supplier</th>
                    <th>Supplier Name</th>
                    <th>Employee Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Note</th>
                    <th style="width: 260px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->Id_Supplier }}</td>
                    <td>{{ $supplier->Nama_Supplier }}</td>
                    <td>{{ $supplier->Nama_Pegawai }}</td>
                    <td>{{ $supplier->Email }}</td>
                    <td>{{ $supplier->Kontak }}</td>
                    <td>{{ $supplier->Alamat }}</td>
                    <td>
                        <span class="badge 
                            @if($supplier->Status === 'Aktif') bg-success
                            @elseif($supplier->Status === 'Non Aktif') bg-danger
                            @elseif($supplier->Status === 'Pending') bg-warning text-dark
                            @else bg-secondary @endif">
                            {{ $supplier->Status }}
                        </span>
                    </td>
                    <td>
                        @if($supplier->Status === 'Non Aktif')
                            <small class="text-muted">{{ $supplier->Keterangan ?? '-' }}</small>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td style="white-space: nowrap;">
                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                            {{-- Show --}}
                            <a href="{{ route('supplier.show', $supplier->Id_Supplier) }}"
                               class="btn btn-sm btn-info" title="Show">
                                <i class="fas fa-eye"></i>
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('supplier.edit', $supplier->Id_Supplier) }}"
                               class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Approve / Aktifkan --}}
                            @if($supplier->Status !== 'Aktif')
                                <form action="{{ route('supplier.approve', $supplier->Id_Supplier) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">
                                        {{ $supplier->Status === 'Pending' ? 'Approve' : 'Aktifkan' }}
                                    </button>
                                </form>
                            @endif

                            {{-- Nonaktifkan --}}
                            @if($supplier->Status === 'Aktif')
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deactivateModal{{ $supplier->Id_Supplier }}">
                                    Nonactive
                                </button>
                            @endif

                            {{-- Delete --}}
                            <form action="{{ route('supplier.destroy', $supplier->Id_Supplier) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-dark" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                {{-- Modal Nonaktifkan --}}
                <div class="modal fade" id="deactivateModal{{ $supplier->Id_Supplier }}" tabindex="-1" aria-labelledby="deactivateModalLabel{{ $supplier->Id_Supplier }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <form action="{{ route('supplier.deactivate', $supplier->Id_Supplier) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Nonaktifkan Supplier</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="keterangan{{ $supplier->Id_Supplier }}" class="form-label">Alasan Nonaktif</label>
                                        <textarea class="form-control" id="keterangan{{ $supplier->Id_Supplier }}" name="keterangan" rows="3" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">Nonaktifkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted">No suppliers found.</td>
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
                search: "Search:",
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
