@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2 class="mb-4">Add New Bill of Material</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('bill-of-materials.store') }}">
        @csrf

        <div class="mb-3">
            <label>BOM Name</label>
            <input type="text" name="Nama_bill_of_material" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="Status" class="form-select" required>
                <option value="">-- Select Status --</option>
                <option value="approved">Approved</option>
                <option value="draft">Draft</option>
            </select>
        </div>

        <hr>
        <h5>Select Raw Materials</h5>

        <div class="table-responsive mb-3">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">✔</th>
                        <th>Material Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($barangs as $barang)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" name="bahan_baku[{{ $barang->Id_Bahan }}][selected]" value="1">
                        </td>
<td>{{ $barang->Nama_Bahan }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('bill-of-materials.index') }}" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-success">Save BOM</button>
        </div>
    </form>
</div>
@endsection
