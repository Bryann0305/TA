@extends('layouts.sidebar')

@section('content')
    <h4>Dashboard</h4>
    <p>Welcome back, {{ Auth::user()->Nama }}!</p>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 text-muted">Inventory Items</p>
                        <h5>487</h5>
                        <small class="text-success">↑ 12% since last month</small>
                    </div>
                    <div class="card-icon">
                        📦
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 text-muted">Production Output</p>
                        <h5>2,347</h5>
                        <small class="text-success">↑ 8% since last month</small>
                    </div>
                    <div class="card-icon">
                        📈
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 text-muted">Procurement Cost</p>
                        <h5>$28,459</h5>
                        <small class="text-danger">↓ 3% since last month</small>
                    </div>
                    <div class="card-icon">
                        💵
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 text-muted">Pending Orders</p>
                        <h5>24</h5>
                        <small class="text-success">↑ 5% since last week</small>
                    </div>
                    <div class="card-icon">
                        🚚
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h6 class="mb-3">Production Trend</h6>
                <canvas id="productionChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h6 class="mb-3">Inventory Levels</h6>
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Reorder Alerts -->
    <div class="card p-3 shadow-sm">
        <h6 class="mb-3">Reorder Alerts</h6>
        <table class="table table-bordered table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th>Material</th>
                    <th>Current Stock</th>
                    <th>Reorder Point</th>
                    <th>EOQ</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-warning">⚠️ Raw Epoxy Resin</td>
                    <td>128 kg</td>
                    <td>150 kg</td>
                    <td>500 kg</td>
                    <td><span class="badge bg-warning text-dark">Near Reorder Point</span></td>
                </tr>
                <tr>
                    <td class="text-danger">❗ Titanium Dioxide</td>
                    <td>45 kg</td>
                    <td>100 kg</td>
                    <td>350 kg</td>
                    <td><span class="badge bg-danger">Below Reorder Point</span></td>
                </tr>
                <tr>
                    <td class="text-danger">❗ Solvent Additive</td>
                    <td>25 L</td>
                    <td>50 L</td>
                    <td>200 L</td>
                    <td><span class="badge bg-danger">Below Reorder Point</span></td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection
