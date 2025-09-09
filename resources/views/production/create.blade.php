@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2>Buat Produksi Baru</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('production.store') }}" method="POST">
        @csrf

        {{-- Pilih SPP / Production Order --}}
        <div class="mb-3">
            <label for="production_order_id" class="form-label">Pilih Surat Perintah Produksi</label>
            <select name="production_order_id" id="production_order_id" class="form-select" required onchange="showOrderDetail(this)">
                <option value="">-- Pilih Surat Perintah Produksi --</option>
                @forelse($orders as $order)
                    <option value="{{ $order->id }}" data-barang='@json(optional($order->pesananProduksi)->detail ?? [])'>
                        {{ $order->Nama_Produksi ?? ('Production Order #' . $order->id) }} (ID: {{ $order->id }})
                    </option>
                @empty
                    <option value="" disabled>Data SPP tidak tersedia</option>
                @endforelse
            </select>
        </div>
        <div id="order-detail" class="mb-3" style="display:none">
            <label class="form-label">Barang yang Dipesan:</label>
            <ul id="order-barang-list"></ul>
        </div>

        {{-- Pilih BOM --}}
        <div id="bom-container" class="mb-3">
            <label class="form-label">Pilih BOM & Jumlah</label>
            <div class="bom-row mb-2">
                <div class="row">
                    <div class="col-8">
                        <select name="bill_of_materials[]" class="form-select mb-1" required>
                            <option value="">-- Pilih BOM --</option>
                            @forelse($boms as $bom)
                                <option value="{{ $bom->Id_bill_of_material }}">
                                    {{ $bom->Nama_BOM ?? $bom->Nama_bill_of_material }}
                                </option>
                            @empty
                                <option value="" disabled>BOM belum tersedia</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="col-4">
                        <input type="number" name="jumlah_bom[]" class="form-control" min="1" value="1" required placeholder="Jumlah BOM">
                    </div>
                </div>
            </div>
        </div>

    <button type="button" id="add-bom" class="btn btn-secondary mb-3">Tambah BOM</button>
        <button type="submit" class="btn btn-primary">Buat Produksi</button>
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
            if (details && details.length > 0) {
                details.forEach(function(barang) {
                    const li = document.createElement('li');
                    li.textContent = barang.Nama_Bahan + ' (Jumlah: ' + barang.Jumlah + ')';
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
    document.getElementById('add-bom').addEventListener('click', function() {
        const container = document.getElementById('bom-container');
        const row = document.createElement('div');
        row.classList.add('bom-row', 'mb-2');
        row.innerHTML = `
            <div class="row">
                <div class="col-8">
                    <select name="bill_of_materials[]" class="form-select mb-1" required>
                        <option value="">-- Pilih BOM --</option>
                        @foreach($boms as $bom)
                            <option value="{{ $bom->Id_bill_of_material }}">
                                {{ $bom->Nama_BOM ?? $bom->Nama_bill_of_material }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4">
                    <input type="number" name="jumlah_bom[]" class="form-control" min="1" value="1" required placeholder="Jumlah BOM">
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger remove-bom">Hapus</button>
        `;
        container.appendChild(row);
        row.querySelector('.remove-bom').addEventListener('click', function() {
            row.remove();
        });
    });
</script>
@endsection
