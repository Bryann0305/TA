@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Supplier</h2>
        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supplier.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="Nama_Supplier" class="form-label">Supplier Name</label>
            <input type="text" class="form-control" id="Nama_Supplier" name="Nama_Supplier" required>
        </div>

        <div class="mb-3">
            <label for="Nama_Pegawai" class="form-label">Employee Name</label>
            <input type="text" class="form-control" id="Nama_Pegawai" name="Nama_Pegawai" required>
        </div>

        <div class="mb-3">
            <label for="Email" class="form-label">Email</label>
            <input type="email" class="form-control" id="Email" name="Email">
        </div>

        <div class="mb-3">
            <label for="Kontak" class="form-label">Contact</label>
            <input type="text" class="form-control" id="Kontak" name="Kontak">
        </div>

        <div class="mb-3">
            <label for="Alamat" class="form-label">Address</label>
            <textarea class="form-control" id="Alamat" name="Alamat" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Supplier</button>
    </form>
</div>
@endsection
