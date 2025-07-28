@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bill of Materials</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">Back to Inventory</a>
            <a href="{{ route('bill-of-materials.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New BOM
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">Manage your list of Bill of Materials and its raw materials.</p>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="bomTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>BOM Name</th>
                    <th>Status</th>
                    <th>Total Materials</th>
                    <th>Material List</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($boms as $bom)
                    <tr>
                        <td><strong>{{ $bom->Nama_bill_of_material }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $bom->Status === 'Approved' ? 'success' : 'secondary' }}">
                                {{ $bom->Status }}
                            </span>
                        </td>
                        <td>{{ $bom->barangs->count() }}</td>
                        <td>
                            @if($bom->barangs->isNotEmpty())
                                <ul class="mb-0 ps-3">
                                    @foreach ($bom->barangs as $barang)
                                        <li>{{ $barang->Nama_Bahan }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">No materials linked</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                <a href="{{ route('bill-of-materials.show', $bom->Id_bill_of_material) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('bill-of-materials.edit', $bom->Id_bill_of_material) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('bill-of-materials.destroy', $bom->Id_bill_of_material) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this BOM?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No BOMs found.</td>
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
        $('#bomTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search BOMs...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching BOMs found",
                info: "Showing _START_ to _END_ of _TOTAL_ BOMs",
                infoEmpty: "No BOMs available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
