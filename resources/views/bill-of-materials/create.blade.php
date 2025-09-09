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

        {{-- BOM Name --}}
        <div class="mb-3">
            <label for="Nama_bill_of_material" class="form-label">BOM Name</label>
            <input type="text" name="Nama_bill_of_material" class="form-control" required>
        </div>

        {{-- Hidden Status --}}
        <input type="hidden" name="Status" value="pending">

        {{-- Items --}}
        <h4>Items</h4>
        <div id="item-wrapper">
            <div class="row mb-2 align-items-center item">
                <div class="col-md-5">
                    <select name="barang[0][barang_Id_Bahan]" class="form-select" required>
                        <option value="">-- Select Item --</option>
                        @foreach($barangs as $b)
                            <option value="{{ $b->Id_Bahan }}">{{ $b->Nama_Bahan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="number" name="barang[0][Jumlah_Bahan]" class="form-control" placeholder="Quantity" min="1" required>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-danger btn-remove">Remove</button>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="button" id="btn-add-item" class="btn btn-secondary">
                <i class="bi bi-plus me-2"></i> Add Item
            </button>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i> Save BOM
        </button>
    </form>
</div>

<script>
let index = 1;

document.getElementById('btn-add-item').addEventListener('click', function() {
    const wrapper = document.getElementById('item-wrapper');
    const div = document.createElement('div');
    div.classList.add('row', 'mb-2', 'align-items-center', 'item');
    div.innerHTML = `
        <div class="col-md-5">
            <select name="barang[${index}][barang_Id_Bahan]" class="form-select" required>
                <option value="">-- Select Item --</option>
                @foreach($barangs as $b)
                    <option value="{{ $b->Id_Bahan }}">{{ $b->Nama_Bahan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" name="barang[${index}][Jumlah_Bahan]" class="form-control" placeholder="Quantity" min="1" required>
        </div>
        <div class="col-md-3 text-end">
            <button type="button" class="btn btn-danger btn-remove">Remove</button>
        </div>
    `;
    wrapper.appendChild(div);
    index++;
});

document.addEventListener('click', function(e){
    if(e.target && e.target.classList.contains('btn-remove')){
        e.target.closest('.item').remove();
    }
});
</script>
@endsection
