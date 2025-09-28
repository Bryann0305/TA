@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Category Details</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('category.edit', $category->Id_Kategori) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <a href="{{ route('category.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Category Information --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Category Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Category Name:</strong></td>
                            <td>{{ $category->Nama_Kategori }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Items in this Category --}}
    <div class="mt-4">
        <h4>Items in this Category</h4>
        @if($category->barang->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th>Stock</th>
                            <th>Unit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($category->barang as $item)
                            <tr>
                                <td>{{ $item->Nama_Bahan }}</td>
                                <td>
                                    <span class="badge {{ $item->Jenis == 'Bahan_Baku' ? 'bg-primary' : 'bg-success' }}">
                                        {{ $item->Jenis == 'Bahan_Baku' ? 'Raw Material' : 'Finished Product' }}
                                    </span>
                                </td>
                                <td>{{ number_format($item->Stok, 0, ',', '.') }}</td>
                                <td>{{ $item->Satuan }}</td>
                                <td>
                                    <span class="badge 
                                        {{ $item->Status == 'Critical Low' ? 'bg-danger' : 
                                           ($item->Status == 'Low' ? 'bg-warning' : 'bg-success') }}">
                                        {{ $item->Status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                No items found in this category.
            </div>
        @endif
    </div>
</div>
@endsection
