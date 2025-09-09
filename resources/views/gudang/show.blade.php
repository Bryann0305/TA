@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Warehouse Details</h2>
        <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Warehouse Name:</strong> {{ $gudang->Nama_Gudang }}</p>
            <p><strong>Capacity:</strong> {{ $gudang->Kapasitas }}</p>
            <p><strong>Address:</strong> {{ $gudang->alamat ?? '-' }}</p>
            <p><strong>Coordinates:</strong> {{ $gudang->latitude ?? '-' }}, {{ $gudang->longitude ?? '-' }}</p>
        </div>
    </div>

    <div class="mb-4">
        <h5>Warehouse Location</h5>
        <div id="map" class="rounded border" style="height:400px;" data-initial-lat="{{ $gudang->latitude ?? -7.2575 }}" data-initial-lng="{{ $gudang->longitude ?? 112.7521 }}"></div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var mapEl = document.getElementById('map');
    var initialLat = parseFloat(mapEl.getAttribute('data-initial-lat')) || -7.2575;
    var initialLng = parseFloat(mapEl.getAttribute('data-initial-lng')) || 112.7521;
    var map = L.map('map').setView([initialLat, initialLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker statis
    L.marker([initialLat, initialLng]).addTo(map)
        .bindPopup("<strong>{{ $gudang->Nama_Gudang }}</strong><br>{{ $gudang->alamat ?? '-' }}")
        .openPopup();
</script>
@endpush
