@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Purchase Order Details #{{ $order->Id_Pembelian }}</h2>
        <a href="{{ route('procurement.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Order Info --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Supplier:</strong> {{ $order->supplier->Nama_Supplier ?? '-' }}</p>
            <p><strong>Order Date:</strong> {{ $order->Tanggal_Pemesanan ? $order->Tanggal_Pemesanan->format('d M Y') : '-' }}</p>
            <p><strong>Arrival Date:</strong> {{ $order->Tanggal_Kedatangan ? $order->Tanggal_Kedatangan->format('d M Y') : '-' }}</p>
            <p><strong>Payment Method:</strong> {{ $order->Metode_Pembayaran ?? '-' }}</p>
            <p>
                <strong>Status Payment:</strong>
                <span class="badge 
                    @if($order->Status_Pembayaran === 'Pending') bg-warning text-dark
                    @elseif($order->Status_Pembayaran === 'Confirmed') bg-success text-white
                    @else bg-secondary @endif">
                    {{ $order->Status_Pembayaran ?? '-' }}
                </span>
            </p>
            <p><strong>Total Cost:</strong> Rp {{ number_format($order->Total_Biaya, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Detail Items --}}
    <h5>Products / Materials</h5>
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->detailPembelian as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->Nama_Bahan ?? '-' }}</td>
                    <td>{{ $detail->Jumlah }}</td>
                    <td>Rp {{ number_format($detail->Harga_Keseluruhan, 0, ',', '.') }}</td>
                    <td>{{ $detail->Keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No item details available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Optional Map (if Supplier has location) --}}
    @if($order->supplier && $order->supplier->latitude && $order->supplier->longitude)
    <h5>Supplier Location</h5>
    <div id="map" class="rounded border mb-4" style="height:400px;"></div>
    @endif
</div>
@endsection

@push('scripts')
@if($order->supplier && $order->supplier->latitude && $order->supplier->longitude)
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    var lat = {{ $order->supplier->latitude }};
    var lng = {{ $order->supplier->longitude }};

    var map = L.map('map').setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("<strong>{{ $order->supplier->Nama_Supplier }}</strong><br>{{ $order->supplier->Alamat ?? '-' }}")
        .openPopup();
</script>
@endif
@endpush
