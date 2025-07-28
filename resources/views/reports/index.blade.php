@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h4 class="fw-bold">Reports & Analytics</h4>
    <p class="text-muted">Generate and view reports for different business areas</p>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#">Inventory Reports</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Production Reports</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Procurement Reports</a>
        </li>
    </ul>

    {{-- Top Stats --}}@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h4 class="fw-bold">Reports & Analytics</h4>
    <p class="text-muted">Generate and view reports for different business areas</p>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" href="#">Inventory Reports</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Production Reports</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Procurement Reports</a>
        </li>
    </ul>

    {{-- Top Stats --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Stock Value</h6>
                    <h4 class="text-primary">${{ number_format($data['stockValue']) }}</h4>
                    <small class="text-success">▲ {{ $data['inventoryGrowth'] }}% from last month</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Items Below ROP</h6>
                    <h4 class="text-warning">{{ $data['itemsBelowROP'] }}</h4>
                    <small class="text-danger">Critical: {{ $data['criticalItems'] }} items need immediate order</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Inventory Turns</h6>
                    <h4 class="text-success">{{ $data['inventoryTurns'] }}</h4>
                    <small class="text-success">▲ 0.5 from last quarter</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Inventory Category --}}
    <div class="card mb-4">
        <div class="card-header">Inventory Value by Category</div>
        <div class="card-body text-center">
            <img src="https://images.pexels.com/photos/6801648/pexels-photo-6801648.jpeg?auto=compress&amp;cs=tinysrgb&amp;w=1260&amp;h=750&amp;dpr=1" alt="Inventory value chart" class="img-fluid mb-3 rounded-3 shadow-sm" style="width: 20%; height: 20%;">
            <div class="row">
                @foreach ($data['categories'] as $cat)
                    <div class="col-md-3">
                        <strong>{{ $cat['name'] }}</strong><br>
                        ${{ number_format($cat['value']) }}<br>
                        <small>{{ $cat['percentage'] }}% of total</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- EOQ Table --}}
    <div class="card">
        <div class="card-header">EOQ Analysis Summary</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Annual Demand</th>
                        <th>Optimal Order Qty</th>
                        <th>Reorder Point</th>
                        <th>Annual Holding Cost</th>
                        <th>Annual Order Cost</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['eoqSummary'] as $item)
                    <tr>
                        <td>{{ $item['material'] }}</td>
                        <td>{{ $item['demand'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ $item['rop'] }}</td>
                        <td>{{ $item['holding'] }}</td>
                        <td>{{ $item['orderCost'] }}</td>
                        <td>{{ $item['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Stock Value</h6>
                    <h4 class="text-primary">${{ number_format($data['stockValue']) }}</h4>
                    <small class="text-success">▲ {{ $data['inventoryGrowth'] }}% from last month</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Items Below ROP</h6>
                    <h4 class="text-warning">{{ $data['itemsBelowROP'] }}</h4>
                    <small class="text-danger">Critical: {{ $data['criticalItems'] }} items need immediate order</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Inventory Turns</h6>
                    <h4 class="text-success">{{ $data['inventoryTurns'] }}</h4>
                    <small class="text-success">▲ 0.5 from last quarter</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Inventory Category --}}
    <div class="card mb-4">
        <div class="card-header">Inventory Value by Category</div>
        <div class="card-body text-center">
            <img src="https://images.pexels.com/photos/6801648/pexels-photo-6801648.jpeg?auto=compress&amp;cs=tinysrgb&amp;w=1260&amp;h=750&amp;dpr=1" alt="Inventory value chart" class="img-fluid mb-3 rounded-3 shadow-sm" style="width: 20%; height: 20%;">
            <div class="row">
                @foreach ($data['categories'] as $cat)
                    <div class="col-md-3">
                        <strong>{{ $cat['name'] }}</strong><br>
                        ${{ number_format($cat['value']) }}<br>
                        <small>{{ $cat['percentage'] }}% of total</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- EOQ Table --}}
    <div class="card">
        <div class="card-header">EOQ Analysis Summary</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Annual Demand</th>
                        <th>Optimal Order Qty</th>
                        <th>Reorder Point</th>
                        <th>Annual Holding Cost</th>
                        <th>Annual Order Cost</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['eoqSummary'] as $item)
                    <tr>
                        <td>{{ $item['material'] }}</td>
                        <td>{{ $item['demand'] }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>{{ $item['rop'] }}</td>
                        <td>{{ $item['holding'] }}</td>
                        <td>{{ $item['orderCost'] }}</td>
                        <td>{{ $item['total'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

