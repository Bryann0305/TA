@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Create BOM</h2>
        <a href="{{ route('bom.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('bom.store') }}" method="POST">
        @csrf

        {{-- Hidden Status --}}
        <input type="hidden" name="Status" value="pending">

        {{-- Finished Goods (Produk) Selection --}}
        <div class="mb-4">
            <h4>Finished Goods (Produk)</h4>
            <div class="mb-3">
                <select name="finished_good_id" class="form-select" required>
                    <option value="">-- Select Finished Good --</option>
                    @foreach($finishedGoods as $product)
                        <option value="{{ $product->Id_Bahan }}">{{ $product->Nama_Bahan }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Raw Materials (Bahan Baku) --}}
        <div class="mb-4">
            <h4>Raw Materials (Bahan Baku)</h4>
            <div id="raw-materials-wrapper">
                <div class="row mb-2 align-items-center raw-material-item">
                    <div class="col-md-5">
                        <select name="raw_materials[0][barang_Id_Bahan]" class="form-select raw-material-select" required>
                            <option value="">-- Select Raw Material --</option>
                            @foreach($rawMaterials as $material)
                                <option value="{{ $material->Id_Bahan }}" data-satuan="{{ $material->Satuan }}">{{ $material->Nama_Bahan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <span class="satuan-display text-center d-block">-</span>
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="raw_materials[0][Jumlah_Bahan]" class="form-control" placeholder="Quantity" min="1" required>
                    </div>
                    <div class="col-md-2 text-end">
                        <button type="button" class="btn btn-danger btn-sm btn-remove-raw">Remove</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="button" id="btn-add-raw-material" class="btn btn-secondary">
                <i class="bi bi-plus me-2"></i> Add Raw Material
            </button>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i> Save BOM
        </button>
    </form>
</div>

<script>
let rawMaterialIndex = 1;

// Add Raw Material button
document.getElementById('btn-add-raw-material').addEventListener('click', function() {
    const wrapper = document.getElementById('raw-materials-wrapper');
    const div = document.createElement('div');
    div.classList.add('row', 'mb-2', 'align-items-center', 'raw-material-item');
    div.innerHTML = `
        <div class="col-md-5">
            <select name="raw_materials[${rawMaterialIndex}][barang_Id_Bahan]" class="form-select raw-material-select" required>
                <option value="">-- Select Raw Material --</option>
                @foreach($rawMaterials as $material)
                    <option value="{{ $material->Id_Bahan }}" data-satuan="{{ $material->Satuan }}">{{ $material->Nama_Bahan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <span class="satuan-display text-center d-block">-</span>
        </div>
        <div class="col-md-3">
            <input type="number" name="raw_materials[${rawMaterialIndex}][Jumlah_Bahan]" class="form-control" placeholder="Quantity" min="1" required>
        </div>
        <div class="col-md-2 text-end">
            <button type="button" class="btn btn-danger btn-sm btn-remove-raw">Remove</button>
        </div>
    `;
    wrapper.appendChild(div);
    rawMaterialIndex++;
});

// Remove Raw Material button
document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('btn-remove-raw')){
        e.target.closest('.raw-material-item').remove();
    }
});

// Update Satuan display when raw material is selected
document.addEventListener('change', function(e){
    if(e.target && e.target.classList.contains('raw-material-select')){
        const selectedOption = e.target.options[e.target.selectedIndex];
        const satuan = selectedOption.getAttribute('data-satuan');
        const satuanDisplay = e.target.closest('.raw-material-item').querySelector('.satuan-display');
        satuanDisplay.textContent = satuan || '-';
    }
});

// Initialize satuan display for first item
document.addEventListener('DOMContentLoaded', function(){
    const firstSelect = document.querySelector('.raw-material-select');
    if(firstSelect) {
        firstSelect.addEventListener('change', function(){
            const selectedOption = this.options[this.selectedIndex];
            const satuan = selectedOption.getAttribute('data-satuan');
            const satuanDisplay = this.closest('.raw-material-item').querySelector('.satuan-display');
            satuanDisplay.textContent = satuan || '-';
        });
    }
});
</script>
@endsection
