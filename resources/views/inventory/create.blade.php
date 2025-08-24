@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2>Add Inventory Item</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventory.store') }}" method="POST" id="inventoryForm">
        @csrf

        <div class="mb-3">
            <label class="form-label">Nama Bahan / Produk</label>
            <input type="text" name="Nama_Bahan" class="form-control" value="{{ old('Nama_Bahan') }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Jenis</label>
            <select name="Jenis" class="form-select" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="Bahan_Baku" {{ old('Jenis')=='Bahan_Baku' ? 'selected' : '' }}>Bahan Baku</option>
                <option value="Produk" {{ old('Jenis')=='Produk' ? 'selected' : '' }}>Produk Jadi</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori_Id_Kategori" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach($kategori as $k)
                    <option value="{{ $k->Id_Kategori }}" {{ old('kategori_Id_Kategori') == $k->Id_Kategori ? 'selected' : '' }}>
                        {{ $k->Nama_Kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Stok Saat Ini</label>
            <input type="number" name="Stok" class="form-control" value="{{ old('Stok',0) }}" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Reorder Point</label>
            <input type="number" name="Reorder_Point" class="form-control" value="{{ old('Reorder_Point',100) }}" min="1" required>
        </div>

        <h5>EOQ Calculation</h5>

        <div class="mb-3">
            <label class="form-label">Demand Tahunan (D)</label>
            <input type="number" id="demand" class="form-control" value="{{ old('D',0) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Biaya Pemesanan per Order (S)</label>
            <input type="number" id="sCost" class="form-control" value="{{ old('S',0) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Biaya Penyimpanan per Unit (H)</label>
            <input type="number" id="hCost" class="form-control" value="{{ old('H',0) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">EOQ (otomatis)</label>
            <input type="number" name="EOQ" id="eoq" class="form-control" readonly>
        </div>

        <input type="hidden" name="Status" id="status" value="">

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function calculateEOQ() {
        let D = parseFloat(document.getElementById('demand').value) || 0;
        let S = parseFloat(document.getElementById('sCost').value) || 0;
        let H = parseFloat(document.getElementById('hCost').value) || 1; // jangan 0
        let eoq = Math.sqrt((2 * D * S)/H);
        document.getElementById('eoq').value = Math.round(eoq);
    }

    function updateStatus() {
        let stok = parseFloat(document.querySelector('input[name="Stok"]').value) || 0;
        let reorder = parseFloat(document.querySelector('input[name="Reorder_Point"]').value) || 100;
        let statusField = document.getElementById('status');
        if(stok <= reorder/2) statusField.value = 'Critical Low';
        else if(stok < reorder) statusField.value = 'Below Reorder Point';
        else statusField.value = 'In Stock';
    }

    document.getElementById('demand').addEventListener('input', calculateEOQ);
    document.getElementById('sCost').addEventListener('input', calculateEOQ);
    document.getElementById('hCost').addEventListener('input', calculateEOQ);

    document.querySelector('input[name="Stok"]').addEventListener('input', updateStatus);
    document.querySelector('input[name="Reorder_Point"]').addEventListener('input', updateStatus);

    // inisialisasi
    calculateEOQ();
    updateStatus();
</script>
@endpush
