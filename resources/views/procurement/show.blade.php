@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Purchase Order Details #PO-{{ $order->Id_Pembelian }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('procurement.pdf', $order->Id_Pembelian) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf me-1"></i> Download PDF
            </a>
            <a href="{{ route('procurement.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    {{-- Order Info --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <p><strong>Supplier:</strong> {{ $order->supplier->Nama_Supplier ?? '-' }}</p>
                    <p><strong>Order Date:</strong> {{ $order->Tanggal_Pemesanan ? $order->Tanggal_Pemesanan->format('d M Y') : '-' }}</p>
                    <p><strong>Arrival Date:</strong> {{ $order->Tanggal_Kedatangan ? $order->Tanggal_Kedatangan->format('d M Y') : '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Payment Method:</strong> {{ $order->Metode_Pembayaran ?? '-' }}</p>
                    <p>
                        <strong>Payment Status:</strong>
                        <span class="badge 
                            @if($order->Status_Pembayaran === 'Pending') bg-warning text-dark
                            @elseif($order->Status_Pembayaran === 'Confirmed') bg-success
                            @else bg-secondary @endif">
                            {{ $order->Status_Pembayaran ?? '-' }}
                        </span>
                    </p>
                    <p>
                        <strong>Receiving Status:</strong>
                        @php
                            $statuses = $order->detailPembelian->pluck('Status_Penerimaan')->unique();
                            $allPending = $statuses->count() === 1 && $statuses->first() === 'Pending';
                            $allReceived = $statuses->count() === 1 && $statuses->first() === 'Diterima';
                            $mixed = $statuses->count() > 1;
                        @endphp
                        <span class="badge 
                            @if($allPending) bg-warning text-dark
                            @elseif($allReceived) bg-success
                            @elseif($mixed) bg-info text-white
                            @else bg-secondary @endif">
                            @if($allPending) ⏳ Pending
                            @elseif($allReceived) ✅ Completed
                            @elseif($mixed) 🔄 Mixed Status
                            @else - @endif
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Items --}}
    <h5 class="fw-bold mb-3">Products / Materials</h5>
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>No</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                    <th>Note</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->detailPembelian as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $detail->barang->Nama_Bahan ?? 'Unknown Item' }}</strong>
                        @if($detail->barang->Satuan)
                            <br><small class="text-muted">Unit: {{ $detail->barang->Satuan }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $detail->Jumlah }}</td>
                    <td class="text-end">Rp {{ number_format($detail->Harga_Keseluruhan / $detail->Jumlah, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($detail->Harga_Keseluruhan, 0, ',', '.') }}</td>
                    <td class="text-muted">{{ $detail->Keterangan ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No item details available.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Cost Summary --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="fw-bold mb-0">Cost Summary</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Items Subtotal:</span>
                        <span class="fw-bold">Rp {{ number_format($order->detailPembelian->sum('Harga_Keseluruhan'), 0, ',', '.') }}</span>
                    </div>
                    @if($order->Biaya_Pengiriman && $order->Biaya_Pengiriman > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping Cost:</span>
                        <span>Rp {{ number_format($order->Biaya_Pengiriman, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span class="fw-bold fs-5">TOTAL:</span>
                        <span class="fw-bold fs-5 text-primary">Rp {{ number_format($order->detailPembelian->sum('Harga_Keseluruhan') + ($order->Biaya_Pengiriman ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Optional Map (if Supplier has location) --}}
    @if($order->supplier && $order->supplier->latitude && $order->supplier->longitude)
        <h5 class="fw-bold mb-3">Supplier Location</h5>
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
