@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Customer</h2>
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

    <form action="{{ route('pelanggan.update', $pelanggan->Id_Pelanggan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="Nama_Pelanggan" class="form-label">Customer Name</label>
                <input type="text" name="Nama_Pelanggan" id="Nama_Pelanggan" 
                       class="form-control" value="{{ old('Nama_Pelanggan', $pelanggan->Nama_Pelanggan) }}" required>
            </div>
            <div class="col-md-6">
                <label for="Nomor_Telp" class="form-label">Phone Number</label>
                <input type="text" name="Nomor_Telp" id="Nomor_Telp" 
                       class="form-control" value="{{ old('Nomor_Telp', $pelanggan->Nomor_Telp) }}" required>
            </div>
        </div>

        <div class="mb-3 position-relative">
            <label for="Alamat" class="form-label">Address (click on the map)</label>
            <input type="text" name="Alamat" id="Alamat" class="form-control" autocomplete="off" 
                   value="{{ old('Alamat', $pelanggan->Alamat) }}" required>
            <div id="alamat-suggestions" class="list-group position-absolute w-100" style="z-index:2000; max-height: 240px; overflow-y:auto; display:none;"></div>
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $pelanggan->latitude) }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $pelanggan->longitude) }}">
        </div>

        <div id="map" class="mb-4 rounded border" style="height:400px;"></div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i> Update Customer
        </button>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var initialLat = {{ $pelanggan->latitude ?? -7.2575 }};
    var initialLng = {{ $pelanggan->longitude ?? 112.7521 }};

    var map = L.map('map').setView([initialLat, initialLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([initialLat, initialLng], {draggable:true}).addTo(map);

    function updateLocation(latlng, writeAddress) {
        document.getElementById('latitude').value = latlng.lat;
        document.getElementById('longitude').value = latlng.lng;
        if (writeAddress) {
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latlng.lat}&lon=${latlng.lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('Alamat').value = data.display_name;
                });
        }
    }

    // update saat marker digeser
    marker.on('dragend', function(e) {
        updateLocation(e.target.getLatLng(), true);
    });

    // update saat klik peta
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLocation(e.latlng, true);
    });

    // Forward geocoding + suggestions
    var geocodeTimeout;
    var alamatInput = document.getElementById('Alamat');
    var suggestionsEl = document.getElementById('alamat-suggestions');

    function showSuggestions(suggestions) {
        suggestionsEl.innerHTML = '';
        suggestions.forEach(function(item) {
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action';
            a.textContent = item.display_name;
            a.addEventListener('click', function(e) {
                e.preventDefault();
                alamatInput.value = item.display_name;
                suggestionsEl.style.display = 'none';
                // Update marker position
                marker.setLatLng([item.lat, item.lon]);
                map.setView([item.lat, item.lon], 15);
                updateLocation(marker.getLatLng(), false);
            });
            suggestionsEl.appendChild(a);
        });
        suggestionsEl.style.display = 'block';
    }

    function hideSuggestions() {
        setTimeout(function() {
            suggestionsEl.style.display = 'none';
        }, 200);
    }

    alamatInput.addEventListener('input', function(){
        clearTimeout(geocodeTimeout);
        var q = this.value.trim();
        if (!q) { suggestionsEl.style.display = 'none'; return; }
        
        geocodeTimeout = setTimeout(function() {
            fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=5&addressdetails=1`)
                .then(response => response.json())
                .then(data => showSuggestions(data))
                .catch(() => {});
        }, 300);
    });

    alamatInput.addEventListener('blur', hideSuggestions);
    alamatInput.addEventListener('focus', function() {
        if (suggestionsEl.children.length > 0) {
            suggestionsEl.style.display = 'block';
        }
    });
</script>
@endpush
