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
        <div class="row">
            @foreach ($data['categories'] as $cat)
                <div class="col-md-3 mb-2">
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
    <div class="card-body table-responsive">
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
                @foreach ($data['eoqSummary'] as $item)
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
