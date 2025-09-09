@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Production Order</h2>
        <a href="{{ route('pesanan_produksi.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pesanan_produksi.store') }}" method="POST">
        @csrf

        {{-- Order Date --}}
        <div class="mb-3">
            <label for="Tanggal_Pesanan" class="form-label">Order Date</label>
            <input type="date" name="Tanggal_Pesanan" class="form-control" required>
        </div>

        {{-- Customer --}}
        <div class="mb-3">
            <label for="pelanggan_Id_Pelanggan" class="form-label">Customer</label>
            <select name="pelanggan_Id_Pelanggan" class="form-select" required>
                <option value="">-- Select Customer --</option>
                @foreach($pelanggans as $pelanggan)
                    <option value="{{ $pelanggan->Id_Pelanggan }}">{{ $pelanggan->Nama_Pelanggan }}</option>
                @endforeach
            </select>
        </div>

        {{-- Products / Materials --}}
        <h4>Products / Materials</h4>
        <table class="table table-bordered align-middle" id="product-table">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th style="width: 150px;">Quantity</th>
                    <th style="width: 100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="product-row">
                    <td>
                        <select name="barang[0][barang_Id_Bahan]" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            @foreach($barangs as $b)
                                <option value="{{ $b->Id_Bahan }}">{{ $b->Nama_Bahan }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="barang[0][Jumlah]" class="form-control" required min="1"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-dark btn-sm btn-remove">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                {{-- Baris tombol Add --}}
                <tr>
                    <td colspan="3" class="text-center">
                        <button type="button" id="btn-add-product" class="btn btn-warning btn-sm">
                            <i class="bi bi-plus me-1"></i> Add Product
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Save Order
        </button>
    </form>
</div>

<script>
let index = 1;

// Add new product row
document.getElementById('btn-add-product').addEventListener('click', function() {
    const tbody = document.querySelector('#product-table tbody');
    const addRow = document.querySelector('#btn-add-product').closest('tr');
    
    const row = document.createElement('tr');
    row.classList.add('product-row');
    row.innerHTML = `
        <td>
            <select name="barang[${index}][barang_Id_Bahan]" class="form-select" required>
                <option value="">-- Select Product --</option>
                @foreach($barangs as $b)
                    <option value="{{ $b->Id_Bahan }}">{{ $b->Nama_Bahan }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="barang[${index}][Jumlah]" class="form-control" required min="1"></td>
        <td class="text-center">
            <button type="button" class="btn btn-dark btn-sm btn-remove">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.insertBefore(row, addRow);
    index++;
});

// Remove row
document.addEventListener('click', function(e){
    if(e.target && (e.target.classList.contains('btn-remove') || e.target.closest('.btn-remove'))){
        e.target.closest('.product-row').remove();
    }
});
</script>
@endsection
