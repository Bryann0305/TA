@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Production Order Details #{{ $pesanan->Nomor_Pesanan }}</h2>
        <a href="{{ route('pesanan_produksi.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Order Info --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>User:</strong> Admin</p> {{-- User always admin --}}
            <p><strong>Customer:</strong> {{ $pesanan->pelanggan->Nama_Pelanggan ?? '-' }}</p>
            <p><strong>Order Date:</strong> {{ $pesanan->Tanggal_Pesanan ? $pesanan->Tanggal_Pesanan->format('d M Y') : '-' }}</p>
            <p>
                <strong>Status:</strong>
                <span class="badge 
                    @if($pesanan->Status=='pending') bg-warning
                    @elseif($pesanan->Status=='confirmed') bg-primary
                    @elseif($pesanan->Status=='in_progress') bg-info
                    @elseif($pesanan->Status=='completed') bg-success
                    @elseif($pesanan->Status=='cancelled') bg-danger
                    @endif
                ">{{ ucfirst($pesanan->Status) }}</span>
            </p>
        </div>
    </div>

    {{-- Detail Products / Materials --}}
    <h5>Products / Materials</h5>
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesanan->detail as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->Nama_Bahan ?? '-' }}</td>
                    <td>{{ $detail->Jumlah }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No product details available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Optional Map --}}
    @if($pesanan->pelanggan && $pesanan->pelanggan->latitude && $pesanan->pelanggan->longitude)
    <h5>Customer Location</h5>
    <div id="map" class="rounded border mb-4" style="height:400px;"></div>
    @endif

</div>
@endsection

@push('scripts')
@if($pesanan->pelanggan && $pesanan->pelanggan->latitude && $pesanan->pelanggan->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var lat = {{ $pesanan->pelanggan->latitude }};
    var lng = {{ $pesanan->pelanggan->longitude }};

    var map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("<strong>{{ $pesanan->pelanggan->Nama_Pelanggan }}</strong><br>{{ $pesanan->pelanggan->Alamat }}")
        .openPopup();
</script>
@endif
@endpush
