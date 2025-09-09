@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Supplier</h2>
        <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Back</a>
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

    <form action="{{ route('supplier.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="Nama_Supplier" class="form-label">Supplier Name</label>
                <input type="text" name="Nama_Supplier" id="Nama_Supplier" 
                       class="form-control" placeholder="Enter supplier name" required>
            </div>
            <div class="col-md-6">
                <label for="Nama_Pegawai" class="form-label">Employee Name</label>
                <input type="text" name="Nama_Pegawai" id="Nama_Pegawai" 
                       class="form-control" placeholder="Enter employee name" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="Email" class="form-label">Email</label>
                <input type="email" name="Email" id="Email" 
                       class="form-control" placeholder="Enter email">
            </div>
            <div class="col-md-6">
                <label for="Kontak" class="form-label">Contact</label>
                <input type="text" name="Kontak" id="Kontak" 
                       class="form-control" placeholder="Enter contact number">
            </div>
        </div>

        <div class="mb-3 position-relative">
            <label for="alamat" class="form-label">Address (click on the map)</label>
            <input type="text" name="Alamat" id="alamat" class="form-control" autocomplete="off" required>
            <div id="alamat-suggestions" class="list-group position-absolute w-100" style="z-index:2000; max-height: 240px; overflow-y:auto; display:none;"></div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
        </div>

        <div id="map" class="mb-4 rounded border" style="height:400px;"></div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i> Save Supplier
        </button>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // default location: Surabaya
    var map = L.map('map').setView([-7.2575, 112.7521], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([-7.2575, 112.7521], {draggable:true}).addTo(map);

    function updateLocation(latlng, writeAddress) {
        document.getElementById('latitude').value = latlng.lat;
        document.getElementById('longitude').value = latlng.lng;
        if (writeAddress) {
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latlng.lat}&lon=${latlng.lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('alamat').value = data.display_name;
                });
        }
    }

    // set initial location
    updateLocation(marker.getLatLng(), false);

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
    var alamatInput = document.getElementById('alamat');
    var suggestionsEl = document.getElementById('alamat-suggestions');

    function renderSuggestionsNominatim(results) {
        suggestionsEl.innerHTML = '';
        if (!results || results.length === 0) { suggestionsEl.style.display = 'none'; return; }
        results.slice(0, 5).forEach(function(item){
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action';
            a.textContent = item.display_name;
            a.addEventListener('click', function(e){
                e.preventDefault();
                alamatInput.value = item.display_name;
                suggestionsEl.style.display = 'none';
                var lat = parseFloat(item.lat);
                var lon = parseFloat(item.lon);
                var latlng = { lat: lat, lng: lon };
                marker.setLatLng(latlng);
                if (item.boundingbox) {
                    var bb = item.boundingbox.map(parseFloat);
                    map.fitBounds([[bb[0], bb[2]], [bb[1], bb[3]]]);
                } else {
                    map.setView([lat, lon], Math.max(16, map.getZoom()));
                }
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lon;
            });
            suggestionsEl.appendChild(a);
        });
        suggestionsEl.style.display = 'block';
    }

    function renderSuggestionsPhoton(results) {
        suggestionsEl.innerHTML = '';
        if (!results || !results.features || results.features.length === 0) { suggestionsEl.style.display = 'none'; return; }
        results.features.slice(0, 5).forEach(function(f){
            var a = document.createElement('a');
            a.href = '#';
            a.className = 'list-group-item list-group-item-action';
            a.textContent = f.properties && f.properties.label ? f.properties.label : (f.properties && f.properties.name ? f.properties.name : 'Hasil');
            a.addEventListener('click', function(e){
                e.preventDefault();
                var lon = f.geometry.coordinates[0];
                var lat = f.geometry.coordinates[1];
                alamatInput.value = a.textContent;
                suggestionsEl.style.display = 'none';
                var latlng = { lat: lat, lng: lon };
                marker.setLatLng(latlng);
                if (f.bbox && f.bbox.length === 4) {
                    map.fitBounds([[f.bbox[1], f.bbox[0]], [f.bbox[3], f.bbox[2]]]);
                } else {
                    map.setView([lat, lon], Math.max(16, map.getZoom()));
                }
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lon;
            });
            suggestionsEl.appendChild(a);
        });
        suggestionsEl.style.display = 'block';
    }

    function geocodeAddress(query) {
        var normalized = query.replace(/^\s*(jl\.?|jln\.?)/i, 'Jalan');
        var bounds = map.getBounds();
        var viewbox = bounds.getWest() + ',' + bounds.getSouth() + ',' + bounds.getEast() + ',' + bounds.getNorth();
        var nominatimBase = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&countrycodes=id&limit=5&accept-language=id';

        fetch(nominatimBase + '&bounded=1&viewbox=' + encodeURIComponent(viewbox) + '&q=' + encodeURIComponent(normalized))
            .then(r => r.json())
            .then(results => {
                renderSuggestionsNominatim(results);
                if (results && results.length) return;
                return fetch(nominatimBase + '&q=' + encodeURIComponent(normalized))
                    .then(r => r.json())
                    .then(res2 => {
                        if (!res2 || !res2.length) {
                            return fetch('https://photon.komoot.io/api/?limit=5&q=' + encodeURIComponent(normalized))
                                .then(r => r.json())
                                .then(renderSuggestionsPhoton)
                                .catch(() => {});
                        } else {
                            renderSuggestionsNominatim(res2);
                        }
                    });
            })
            .catch(() => {});
    }

    alamatInput.addEventListener('input', function(){
        clearTimeout(geocodeTimeout);
        var q = this.value.trim();
        if (!q) { suggestionsEl.style.display = 'none'; return; }
        geocodeTimeout = setTimeout(function(){ geocodeAddress(q); }, 500);
    });

    document.addEventListener('click', function(e){
        if (!suggestionsEl.contains(e.target) && e.target !== alamatInput) {
            suggestionsEl.style.display = 'none';
        }
    });
</script>
@endpush
