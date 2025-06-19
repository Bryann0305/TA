@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2>Create Purchase Order</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('procurement.store') }}">
        @csrf

        <div class="mb-3">
            <label for="supplier_Id_Supplier" class="form-label">Supplier</label>
            <select name="supplier_Id_Supplier" id="supplier_Id_Supplier" class="form-select" required>
                <option value="">-- Select Supplier --</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->Id_Supplier }}" {{ old('supplier_Id_Supplier') == $supplier->Id_Supplier ? 'selected' : '' }}>
                        {{ $supplier->Nama_Supplier }}
                    </option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="user_Id_User" value="{{ auth()->user()->Id_User }}">

                <div class="mb-3">
            <label for="Status" class="form-label">Status Pesanan</label>
            <select name="Status" id="Status" class="form-select" required>
                <option value="">-- Select Status --</option>
                <option value="Pending" {{ old('Status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ old('Status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Completed" {{ old('Status') == 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>


        <div class="mb-3">
            <label for="Tanggal_Pemesanan" class="form-label">Tanggal Pemesanan</label>
            <input type="date" name="Tanggal_Pemesanan" id="Tanggal_Pemesanan" class="form-control" value="{{ old('Tanggal_Pemesanan') }}" required>
        </div>

        <div class="mb-3">
            <label for="Tanggal_Kedatangan" class="form-label">Tanggal Kedatangan</label>
            <input type="date" name="Tanggal_Kedatangan" id="Tanggal_Kedatangan" class="form-control" value="{{ old('Tanggal_Kedatangan') }}">
        </div>

        <div class="mb-3">
            <label for="Status_Pembayaran" class="form-label">Status Pembayaran</label>
            <select name="Status_Pembayaran" id="Status_Pembayaran" class="form-select" required>
                <option value="">-- Select Status --</option>
                <option value="Pending" {{ old('Status_Pembayaran') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Confirmed" {{ old('Status_Pembayaran') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="Delivered" {{ old('Status_Pembayaran') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="Total_Biaya" class="form-label">Total Biaya (Rp)</label>
            <input type="number" name="Total_Biaya" id="Total_Biaya" class="form-control" value="{{ old('Total_Biaya') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('procurement.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
