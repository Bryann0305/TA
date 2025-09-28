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
            <input type="date" name="Tanggal_Pemesanan" class="form-control" value="{{ date('Y-m-d') }}" readonly required>
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
                <th style="width: 200px;">Item</th>
                <th style="width: 100px;">Satuan</th>
                <th style="width: 100px;">Quantity</th>
                <th style="width: 130px;">Price</th>
                <th style="width: 130px;">Total</th>
                <th style="width: 100px;">Action</th>
            </tr>
            </thead>
            <tbody>
                <tr class="item-row">
                    <td>
                        <select name="details[0][bahan_baku_Id_Bahan]" class="form-select" required>
                            <option value="">-- Select Item --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->Id_Bahan }}" data-satuan="{{ $barang->Satuan }}">{{ $barang->Nama_Bahan }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-center satuan-display">-</td>
                    <td><input type="number" name="details[0][Jumlah]" class="form-control jumlah" required min="1"></td>
                    <td><input type="text" name="details[0][Harga]" class="form-control harga" required placeholder="0"></td>
                    <td><input type="text" name="details[0][Total]" class="form-control total" readonly></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-dark btn-sm btn-remove">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" class="text-center">
                        <button type="button" id="btn-add-item" class="btn btn-warning btn-sm">
                            <i class="bi bi-plus me-1"></i> Add Item
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Biaya Pengiriman --}}
        <div class="mb-3">
            <label for="Biaya_Pengiriman" class="form-label">Biaya Pengiriman</label>
            <input type="text" name="Biaya_Pengiriman" id="Biaya_Pengiriman" class="form-control" placeholder="0" value="{{ old('Biaya_Pengiriman', '0') }}">
        </div>

        {{-- Grand Total --}}
        <div class="mb-3">
            <label for="Total_Biaya" class="form-label">Grand Total</label>
            <input type="text" name="Total_Biaya" id="Total_Biaya" class="form-control" readonly>
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
                    <option value="{{ $barang->Id_Bahan }}" data-satuan="{{ $barang->Satuan }}">{{ $barang->Nama_Bahan }}</option>
                @endforeach
            </select>
        </td>
        <td class="text-center satuan-display">-</td>
        <td><input type="number" name="details[${index}][Jumlah]" class="form-control jumlah" required min="1"></td>
        <td><input type="text" name="details[${index}][Harga]" class="form-control harga" required placeholder="0"></td>
        <td><input type="text" name="details[${index}][Total]" class="form-control total" readonly></td>
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

// Update satuan when item is selected
document.addEventListener('change', function(e){
    if(e.target.tagName === 'SELECT' && e.target.name.includes('bahan_baku_Id_Bahan')){
        const row = e.target.closest('.item-row');
        const satuanDisplay = row.querySelector('.satuan-display');
        const selectedOption = e.target.selectedOptions[0];
        
        if(selectedOption && selectedOption.dataset.satuan){
            satuanDisplay.textContent = selectedOption.dataset.satuan;
        } else {
            satuanDisplay.textContent = '-';
        }
    }
});

// Handle input for both formatting and calculation
document.addEventListener('input', function(e){
    console.log('Input event triggered:', e.target.className);
    
    if(e.target.classList.contains('harga')){
        console.log('Processing harga input');
        let value = e.target.value.replace(/[^\d]/g, ''); // Remove non-digits
        if(value){
            // Format with Rp and dots as thousand separators
            value = 'Rp ' + parseInt(value).toLocaleString('id-ID');
            e.target.value = value;
            console.log('Formatted harga:', value);
        }
        
        // Calculate total after formatting
        let row = e.target.closest('.item-row');
        const qty = parseFloat(row.querySelector('.jumlah').value) || 0;
        
        // Parse price by removing Rp and dots, then converting to number
        const priceText = e.target.value.replace(/Rp\s?/g, '').replace(/\./g, '');
        const price = parseFloat(priceText) || 0;
        
        console.log('Qty:', qty, 'Price:', price);
        
        const total = qty * price;
        const formattedTotal = 'Rp ' + total.toLocaleString('id-ID');
        row.querySelector('.total').value = formattedTotal;
        console.log('Set total to:', formattedTotal);
        
        calculateGrandTotal();
    }
    
    if(e.target.classList.contains('jumlah')){
        console.log('Processing jumlah input');
        let row = e.target.closest('.item-row');
        const qty = parseFloat(e.target.value) || 0;
        
        // Parse price by removing Rp and dots, then converting to number
        const priceText = row.querySelector('.harga').value.replace(/Rp\s?/g, '').replace(/\./g, '');
        const price = parseFloat(priceText) || 0;
        
        console.log('Qty:', qty, 'Price:', price);
        
        const total = qty * price;
        const formattedTotal = 'Rp ' + total.toLocaleString('id-ID');
        row.querySelector('.total').value = formattedTotal;
        console.log('Set total to:', formattedTotal);
        
        calculateGrandTotal();
    }
    
    if(e.target.id === 'Biaya_Pengiriman'){
        console.log('Processing biaya pengiriman input');
        let value = e.target.value.replace(/[^\d]/g, ''); // Remove non-digits
        if(value){
            // Format with Rp and dots as thousand separators
            value = 'Rp ' + parseInt(value).toLocaleString('id-ID');
            e.target.value = value;
            console.log('Formatted biaya pengiriman:', value);
        }
        
        calculateGrandTotal();
    }
});

function calculateGrandTotal(){
    console.log('Calculating grand total...');
    let total = 0;
    document.querySelectorAll('.item-row').forEach((row, index) => {
        const totalElement = row.querySelector('.total');
        const totalValue = totalElement.value;
        console.log(`Row ${index} total value:`, totalValue);
        
        // Parse total by removing Rp and dots, then converting to number
        const totalText = totalValue.replace(/Rp\s?/g, '').replace(/\./g, '');
        const numericTotal = parseFloat(totalText) || 0;
        console.log(`Row ${index} numeric total:`, numericTotal);
        
        total += numericTotal;
    });
    
    // Add shipping cost
    const biayaPengirimanElement = document.getElementById('Biaya_Pengiriman');
    const biayaPengirimanValue = biayaPengirimanElement.value;
    console.log('Biaya pengiriman value:', biayaPengirimanValue);
    
    const biayaPengirimanText = biayaPengirimanValue.replace(/Rp\s?/g, '').replace(/\./g, '');
    const biayaPengiriman = parseFloat(biayaPengirimanText) || 0;
    console.log('Biaya pengiriman numeric:', biayaPengiriman);
    
    total += biayaPengiriman;
    
    console.log('Grand total calculated:', total);
    
    // Format grand total with currency format
    const formattedGrandTotal = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('Total_Biaya').value = formattedGrandTotal;
    console.log('Set grand total to:', formattedGrandTotal);
}
</script>
@endsection
