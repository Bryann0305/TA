@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Production Order Details #{{ $order->pesananProduksi->Nomor_Pesanan }}</h2>
        <a href="{{ route('production_order.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Order Info --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Production Name:</strong> {{ $order->Nama_Produksi }}</p>
            <p><strong>Production Date:</strong> {{ \Carbon\Carbon::parse($order->Tanggal_Produksi)->format('d M Y') }}</p>
            <p>
                <strong>Status:</strong>
                <span class="badge 
                    @if($order->Status=='pending') bg-warning
                    @elseif($order->Status=='confirmed') bg-primary
                    @elseif($order->Status=='in_progress') bg-info
                    @elseif($order->Status=='completed') bg-success
                    @elseif($order->Status=='cancelled') bg-danger
                    @endif
                ">{{ ucfirst($order->Status) }}</span>
            </p>
            <p><strong>Schedule:</strong>
                @if($order->penjadwalan)
                    {{ \Carbon\Carbon::parse($order->penjadwalan->Tanggal_Mulai)->format('d M Y') }} - 
                    {{ \Carbon\Carbon::parse($order->penjadwalan->Tanggal_Selesai)->format('d M Y') }}
                @else
                    -
                @endif
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
                @forelse($order->pesananProduksi->detail as $index => $detail)
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

    {{-- Optional Customer Info / Map --}}
    @if($order->pesananProduksi->pelanggan && $order->pesananProduksi->pelanggan->latitude && $order->pesananProduksi->pelanggan->longitude)
    <h5>Customer Location</h5>
    <div id="map" class="rounded border mb-4" style="height:400px;"></div>
    @endif
</div>
@endsection

@push('scripts')
@if($order->pesananProduksi->pelanggan && $order->pesananProduksi->pelanggan->latitude && $order->pesananProduksi->pelanggan->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var lat = {{ $order->pesananProduksi->pelanggan->latitude }};
    var lng = {{ $order->pesananProduksi->pelanggan->longitude }};

    var map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("<strong>{{ $order->pesananProduksi->pelanggan->Nama_Pelanggan }}</strong><br>{{ $order->pesananProduksi->pelanggan->Alamat }}")
        .openPopup();
</script>
@endif
@endpush
