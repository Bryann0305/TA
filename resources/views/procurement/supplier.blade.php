@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-2">Suppliers</h2>
    <p class="text-muted">Manage supplier information</p>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Total Suppliers</h6>
                    <h4>{{ $suppliers->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Active Suppliers</h6>
                    <h4>{{ $suppliers->where('Status', 'Active')->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h6>Inactive Suppliers</h6>
                    <h4>{{ $suppliers->where('Status', 'Inactive')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="procurementTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('procurement.index') }}">Purchase Orders</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('procurement.supplier') }}">Suppliers</a>
        </li>
    </ul>

    <table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID Supplier</th>
            <th>Nama Supplier</th>
            <th>Nama Pegawai</th>
            <th>Email</th>
            <th>Kontak</th>
            <th>Alamat</th>
            <th>Status</th>
            <th style="width: 140px;">Actions</th>
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
                <span class="badge {{ $supplier->Status == 'Active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $supplier->Status }}
                </span>
            </td>
            <td>
                <a href="{{ route('procurement.show', ['id' => $supplier->Id_Supplier]) }}" 
                   class="btn btn-info btn-sm" title="Show">
                    <i class="fas fa-eye"></i>
                </a>

                <a href="{{ route('procurement.edit_supplier', ['id' => $supplier->Id_Supplier]) }}" 
                   class="btn btn-warning btn-sm" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>

                <form action="{{ route('procurement.destroy_supplier', ['id' => $supplier->Id_Supplier]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this supplier?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center text-muted">No suppliers found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

    <a href="{{ route('procurement.create_supplier') }}" class="btn btn-primary mt-3">New Supplier</a>
</div>
@endsection
