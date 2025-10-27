@extends('layouts.sidebar')

@section('content')
<div class="container-fluid py-3">
    <h4 class="fw-bold mb-1">Reports & Analytics</h4>
    <p class="text-muted mb-4">Monitor business performance through comprehensive reports</p>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#inventory" type="button">Inventory</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#production" type="button">Production</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#procurement" type="button">Procurement</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#order" type="button">Orders</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#warehouse" type="button">Warehouse</button></li>
    </ul>

    <div class="tab-content">

        {{-- INVENTORY TAB --}}
        <div class="tab-pane fade show active" id="inventory">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-semibold">Inventory Report</h5>
                <a href="{{ route('reports.export', ['type' => 'inventory']) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h6>Items Below ROP</h6>
                        <h4 class="text-warning">{{ $data['itemsBelowROP'] }}</h4>
                        <small class="text-danger">Critical: {{ $data['criticalItems'] ?? 0 }} items need restock</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h6>Inventory Turns</h6>
                        <h4 class="text-success">{{ $data['inventoryTurns'] ?? 0 }}</h4>
                        <small>Efficiency indicator</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h6>Total Categories</h6>
                        <h4>{{ count($data['categories'] ?? []) }}</h4>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Inventory Value by Category</div>
                <div class="card-body text-center">
                    <div class="row">
                        @foreach ($data['categories'] ?? [] as $cat)
                            <div class="col-md-3 mb-3">
                                <strong>{{ $cat['name'] }}</strong><br>
                                Rp {{ number_format($cat['value'], 0, ',', '.') }}<br>
                                <small>{{ $cat['percentage'] }}% of total</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold">EOQ Analysis Summary</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Material</th>
                                <th>Annual Demand</th>
                                <th>Optimal Order Qty</th>
                                <th>Reorder Point</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['eoqSummary']->take(10) ?? [] as $item)
                                <tr class="text-center">
                                    <td>{{ $item['material'] }}</td>
                                    <td>{{ $item['demand'] }}</td>
                                    <td>{{ $item['qty'] }}</td>
                                    <td>{{ $item['rop'] }}</td>
                                    <td>Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PRODUCTION TAB --}}
        <div class="tab-pane fade" id="production">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-semibold">Production Report</h5>
                <a href="{{ route('reports.export', ['type' => 'production']) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Production Summary</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID Produksi</th>
                                <th>Nama Produksi</th>
                                <th>Tanggal Produksi</th>
                                <th>Jumlah Berhasil</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['productions'] ?? [] as $prod)
                                <tr class="text-center">
                                    <td>{{ $prod->Id_Produksi }}</td>
                                    <td>{{ $prod->Nama_Produksi ?? '-' }}</td>
                                    <td>{{ $prod->Tanggal_Produksi }}</td>
                                    <td>{{ $prod->Jumlah_Berhasil ?? '-' }}</td>
                                    <td>{{ $prod->Status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PROCUREMENT TAB --}}
        <div class="tab-pane fade" id="procurement">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-semibold">Procurement Report</h5>
                <a href="{{ route('reports.export', ['type' => 'procurement']) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Purchase Orders</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID Pembelian</th>
                                <th>Tanggal</th>
                                <th>Total Biaya</th>
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['purchases'] ?? [] as $p)
                                <tr class="text-center">
                                    <td>{{ $p->Id_Pembelian }}</td>
                                    <td>{{ $p->Tanggal_Pemesanan }}</td>
                                    <td>Rp {{ number_format($p->Total_Biaya, 0, ',', '.') }}</td>
                                    <td>{{ $p->Metode_Pembayaran }}</td>
                                    <td>{{ $p->Status_Pembayaran }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ORDER TAB --}}
        <div class="tab-pane fade" id="order">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-semibold">Orders Report</h5>
                <a href="{{ route('reports.export', ['type' => 'orders']) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Customer Orders</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID Order</th>
                                <th>Customer</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['orders'] ?? [] as $order)
                                <tr class="text-center">
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->tanggal_order }}</td>
                                    <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    <td>{{ $order->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- WAREHOUSE TAB --}}
        <div class="tab-pane fade" id="warehouse">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-semibold">Warehouse Report</h5>
                <a href="{{ route('reports.export', ['type' => 'warehouse']) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
            </div>

            <div class="card mb-4">
                <div class="card-header fw-semibold">Warehouse Utilization</div>
                <div class="card-body">
                    <p>Total Capacity: <strong>{{ $data['totalCapacity'] ?? 0 }} m³</strong></p>
                    <p>Used: <strong>{{ $data['usedCapacity'] ?? 0 }} m³ ({{ $data['usageRate'] ?? 0 }}%)</strong></p>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: {{ $data['usageRate'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold">Storage Cost Overview</div>
                <div class="card-body">
                    <p>Total Cost: Rp {{ number_format($data['storageCost'], 0, ',', '.') }}</p>
                    <p>Average per Item: Rp {{ number_format($data['avgCostPerItem'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
