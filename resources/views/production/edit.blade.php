@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Production</h2>
        <a href="{{ route('production.index', ['tab' => request('tab', 'planned')]) }}" class="btn btn-secondary">Back</a>
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

    <form action="{{ route('production.update', $produksi->Id_Produksi) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Hidden input untuk tab --}}
        <input type="hidden" name="tab" value="{{ request('tab', 'planned') }}">

        {{-- Pilih Surat Perintah Produksi --}}
        <div class="mb-3">
            <label for="production_order_id" class="form-label">Pilih Surat Perintah Produksi</label>
            <select name="production_order_id" id="production_order_id" class="form-select" required onchange="showOrderDetail(this)">
                <option value="">-- Pilih Surat Perintah Produksi --</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}" 
                        data-barang='@json(optional($order->pesananProduksi->detail ?? collect([]))->map(function($d){
                            return ["Nama_Bahan" => $d->barang->Nama_Bahan ?? "-", "Jumlah" => $d->Jumlah ?? 0];
                        }))'
                        {{ $produksi->production_order_id == $order->id ? 'selected' : '' }}>
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

        {{-- Tabel BOM --}}
        <h5 class="mt-4">Pilih BOM & Jumlah</h5>
        <table class="table table-bordered align-middle" id="bom-table">
            <thead class="table-light">
                <tr>
                    <th>BOM</th>
                    <th style="width: 150px;">Jumlah</th>
                    <th style="width: 100px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produksi->details->groupBy('bill_of_material_id') as $bomId => $details)
                    <tr class="bom-row">
                        <td>
                            <select name="bom_ids[]" class="form-select" required>
                                <option value="">-- Pilih BOM --</option>
                                @foreach($boms as $bom)
                                    <option value="{{ $bom->Id_bill_of_material }}" {{ $bom->Id_bill_of_material == $bomId ? 'selected' : '' }}>
                                        {{ str_replace('BOM - ', '', $bom->Nama_BOM ?? $bom->Nama_bill_of_material) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="jumlah_bom[]" class="form-control" min="1" 
                                   value="{{ $details->sum('jumlah') }}" required>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-dark btn-sm btn-remove">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach

                {{-- Baris tombol Add --}}
                <tr>
                    <td colspan="3" class="text-center">
                        <button type="button" id="btn-add-bom" class="btn btn-warning btn-sm">
                            <i class="bi bi-plus me-1"></i> Add BOM
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Update Produksi
        </button>
    </form>
</div>

<script>
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

// Tambah row BOM baru
document.getElementById('btn-add-bom').addEventListener('click', function() {
    const tbody = document.querySelector('#bom-table tbody');
    const addRow = document.querySelector('#btn-add-bom').closest('tr');

    const row = document.createElement('tr');
    row.classList.add('bom-row');
    row.innerHTML = `
        <td>
            <select name="bom_ids[]" class="form-select" required>
                <option value="">-- Pilih BOM --</option>
                @foreach($boms as $bom)
                    <option value="{{ $bom->Id_bill_of_material }}">{{ str_replace('BOM - ', '', $bom->Nama_BOM ?? $bom->Nama_bill_of_material) }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="jumlah_bom[]" class="form-control" min="1" value="1" required></td>
        <td class="text-center">
            <button type="button" class="btn btn-dark btn-sm btn-remove">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.insertBefore(row, addRow);
});

// Remove row BOM
document.addEventListener('click', function(e){
    if(e.target && (e.target.classList.contains('btn-remove') || e.target.closest('.btn-remove'))){
        e.target.closest('.bom-row').remove();
    }
});

// Tampilkan detail barang saat page load
document.addEventListener('DOMContentLoaded', function(){
    const select = document.getElementById('production_order_id');
    if(select.value) showOrderDetail(select);
});
</script>
@endsection
