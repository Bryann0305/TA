@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bill of Materials (BOM)</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                Back to Inventory
            </a>
            <a href="{{ route('bom.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> New BOM
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">Manage your Bill of Material records below.</p>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="bomTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID BOM</th>
                    <th>BOM Name</th>
                    <th>Related Items</th>
                    <th>Status</th>
                    <th style="width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($boms as $bom)
                    <tr>
                        <td><strong>BOM-{{ $bom->Id_bill_of_material }}</strong></td>
                        <td>{{ $bom->Nama_bill_of_material }}</td>
                        <td>
                            @if($bom->barangs && $bom->barangs->count())
                                <ul class="mb-0 ps-3">
                                    @foreach($bom->barangs as $barang)
                                        <li>{{ $barang->Nama_Bahan ?? '-' }} ({{ $barang->pivot->Jumlah_Bahan }})</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusText = match($bom->Status) {
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    default => 'Other',
                                };
                                $badgeClass = match($bom->Status) {
                                    'pending' => 'warning text-dark',
                                    'confirmed' => 'success text-white',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                        </td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                {{-- View --}}
                                <a href="{{ route('bom.show', $bom->Id_bill_of_material) }}" class="btn btn-sm btn-info" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('bom.edit', $bom->Id_bill_of_material) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- Approve --}}
                                @if($bom->Status === 'pending')
                                    <form action="{{ route('bom.approve', $bom->Id_bill_of_material) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- Delete --}}
                                <form action="{{ route('bom.destroy', $bom->Id_bill_of_material) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this BOM?');" class="d-inline">
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
                        <td colspan="5" class="text-center text-muted">No BOM records available.</td>
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
                searchPlaceholder: "Search BOM...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching BOM found",
                info: "Showing _START_ to _END_ of _TOTAL_ BOM",
                infoEmpty: "No BOM available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });
    });
</script>
@endpush
