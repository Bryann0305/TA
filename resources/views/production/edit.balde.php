@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2><strong>Edit Production Order</strong></h2>
    <p>Modify the details of your production order.</p>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('production.update', $produksi->Id_Produksi) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="Nama_Produksi" class="form-label">Production Name</label>
            <input type="text" class="form-control" name="Nama_Produksi" value="{{ old('Nama_Produksi', $produksi->Nama_Produksi) }}" required>
        </div>

        <div class="mb-3">
            <label for="bill_of_material_Id_bill_of_material" class="form-label">Bill of Material</label>
            <select class="form-select" name="bill_of_material_Id_bill_of_material" required>
                @foreach($boms as $bom)
                    <option value="{{ $bom->Id_bill_of_material }}" {{ $produksi->bill_of_material_Id_bill_of_material == $bom->Id_bill_of_material ? 'selected' : '' }}>
                        {{ $bom->Nama_BOM }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="Jumlah_Produksi" class="form-label">Production Quantity (Liter)</label>
            <input type="number" class="form-control" name="Jumlah_Produksi" value="{{ old('Jumlah_Produksi', $produksi->Jumlah_Produksi) }}" min="1" required>
        </div>

        <div class="mb-3">
            <label for="Status" class="form-label">Status</label>
            <select class="form-select" name="Status" required>
                <option value="planned" {{ $produksi->Status == 'planned' ? 'selected' : '' }}>Planned</option>
                <option value="in_progress" {{ $produksi->Status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $produksi->Status == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $produksi->Status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Production</button>
        <a href="{{ route('production.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
