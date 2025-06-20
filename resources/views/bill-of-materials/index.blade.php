@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Bill of Materials</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">← Back to Inventory</a>
            <a href="{{ route('bill-of-materials.create') }}" class="btn btn-primary">New BOM</a>
        </div>
    </div>

    <p>Manage your list of Bill of Materials and its raw materials.</p>

    <div class="card p-4 shadow-sm mb-4 border-0 w-100">
        <form method="GET" action="{{ route('bill-of-materials.index') }}">
            <div class="row gy-2 gx-3">

                {{-- Search by name --}}
                <div class="col-12 col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Search BOM name" value="{{ request('search') }}">
                </div>

                {{-- Status filter --}}
                <div class="col-12 col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">🔎 Search</button>
                    <a href="{{ route('bill-of-materials.index') }}" class="btn btn-outline-danger w-100">⟲ Reset</a>
                </div>

            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>BOM Name</th>
                    <th>Status</th>
                    <th>Total Materials</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($boms as $bom)
                    <tr>
                        <td><strong>{{ $bom->Nama_bill_of_material }}</strong></td>
                        <td>
                            <span class="badge bg-{{ $bom->Status == 'approved' ? 'success' : 'secondary' }}">
                                {{ ucfirst($bom->Status) }}
                            </span>
                        </td>
                        <td>{{ $bom->barangHasBill->count() }} materials</td>
                        <td>{{ $bom->created_at->format('d M Y') }}</td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('bill-of-materials.show', $bom->Id_bill_of_material) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('bill-of-materials.edit', $bom->Id_bill_of_material) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('bill-of-materials.destroy', $bom->Id_bill_of_material) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this BOM?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No BOMs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
