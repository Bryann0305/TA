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

    <form method="POST" action="{{ route('procurement.store') }}" id="purchase-order-form">
        @csrf
        <input type="hidden" name="user_Id_User" value="{{ auth()->user()->Id_User }}">

        {{-- Order Details --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4>Order Details</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
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
                    <div class="col-md-6 mb-3">
                        <label for="Tanggal_Pemesanan" class="form-label">Order Date</label>
                        <input type="date" name="Tanggal_Pemesanan" id="Tanggal_Pemesanan" class="form-control" value="{{ old('Tanggal_Pemesanan', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="Status" class="form-label">Order Status</label>
                        <select name="Status" id="Status" class="form-select" required>
                            <option value="Pending" {{ old('Status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Approved" {{ old('Status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Completed" {{ old('Status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="Status_Pembayaran" class="form-label">Payment Status</label>
                        <select name="Status_Pembayaran" id="Status_Pembayaran" class="form-select" required>
                            <option value="Pending" {{ old('Status_Pembayaran') == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Confirmed" {{ old('Status_Pembayaran') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="Delivered" {{ old('Status_Pembayaran') == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Details --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Items</h4>
                <button type="button" class="btn btn-primary" id="add-item-btn">Add Item</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Unit Price (Rp)</th>
                            <th>Subtotal (Rp)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="items-tbody">
                        {{-- JS will add rows here --}}
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Total --}}
        <div class="d-flex justify-content-end mb-4">
            <h4>Total: Rp <span id="grand-total">0.00</span></h4>
            <input type="hidden" name="Total_Biaya" id="total-biaya-input" value="0">
        </div>

        <button type="submit" class="btn btn-success">Save Purchase Order</button>
        <a href="{{ route('procurement.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addItemBtn = document.getElementById('add-item-btn');
    const itemsTbody = document.getElementById('items-tbody');
    const grandTotalSpan = document.getElementById('grand-total');
    const totalBiayaInput = document.getElementById('total-biaya-input');
    const barangs = @json($barangs);
    let itemIndex = 0;

    addItemBtn.addEventListener('click', function () {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="items[${itemIndex}][barang_Id_Barang]" class="form-select item-select" required>
                    <option value="">-- Select Item --</option>
                    ${barangs.map(b => `<option value="${b.Id_Bahan}">${b.Nama_Bahan}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" name="items[${itemIndex}][Jumlah]" class="form-control item-qty" min="1" value="1" required></td>
            <td><input type="number" name="items[${itemIndex}][Harga_Satuan]" class="form-control item-price" min="0" value="0" required></td>
            <td><input type="text" class="form-control item-subtotal" readonly value="0.00"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-item-btn">Remove</button></td>
        `;
        itemsTbody.appendChild(row);
        itemIndex++;
    });

    itemsTbody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item-btn')) {
            e.target.closest('tr').remove();
            updateGrandTotal();
        }
    });

    itemsTbody.addEventListener('input', function (e) {
        if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-price')) {
            const row = e.target.closest('tr');
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const subtotal = qty * price;
            row.querySelector('.item-subtotal').value = subtotal.toFixed(2);
            updateGrandTotal();
        }
    });

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-subtotal').forEach(function (subtotalEl) {
            total += parseFloat(subtotalEl.value) || 0;
        });
        grandTotalSpan.textContent = total.toFixed(2);
        totalBiayaInput.value = total;
    }
});
</script>
@endpush

