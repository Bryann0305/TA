@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Inventory Status (EOQ & ROP Real-time)</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                    <th>EOQ</th>
                    <th>ROP</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangList as $index => $b)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $b->Nama_Bahan }}</td>
                        <td>{{ $b->Stok }}</td>
                        <td>{{ $b->EOQ }}</td>
                        <td>{{ $b->ROP }}</td>
                        <td>
                            @if($b->Stok <= $b->ROP)
                                <span class="badge bg-danger">Reorder!</span>
                            @else
                                <span class="badge bg-success">Safe</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
