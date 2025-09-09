@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Supplier Details</h2>
        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Supplier:</strong> {{ $supplier->Nama_Supplier }}</p>
            <p><strong>Employee Name:</strong> {{ $supplier->Nama_Pegawai }}</p>
            <p><strong>Email:</strong> {{ $supplier->Email ?? '-' }}</p>
            <p><strong>Contact:</strong> {{ $supplier->Kontak ?? '-' }}</p>
            <p><strong>Address:</strong> {{ $supplier->Alamat }}</p>
        </div>
    </div>

    <div class="mt-4">
        <div id="map" class="rounded border" style="height:400px;"></div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var lat = {{ $supplier->latitude ?? -7.2575 }};
    var lng = {{ $supplier->longitude ?? 112.7521 }};

    var map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("{{ $supplier->Nama_Supplier }}")
        .openPopup();
</script>
@endpush
