@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h4 class="fw-bold mb-1">Reports & Analytics</h4>
    <p class="text-muted mb-4">Generate and view reports for different business areas</p>

    {{-- Navigation Tabs --}}
    <ul class="nav nav-tabs mb-3" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">Inventory Reports</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="production-tab" data-bs-toggle="tab" data-bs-target="#production" type="button" role="tab">Production Reports</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="procurement-tab" data-bs-toggle="tab" data-bs-target="#procurement" type="button" role="tab">Procurement Reports</button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Inventory Tab --}}
        <div class="tab-pane fade show active" id="inventory" role="tabpanel">
            {{-- Top Stats --}}
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h6>Stock Value</h6>
                        <h4 class="text-primary">Rp {{ number_format($data['stockValue'], 0, ',', '.') }}</h4>
                        <small class="text-success">▲ {{ $data['inventoryGrowth'] ?? 0 }}% from last month</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h6>Items Below ROP</h6>
                        <h4 class="text-warning">{{ $data['itemsBelowROP'] }}</h4>
                        <small class="text-danger">Critical: {{ $data['criticalItems'] }} items need immediate order</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm p-3">
                        <h6>Inventory Turns</h6>
                        <h4 class="text-success">{{ $data['inventoryTurns'] }}</h4>
                        <small class="text-success">▲ 0.5 from last quarter</small>
                    </div>
                </div>
            </div>

            {{-- Inventory Category --}}
            <div class="card mb-4">
                <div class="card-header">Inventory Value by Category</div>
                <div class="card-body text-center">
                    <div class="row justify-content-center">
                        @foreach ($data['categories'] as $cat)
                            <div class="col-md-3 mb-2 text-center">
                                <strong>{{ $cat['name'] }}</strong><br>
                                Rp {{ number_format($cat['value'], 0, ',', '.') }}<br>
                                <small>{{ $cat['percentage'] }}% of total</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- EOQ Table --}}
            <div class="card mb-4">
                <div class="card-header">EOQ Analysis Summary</div>
                <div class="card-body table-responsive" style="max-height:400px; overflow:auto;">
                    <table class="table table-bordered table-striped table-sm align-middle mb-0">
                        <thead class="table-dark text-center">
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
                            @foreach ($data['eoqSummary']->take(10) as $item)
                                <tr class="text-center">
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

        {{-- Production Tab --}}
        <div class="tab-pane fade" id="production" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header">Production Orders</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped table-sm align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>ID Produksi</th>
                                <th>Nama Produksi</th>
                                <th>Tanggal Produksi</th>
                                <th>Hasil</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['productions'] as $prod)
                                <tr class="text-center">
                                    <td>{{ $prod->Id_Produksi }}</td>
                                    <td>{{ $prod->Nama_Produksi ?? '-' }}</td>
                                    <td>{{ $prod->Tanggal_Produksi }}</td>
                                    <td>{{ $prod->Jumlah_Berhasil ?? $prod->Hasil_Produksi }}</td>
                                    <td>{{ $prod->Status }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>BOM</th>
                                                    <th>Bahan</th>
                                                    <th>Jumlah</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(isset($data['productionDetails'][$prod->Id_Produksi]))
                                                    @foreach($data['productionDetails'][$prod->Id_Produksi] as $detail)
                                                        <tr class="text-center">
                                                            <td>{{ str_replace('BOM - ', '', $detail->Nama_bill_of_material) }}</td>
                                                            <td>{{ $detail->Nama_Bahan }}</td>
                                                            <td>{{ $detail->jumlah }}</td>
                                                            <td>{{ $detail->status }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr><td colspan="4">No details</td></tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Procurement Tab --}}
        <div class="tab-pane fade" id="procurement" role="tabpanel">
            <div class="card mb-4">
                <div class="card-header">Purchases</div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped table-sm align-middle mb-0">
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
                            @foreach($data['purchases'] as $p)
                                <tr class="text-center">
                                    <td>{{ $p->Id_Pembelian }}</td>
                                    <td>{{ $p->Tanggal_Pemesanan }}</td>
                                    <td>{{ $p->Total_Biaya }}</td>
                                    <td>{{ $p->Metode_Pembayaran }}</td>
                                    <td>{{ $p->Status_Pembayaran }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
