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
                        <h5>{{ $inventoryCount }}</h5>
                    </div>
                    <div class="card-icon">📦</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 text-muted">Production Output</p>
                        <h5>{{ number_format($productionCount) }}</h5>
                    </div>
                    <div class="card-icon">📈</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 text-muted">Procurement Cost</p>
                        <h5>Rp {{ number_format($procurementCost, 0, ',', '.') }}</h5>
                    </div>
                    <div class="card-icon">💵</div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="mb-0 text-muted">Pending Orders</p>
                        <h5>{{ $pendingOrders }}</h5>
                    </div>
                    <div class="card-icon">🚚</div>
                </div>
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
                @forelse($reorderAlerts as $alert)
                    <tr>
                        <td class="{{ $alert['Status']=='Critical Low' ? 'text-danger' : ($alert['Status']=='Near Reorder Point' ? 'text-warning' : '') }}">
                            {{ $alert['Nama_Bahan'] }}
                        </td>
                        <td>{{ $alert['Stok'] }}</td>
                        <td>{{ $alert['Reorder_Point'] }}</td>
                        <td>{{ $alert['EOQ'] }}</td>
                        <td>
                            <span class="badge 
                                {{ $alert['Status']=='Critical Low' ? 'bg-danger' : ($alert['Status']=='Near Reorder Point' ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ $alert['Status'] }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No alerts</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
