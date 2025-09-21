@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add Production</h2>
        <a href="{{ route('production.index') }}" class="btn btn-secondary">Back</a>
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

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <form action="{{ route('production.store') }}" method="POST">
        @csrf

        {{-- Pilih Surat Perintah Produksi --}}
        <div class="mb-3">
            <label for="production_order_id" class="form-label">Pilih Surat Perintah Produksi</label>
            <select name="production_order_id" id="production_order_id" class="form-select" required onchange="showOrderDetail(this)">
                <option value="">-- Pilih Surat Perintah Produksi --</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}" 
                        data-barang='@json(optional($order->pesananProduksi)->detail->map(function($d){
                            return ["Nama_Bahan" => $d->barang->Nama_Bahan ?? "-", "Jumlah" => $d->Jumlah ?? 0];
                        }) ?? [])'>
                        {{ $order->Nama_Produksi ?? 'Production Order #' . $order->id }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Detail Barang Pesanan --}}
        <div id="order-detail" class="mb-3" style="display:none">
            <label class="form-label">Barang yang Dipesan:</label>
            <ul id="order-barang-list" class="list-group list-group-flush"></ul>
        </div>

        {{-- Pilih Produk & BOM --}}
        <h5>Pilih Produk & BOM</h5>
        <table class="table table-bordered align-middle" id="produk-bom-table">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>BOM</th>
                    <th style="width:100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr class="produk-bom-row">
                    <td>
                        <select name="produk_ids[]" class="form-select" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($barangs as $barang)
                                <option value="{{ $barang->Id_Bahan }}">{{ $barang->Nama_Bahan }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="bom_ids[]" class="form-select" required>
                            <option value="">-- Pilih BOM --</option>
                            @foreach($boms as $bom)
                                <option value="{{ $bom->Id_bill_of_material }}">{{ $bom->Nama_BOM ?? $bom->Nama_bill_of_material }}</option>
                            @endforeach
                        </select>
                    </td>
                    <!-- <td><input type="number" name="jumlah_bom[]" class="form-control" min="1" value="1" required></td> -->
                    <td class="text-center">
                        <button type="button" class="btn btn-dark btn-sm btn-remove">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>

                {{-- Tombol Add --}}
                <tr>
                    <td colspan="4" class="text-center">
                        <button type="button" id="btn-add-produk-bom" class="btn btn-warning btn-sm">
                            <i class="bi bi-plus me-1"></i> Add Produk & BOM
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Buat Produksi
        </button>
    </form>
</div>

<script>
let bomIndex = 1;

// Tampilkan detail barang pesanan
function showOrderDetail(select) {
    const selected = select.options[select.selectedIndex];
    const barangList = document.getElementById('order-barang-list');
    const detailDiv = document.getElementById('order-detail');
    barangList.innerHTML = '';

    if (selected.dataset.barang) {
        const details = JSON.parse(selected.dataset.barang);
        if (details.length > 0) {
            details.forEach(barang => {
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'py-1');
                li.textContent = `${barang.Nama_Bahan} (Jumlah: ${barang.Jumlah})`;
                barangList.appendChild(li);
            });
            detailDiv.style.display = 'block';
        } else {
            detailDiv.style.display = 'none';
        }
    } else {
        detailDiv.style.display = 'none';
    }
}

// Tambah row Produk & BOM
document.getElementById('btn-add-produk-bom').addEventListener('click', function(){
    const tbody = document.querySelector('#produk-bom-table tbody');
    const addRow = document.querySelector('#btn-add-produk-bom').closest('tr');

    const row = document.createElement('tr');
    row.classList.add('produk-bom-row');
    row.innerHTML = `
        <td>
            <select name="produk_ids[]" class="form-select" required>
                <option value="">-- Pilih Produk --</option>
                @foreach($barangs as $barang)
                    <option value="{{ $barang->Id_Bahan }}">{{ $barang->Nama_Bahan }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select name="bom_ids[]" class="form-select" required>
                <option value="">-- Pilih BOM --</option>
                @foreach($boms as $bom)
                    <option value="{{ $bom->Id_bill_of_material }}">{{ $bom->Nama_BOM ?? $bom->Nama_bill_of_material }}</option>
                @endforeach
            </select>
        </td>
    <!-- <td><input type="number" name="jumlah_bom[]" class="form-control" min="1" value="1" required></td> -->
        <td class="text-center">
            <button type="button" class="btn btn-dark btn-sm btn-remove">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.insertBefore(row, addRow);
    bomIndex++;
});

// Remove row
document.addEventListener('click', function(e){
    if(e.target && (e.target.classList.contains('btn-remove') || e.target.closest('.btn-remove'))){
        e.target.closest('.produk-bom-row').remove();
    }
});
</script>
@endsection
