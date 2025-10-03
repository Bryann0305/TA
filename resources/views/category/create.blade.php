@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Category</h2>
        <a href="{{ route('category.index') }}" class="btn btn-secondary">
             Back
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('category.store') }}" method="POST">
        @csrf

        {{-- Category Name --}}
        <div class="mb-3">
            <label for="Nama_Kategori" class="form-label">Category Name</label>
            <input type="text" name="Nama_Kategori" id="Nama_Kategori" class="form-control" 
                   value="{{ old('Nama_Kategori') }}" required>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i> Save Category
            </button>
        </div>
    </form>
</div>
@endsection
