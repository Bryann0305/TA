@extends('layouts.sidebar')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Dashboard</h4>
        <small class="text-muted">Welcome back, {{ Auth::user()->Nama }} 👋</small>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-3 bg-light">
            <div class="d-flex align-items-center">
                <div class="me-3 text-primary"><i class="fas fa-boxes fa-2x"></i></div>
                <div>
                    <p class="mb-0 text-muted">Total Inventory</p>
                    <h5 class="fw-bold mb-0">{{ $totalBarang ?? 0 }}</h5>
                    <small>All SKUs</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-3 bg-light">
            <div class="d-flex align-items-center">
                <div class="me-3 text-success"><i class="fas fa-leaf fa-2x"></i></div>
                <div>
                    <p class="mb-0 text-muted">Raw Materials</p>
                    <h5 class="fw-bold mb-0">{{ $bahanBakuCount ?? 0 }}</h5>
                    <small>Items available</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-3 bg-light">
            <div class="d-flex align-items-center">
                <div class="me-3 text-warning"><i class="fas fa-cubes fa-2x"></i></div>
                <div>
                    <p class="mb-0 text-muted">Finished Products</p>
                    <h5 class="fw-bold mb-0">{{ $produkJadiCount ?? 0 }}</h5>
                    <small>Ready to ship</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 p-3 bg-light">
            <div class="d-flex align-items-center">
                <div class="me-3 text-danger"><i class="fas fa-industry fa-2x"></i></div>
                <div>
                    <p class="mb-0 text-muted">Produced (This Month)</p>
                    <h5 class="fw-bold mb-0">{{ $producedThisMonth ?? 0 }}</h5>
                    <small>Units completed</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Left: Main Analytics -->
    <div class="col-lg-8">
        <!-- Production Cost Overview -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">High Cost Materials</h6>
                    <span class="badge bg-danger">HPP Priority</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Material</th>
                                <th>HPP (Rp)</th>
                                <th>Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topHpp as $item)
                                <tr>
                                    <td>{{ $item->Nama_Bahan }}</td>
                                    <td>Rp {{ number_format($item->HPP ?? 0, 2, ',', '.') }}</td>
                                    <td>{{ $item->Stok ?? 0 }}</td>
                                    <td>
                                        @if(($item->Stok ?? 0) < ($item->Safety_Stock ?? 0))
                                            <span class="badge bg-danger">Low</span>
                                        @else
                                            <span class="badge bg-success">OK</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No HPP data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Procurement -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Recent Procurements</h6>
<a href="{{ route('procurement.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
            <i class="fas fa-eye me-1"></i> View All
        </a>                 </div>
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Total (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPurchases as $p)
                                <tr>
                                    <td>{{ $p->Id_Pembelian }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->Tanggal_Pemesanan)->format('d M Y') }}</td>
                                    <td>{{ $p->Nama_Bahan }}</td>
                                    <td>{{ $p->Jumlah }}</td>
                                    <td>Rp {{ number_format($p->Harga_Keseluruhan, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No recent procurement data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Notifications & Insights -->
    <div class="col-lg-4">
        <!-- Low Stock Alerts -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Low Stock Alerts</h6>
                <ul class="list-group list-group-flush">
                    @forelse($lowStock as $s)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $s->Nama_Bahan }}</strong>
                                <div class="small text-muted">Safety: {{ $s->Safety_Stock ?? 0 }}</div>
                            </div>
                            <span class="badge bg-danger">{{ $s->Stok }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No low-stock items 🎉</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Quick Summary -->
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Today’s Summary</h6>
                <ul class="list-unstyled mb-0">
                    <li><i class="fas fa-box text-primary me-2"></i> Total Materials: <strong>{{ $bahanBakuCount ?? 0 }}</strong></li>
                    <li><i class="fas fa-cubes text-warning me-2"></i> Finished Products: <strong>{{ $produkJadiCount ?? 0 }}</strong></li>
                    <li><i class="fas fa-industry text-success me-2"></i> Produced this month: <strong>{{ $producedThisMonth ?? 0 }}</strong></li>
                    <li><i class="fas fa-exclamation-circle text-danger me-2"></i> Low stock items: <strong>{{ count($lowStock ?? []) }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
