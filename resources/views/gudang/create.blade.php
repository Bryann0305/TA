@extends('layouts.sidebar')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Add Gudang</h2>
        <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Back</a>
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

    <form id="form-gudang-create" action="{{ route('gudang.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="Nama_Gudang" class="form-label">Warehouse Name</label>
                <input type="text" name="Nama_Gudang" id="Nama_Gudang" 
                       class="form-control" placeholder="Enter warehouse name" required>
            </div>
            <div class="col-md-6">
                <label for="Kapasitas" class="form-label">Capacity</label>
                <input type="number" name="Kapasitas" id="Kapasitas" 
                       class="form-control" placeholder="Enter capacity" required>
            </div>
        </div>

        <div class="mb-3 position-relative">
            <label for="alamat" class="form-label">Address (click on the map)</label>
            <input type="text" name="alamat" id="alamat" class="form-control" autocomplete="off" required>
            <div id="alamat-suggestions" class="list-group position-absolute w-100" style="z-index:2000; max-height: 240px; overflow-y:auto; display:none;"></div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
        </div>

        <div id="map" class="mb-4 rounded border" style="height:400px;"></div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-2"></i> Save Gudang
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
            // reverse geocoding OpenStreetMap
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

    // forward geocoding when typing address (debounced)
    var geocodeTimeout;
    var alamatInput = document.getElementById('alamat');
    var suggestionsEl = document.getElementById('alamat-suggestions');
    function geocodeAddress(query) {
        // Normalisasi alamat umum: "Jl"/"Jln" -> "Jalan"
        var normalized = query.replace(/^\s*(jl\.?|jln\.?)/i, 'Jalan');

        var bounds = map.getBounds();
        var viewbox = bounds.getWest() + ',' + bounds.getSouth() + ',' + bounds.getEast() + ',' + bounds.getNorth();
        var nominatimBase = 'https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&countrycodes=id&limit=1&accept-language=id';

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

        function applyResultFromNominatim(results) {
            if (results && results.length > 0) {
                var r = results[0];
                var lat = parseFloat(r.lat);
                var lon = parseFloat(r.lon);
                var latlng = { lat: lat, lng: lon };
                // Heuristik: jika query mengandung segmen terakhir (mis. kota/kabupaten)
                // dan tidak ada pada display_name hasil bounded, abaikan hasil ini
                var display = (r.display_name || '').toLowerCase();
                var parts = normalized.toLowerCase().split(',').map(function(s){ return s.trim(); }).filter(Boolean);
                var tail = parts.length > 1 ? parts[parts.length - 1] : '';
                if (tail && display.indexOf(tail) === -1) {
                    return false; // biarkan fallback global mencoba
                }
                marker.setLatLng(latlng);
                if (r.boundingbox) {
                    var bb = r.boundingbox.map(parseFloat);
                    map.fitBounds([[bb[0], bb[2]], [bb[1], bb[3]]]);
                } else {
                    map.setView([lat, lon], Math.max(16, map.getZoom()));
                }
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lon;
                return true;
            }
            return false;
        }

        function renderSuggestionsPhoton(results) {
            suggestionsEl.innerHTML = '';
            if (!results || !results.features || results.features.length === 0) { suggestionsEl.style.display = 'none'; return; }
            results.features.slice(0, 5).forEach(function(f){
                var a = document.createElement('a');
                a.href = '#';
                a.className = 'list-group-item list-group-item-action';
                a.textContent = f.properties && f.properties.name ? (f.properties.name + (f.properties.city ? ', ' + f.properties.city : '') + (f.properties.country ? ', ' + f.properties.country : '')) : (f.properties && f.properties.label ? f.properties.label : 'Hasil');
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

        function applyResultFromPhoton(results) {
            if (results && results.features && results.features.length > 0) {
                var f = results.features[0];
                var lon = f.geometry.coordinates[0];
                var lat = f.geometry.coordinates[1];
                var latlng = { lat: lat, lng: lon };
                marker.setLatLng(latlng);
                if (f.bbox && f.bbox.length === 4) {
                    map.fitBounds([[f.bbox[1], f.bbox[0]], [f.bbox[3], f.bbox[2]]]);
                } else {
                    map.setView([lat, lon], Math.max(16, map.getZoom()));
                }
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lon;
                return true;
            }
            return false;
        }

        return new Promise(function(resolve){
            fetch(nominatimBase + '&bounded=1&viewbox=' + encodeURIComponent(viewbox) + '&q=' + encodeURIComponent(normalized) + '&limit=5', { headers: { 'Accept-Language': 'id' }})
                .then(r => r.json())
                .then(results => {
                    renderSuggestionsNominatim(results);
                    if (applyResultFromNominatim(results)) { resolve(true); return; }
                    return fetch(nominatimBase + '&q=' + encodeURIComponent(normalized) + '&limit=5', { headers: { 'Accept-Language': 'id' }})
                        .then(r => r.json())
                        .then(res2 => {
                            if (!results || results.length === 0) { renderSuggestionsNominatim(res2); }
                            if (applyResultFromNominatim(res2)) { resolve(true); return; }
                            return fetch('https://photon.komoot.io/api/?limit=5&q=' + encodeURIComponent(normalized))
                                .then(r => r.json())
                                .then(function(ph){ renderSuggestionsPhoton(ph); return ph; })
                                .then(function(phRes){ resolve(applyResultFromPhoton(phRes)); })
                                .catch(function(){ resolve(false); });
                        })
                        .catch(function(){ resolve(false); });
                })
                .catch(function(){ resolve(false); });
        });
    }

    alamatInput.addEventListener('input', function() {
        clearTimeout(geocodeTimeout);
        var query = this.value.trim();
        if (!query) { return; }
        geocodeTimeout = setTimeout(function() {
            geocodeAddress(query);
        }, 500);
    });

    alamatInput.addEventListener('change', function() {
        var query = this.value.trim();
        if (query) { geocodeAddress(query); }
    });

    // Final guard: saat submit, pastikan lat/lng sesuai alamat
    var formEl = document.getElementById('form-gudang-create');
    if (formEl) {
        formEl.addEventListener('submit', function(e){
            var q = alamatInput.value.trim();
            if (!q) { return; }
            e.preventDefault();
            geocodeAddress(q).then(function(){
                formEl.submit();
            });
        });
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e){
        if (!suggestionsEl.contains(e.target) && e.target !== alamatInput) {
            suggestionsEl.style.display = 'none';
        }
    });
</script>
@endpush
