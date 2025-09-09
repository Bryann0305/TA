@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Procurement</h2>
        <a href="{{ route('procurement.index') }}" class="btn btn-secondary">Back</a>
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

    <form action="{{ route('procurement.store') }}" method="POST">
        @csrf

        {{-- Supplier --}}
        <div class="mb-3">
            <label for="supplier_Id_Supplier" class="form-label">Supplier</label>
            <select name="supplier_Id_Supplier" class="form-select" required>
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->Id_Supplier }}">{{ $supplier->Nama_Supplier }}</option>
                @endforeach
            </select>
        </div>

        {{-- Gudang --}}
        <div class="mb-3">
            <label for="gudang_Id_Gudang" class="form-label">Gudang</label>
            <select name="gudang_Id_Gudang" class="form-select" required>
                <option value="">-- Select Gudang --</option>
                @foreach($gudangs as $gudang)
                    <option value="{{ $gudang->Id_Gudang }}">{{ $gudang->Nama_Gudang }}</option>
                @endforeach
            </select>
        </div>

        {{-- Order Date --}}
        <div class="mb-3">
            <label for="Tanggal_Pemesanan" class="form-label">Order Date</label>
            <input type="date" name="Tanggal_Pemesanan" class="form-control" required>
        </div>

        {{-- Arrival Date --}}
        <div class="mb-3">
            <label for="Tanggal_Kedatangan" class="form-label">Arrival Date</label>
            <input type="date" name="Tanggal_Kedatangan" class="form-control">
        </div>

        {{-- Payment Method --}}
        <div class="mb-3">
            <label for="Metode_Pembayaran" class="form-label">Payment Method</label>
            <select name="Metode_Pembayaran" class="form-select" required>
                <option value="">-- Select Payment Method --</option>
                <option value="Transfer">Transfer</option>
                <option value="Tunai">Cash</option>
            </select>
        </div>

        <hr>

        {{-- Items --}}
        <h4>Items</h4>
        <table class="table table-bordered align-middle" id="item-table">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th style="width: 120px;">Quantity</th>
                    <th style="width: 120px;">Price</th>
                    <th style="width: 120px;">Total</th>
                    <th style="width: 100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="item-row">
                    <td>
                        <select name="details[0][bahan_baku_Id_Bahan]" class="form-select" required>
                            <option value="">-- Select Item --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->Id_Bahan }}">{{ $barang->Nama_Bahan }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" name="details[0][Jumlah]" class="form-control jumlah" required min="1"></td>
                    <td><input type="number" name="details[0][Harga]" class="form-control harga" required min="0"></td>
                    <td><input type="number" name="details[0][Total]" class="form-control total" readonly></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-dark btn-sm btn-remove">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" class="text-center">
                        <button type="button" id="btn-add-item" class="btn btn-warning btn-sm">
                            <i class="bi bi-plus me-1"></i> Add Item
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Grand Total --}}
        <div class="mb-3">
            <label for="Total_Biaya" class="form-label">Grand Total</label>
            <input type="number" name="Total_Biaya" id="Total_Biaya" class="form-control" readonly>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Save Procurement
        </button>
    </form>
</div>

<script>
let index = 1;

// Add item row
document.getElementById('btn-add-item').addEventListener('click', function() {
    const tbody = document.querySelector('#item-table tbody');
    const addRow = document.querySelector('#btn-add-item').closest('tr');
    const row = document.createElement('tr');
    row.classList.add('item-row');
    row.innerHTML = `
        <td>
            <select name="details[${index}][bahan_baku_Id_Bahan]" class="form-select" required>
                <option value="">-- Select Item --</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->Id_Bahan }}">{{ $barang->Nama_Bahan }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="details[${index}][Jumlah]" class="form-control jumlah" required min="1"></td>
        <td><input type="number" name="details[${index}][Harga]" class="form-control harga" required min="0"></td>
        <td><input type="number" name="details[${index}][Total]" class="form-control total" readonly></td>
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
        e.target.closest('.item-row').remove();
        calculateGrandTotal();
    }
});

// Calculate total per row and grand total
document.addEventListener('input', function(e){
    if(e.target.classList.contains('jumlah') || e.target.classList.contains('harga')){
        let row = e.target.closest('.item-row');
        const qty = parseFloat(row.querySelector('.jumlah').value) || 0;
        const price = parseFloat(row.querySelector('.harga').value) || 0;
        row.querySelector('.total').value = qty * price;
        calculateGrandTotal();
    }
});

function calculateGrandTotal(){
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        total += parseFloat(row.querySelector('.total').value) || 0;
    });
    document.getElementById('Total_Biaya').value = total;
}
</script>
@endsection
