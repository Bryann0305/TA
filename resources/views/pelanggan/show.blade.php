@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Customer Details</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <a href="{{ route('pelanggan.edit', $pelanggan->Id_Pelanggan) }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-2"><strong>ID</strong></div>
                    <div>{{ $pelanggan->Id_Pelanggan }}</div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2"><strong>Status</strong></div>
                    <div>
                        @if($pelanggan->status === 'active')
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-2"><strong>Customer Name</strong></div>
                    <div>{{ $pelanggan->Nama_Pelanggan }}</div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2"><strong>Phone Number</strong></div>
                    <div>{{ $pelanggan->Nomor_Telp ?? '-' }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="mb-2"><strong>Address</strong></div>
                    <div>{{ $pelanggan->Alamat ?? '-' }}</div>
                </div>
            </div>

            <div class="d-flex gap-2">
                @if($pelanggan->status === 'active')
                <form action="{{ route('pelanggan.deactivate', $pelanggan->Id_Pelanggan) }}" method="POST" onsubmit="return confirm('Deactivate this customer?');">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-secondary">
                        <i class="fas fa-user-slash me-1"></i> Deactivate
                    </button>
                </form>
                @else
                <form action="{{ route('pelanggan.toggle-status', $pelanggan->Id_Pelanggan) }}" method="POST" onsubmit="return confirm('Activate this customer?');">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-success">
                        <i class="fas fa-user-check me-1"></i> Activate
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
