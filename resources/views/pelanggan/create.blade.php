@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Customer</h2>
        <a href="{{ route('pelanggan.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('pelanggan.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="Nama_Pelanggan" class="form-label">Customer Name</label>
                <input type="text" name="Nama_Pelanggan" id="Nama_Pelanggan" 
                       class="form-control" placeholder="Enter customer name" required>
            </div>
            <div class="col-md-6">
                <label for="Nomor_Telp" class="form-label">Phone Number</label>
                <input type="text" name="Nomor_Telp" id="Nomor_Telp" 
                       class="form-control" placeholder="Enter phone number" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="Alamat" class="form-label">Address (click on the map)</label>
            <input type="text" name="Alamat" id="Alamat" 
                   class="form-control" readonly required>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
        </div>

        <div id="map" class="mb-4 rounded border" style="height:400px;"></div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i> Save Customer
        </button>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([-7.2575, 112.7521], 13); // default Surabaya

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([-7.2575, 112.7521], {draggable:true}).addTo(map);

    function updateLocation(latlng) {
        document.getElementById('latitude').value = latlng.lat;
        document.getElementById('longitude').value = latlng.lng;

        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latlng.lat}&lon=${latlng.lng}&format=json`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('Alamat').value = data.display_name;
            });
    }

    // set initial location
    updateLocation(marker.getLatLng());

    marker.on('dragend', function(e) {
        updateLocation(e.target.getLatLng());
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLocation(e.latlng);
    });
</script>
@endpush
