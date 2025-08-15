@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Suppliers</h2>
        <a href="{{ route('procurement.create_supplier') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Supplier
        </a>
    </div>

    <p class="text-muted mb-4">Manage supplier information</p>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h6>Total Suppliers</h6>
                    <h4>{{ $suppliers->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h6>Active Suppliers</h6>
                    <h4>{{ $suppliers->where('Status', 'Active')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-secondary">
                <div class="card-body">
                    <h6>Inactive Suppliers</h6>
                    <h4>{{ $suppliers->where('Status', 'Inactive')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="procurementTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('procurement.index') }}">Purchase Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('procurement.supplier') }}">Suppliers</a>
        </li>
    </ul>

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
                    <th style="width: 160px;">Actions</th>
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
                        {{-- Tombol toggle status --}}
                        <form action="{{ route('procurement.toggle_status_supplier', $supplier->Id_Supplier) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm 
                                {{ $supplier->Status === 'Active' ? 'btn-success' : 'btn-secondary' }}">
                                {{ $supplier->Status }}
                            </button>
                        </form>
                    </td>
                    <td style="white-space: nowrap;">
                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                            <a href="{{ route('procurement.show_supplier', ['id' => $supplier->Id_Supplier]) }}"
                               class="btn btn-sm btn-info" title="Show">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('procurement.edit_supplier', ['id' => $supplier->Id_Supplier]) }}"
                               class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('procurement.destroy_supplier', ['id' => $supplier->Id_Supplier]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">No suppliers found.</td>
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
                search: "INPUT",
                searchPlaceholder: "Search suppliers...",
                lengthMenu: "Show MENU entries per page",
                zeroRecords: "No matching suppliers found",
                info: "Showing START to END of TOTAL suppliers",
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