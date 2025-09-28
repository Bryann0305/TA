@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Category Management</h2>
        <a href="{{ route('category.create') }}" class="btn btn-primary">
            <i class="bi bi-plus me-1"></i> Add Category
        </a>
    </div>

    <p class="text-muted mb-4">Manage product categories for inventory items.</p>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Table --}}
    <div class="table-responsive">
        <table id="categoryTable" class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 60px;">No</th>
                    <th class="text-center">Category Name</th>
                    <th class="text-center" style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td></td>
                        <td>{{ $category->Nama_Kategori }}</td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                {{-- View --}}
                                <a href="{{ route('category.show', $category->Id_Kategori) }}" class="btn btn-sm btn-info" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                {{-- Edit --}}
                                <a href="{{ route('category.edit', $category->Id_Kategori) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                {{-- Delete --}}
                                <form action="{{ route('category.destroy', $category->Id_Kategori) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this category?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">No categories found.</td>
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
        var t = $('#categoryTable').DataTable({
            pageLength: 10,
            responsive: true,
            autoWidth: false,
            columnDefs: [
                { targets: 0, orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search categories...",
                lengthMenu: "Show _MENU_ entries per page",
                zeroRecords: "No matching records found",
                info: "Showing _START_ to _END_ of _TOTAL_ categories",
                infoEmpty: "No category data available",
                paginate: {
                    previous: "‹",
                    next: "›"
                }
            }
        });

        // Re-number first column on each draw
        t.on('order.dt search.dt draw.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();
    });
</script>
@endpush
