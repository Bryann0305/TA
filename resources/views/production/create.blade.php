@extends('layouts.sidebar')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add Production</h2>
        <a href="{{ route('production.index') }}" class="btn btn-secondary">Back</a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
    @endif

    <form action="{{ route('production.store') }}" method="POST">
        @csrf

        {{-- Pilih Surat Perintah Produksi --}}
        <div class="mb-3">
            <label for="production_order_id" class="form-label">Pilih Surat Perintah Produksi</label>
            <select name="production_order_id" id="production_order_id" class="form-select" required onchange="showOrderDetail(this)">
                <option value="">-- Pilih Surat Perintah Produksi --</option>
                @foreach($orders as $order)
                    @php
                        $barangData = [];
                        if($order->pesananProduksi && $order->pesananProduksi->detail) {
                            $barangData = $order->pesananProduksi->detail->map(function($d){
                                return [
                                    "Id_Bahan" => $d->barang->Id_Bahan ?? null,
                                    "Nama_Bahan" => $d->barang->Nama_Bahan ?? "-", 
                                    "Jumlah" => $d->Jumlah ?? 0,
                                    "Satuan" => $d->barang->Satuan ?? "-"
                                ];
                            })->toArray();
                        }
                    @endphp
                    <option value="{{ $order->id }}" 
                        data-barang='@json($barangData)'
                        data-order-id="{{ $order->id }}"
                        data-production-name="{{ $order->Nama_Produksi ?? 'Production Order #' . $order->id }}">
                        {{ $order->Nama_Produksi ?? 'Production Order #' . $order->id }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Detail Barang Pesanan --}}
        <div id="order-detail" class="mb-3" style="display:none">
            <label for="order-barang-list" class="form-label">Barang yang Dipesan:</label>
            <ul id="order-barang-list" class="list-group list-group-flush"></ul>
        </div>

        {{-- Production Information --}}
        <div id="production-info" class="mb-4" style="display:none">
            <h5>Production Information</h5>
            <div class="card">
                <div class="card-body">
                    <div id="production-details">
                        <!-- Production details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Buat Produksi
        </button>
    </form>
</div>

<script>
let bomIndex = 1;

// Tampilkan detail barang pesanan dan informasi produksi
function showOrderDetail(select) {
    console.log('=== showOrderDetail called ===');
    console.log('showOrderDetail called with select:', select);
    
    const selected = select.options[select.selectedIndex];
    const barangList = document.getElementById('order-barang-list');
    const detailDiv = document.getElementById('order-detail');
    const productionInfo = document.getElementById('production-info');
    const productionDetails = document.getElementById('production-details');
    
    console.log('Selected option:', selected);
    console.log('Dataset barang:', selected.dataset.barang);
    console.log('Order ID:', selected.dataset.orderId);
    console.log('Production Name:', selected.dataset.productionName);
    
    barangList.innerHTML = '';
    productionDetails.innerHTML = '';

    if (selected.dataset.barang) {
        console.log('Raw barang data:', selected.dataset.barang);
        try {
        const details = JSON.parse(selected.dataset.barang);
            console.log('Parsed details:', details);
        if (details.length > 0) {
                // Tampilkan barang yang dipesan
            details.forEach(barang => {
                const li = document.createElement('li');
                li.classList.add('list-group-item', 'py-1');
                    li.innerHTML = `
                        <div class="d-flex justify-content-between align-items-center">
                            <span>${barang.Nama_Bahan} (${barang.Satuan})</span>
                            <span class="badge bg-primary">${barang.Jumlah}</span>
                        </div>
                    `;
                barangList.appendChild(li);
            });
            detailDiv.style.display = 'block';
            
            // Tampilkan informasi produksi dengan BOM dan bahan baku
            let productionHtml = '<h6>Products to be produced:</h6><ul class="list-group list-group-flush mb-3">';
            details.forEach(barang => {
                productionHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>${barang.Nama_Bahan} (${barang.Satuan})</span>
                    <span class="badge bg-primary rounded-pill">${barang.Jumlah}</span>
                </li>`;
            });
            productionHtml += '</ul>';
            
            // Tambahkan informasi BOM dan bahan baku
            productionHtml += '<h6>Required Raw Materials (BOM):</h6>';
            productionHtml += '<div class="table-responsive">';
            productionHtml += '<table id="bom-materials-table" name="bom-materials-table" class="table table-sm table-bordered">';
            productionHtml += '<thead class="table-light">';
            productionHtml += '<tr><th>Material</th><th>BOM Qty/Unit</th><th>Total Qty Needed</th><th>Current Stock</th><th>Unit</th></tr>';
            productionHtml += '</thead><tbody>';
            
            // Tampilkan data BOM dengan stock (akan diisi setelah data BOM dimuat)
            productionHtml += '<tr><td colspan="5" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading BOM and stock data...</td></tr>';
            
            productionHtml += '</tbody></table></div>';
            productionHtml += '<div class="mt-3"><small class="text-muted">BOM will be automatically applied based on the selected products.</small></div>';
            
            productionDetails.innerHTML = productionHtml;
            productionInfo.style.display = 'block';
            
            // Load BOM dan stock data setelah tabel ditampilkan
            loadBomAndStockData(details, selected.dataset.orderId, selected.dataset.productionName);
        } else {
            detailDiv.style.display = 'none';
            productionInfo.style.display = 'none';
        }
        } catch (error) {
            console.error('Error parsing barang data:', error);
            detailDiv.style.display = 'none';
            productionInfo.style.display = 'none';
        }
    } else {
        detailDiv.style.display = 'none';
        productionInfo.style.display = 'none';
    }
}

// Function untuk memuat data BOM dan stock secara bersamaan
function loadBomAndStockData(details, orderId, productionName) {
    console.log('loadBomAndStockData called with details:', details);
    console.log('Order ID:', orderId);
    console.log('Production Name:', productionName);
    
    details.forEach(barang => {
        const productName = barang.Nama_Bahan;
        const quantity = parseInt(barang.Jumlah);
        
        console.log('Loading BOM and stock data for:', productName, 'quantity:', quantity);
        console.log('Product name type:', typeof productName);
        console.log('Product name length:', productName ? productName.length : 'null');
        console.log('Product name exact:', `"${productName}"`);
        
        // Ambil data BOM dan stock secara bersamaan
        console.log('Fetching BOM data for:', productName, 'quantity:', quantity);
        const bomUrl = `{{ route('production.getBomData') }}?product_name=${encodeURIComponent(productName)}&quantity=${quantity}`;
        const stockUrl = `{{ route('production.getStockData') }}?product_name=${encodeURIComponent(productName)}`;
        console.log('BOM URL:', bomUrl);
        console.log('Stock URL:', stockUrl);
        
        // Load BOM data directly from server-side data
        loadBomDataDirectly(productName, quantity);
    });
}

// Function untuk load BOM data langsung dari server-side
function loadBomDataDirectly(productName, quantity) {
    console.log('Loading BOM data directly for:', productName, 'quantity:', quantity);
    
    // Get BOM data from server-side (passed from controller)
    const serverBomData = @json($bomData ?? []);
    console.log('Server BOM data:', serverBomData);
    
    if (serverBomData && serverBomData.length > 0) {
        // Process BOM data for display
        const processedBomData = serverBomData.map(material => ({
            material_id: material.material_id,
            material_name: material.material_name,
            bom_quantity: material.bom_quantity,
            total_quantity: material.bom_quantity * quantity,
            unit: material.unit
        }));
        
        const processedStockData = serverBomData.map(material => ({
            material_id: material.material_id,
            material_name: material.material_name,
            current_stock: material.current_stock,
            unit: material.unit
        }));
        
        console.log('Processed BOM data:', processedBomData);
        console.log('Processed Stock data:', processedStockData);
        
        updateBomTable(processedBomData, processedStockData, quantity);
    } else {
        console.log('No BOM data found for product:', productName);
        const tbody = document.querySelector('tbody');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-warning">
                <i class="fas fa-exclamation-triangle"></i> BOM not found for product: ${productName}
                <br><small>Please create BOM first in Bill of Materials section</small>
                <br><a href="{{ route('bom.create') }}" class="btn btn-sm btn-primary mt-2">Create BOM</a>
            </td></tr>`;
        }
    }
}

// Function untuk mengupdate tabel BOM dengan data yang sebenarnya
function updateBomTable(bomMaterials, stockMaterials, quantity) {
    console.log('updateBomTable called with BOM materials:', bomMaterials);
    console.log('updateBomTable called with Stock materials:', stockMaterials);
    
    const tbody = document.querySelector('tbody');
    if (!tbody) {
        console.error('tbody element not found');
        return;
    }
    
    // Check if we have valid data
    if (!bomMaterials || bomMaterials.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-warning"><i class="fas fa-info-circle"></i> No BOM materials found</td></tr>';
        return;
    }
    
    // Clear loading message
    tbody.innerHTML = '';
    
    // Tampilkan data BOM dengan stock
    bomMaterials.forEach((material, index) => {
        // Cari stock data yang sesuai
        const stockInfo = stockMaterials.find(stock => stock.material_name === material.material_name);
        const currentStock = stockInfo ? stockInfo.current_stock : 0;
        const stockClass = currentStock > 0 ? 'text-success' : 'text-danger';
        const stockIcon = currentStock > 0 ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        tbody.innerHTML += `
            <tr data-material-id="${material.material_id}" data-material-name="${material.material_name}">
                <td>${material.material_name}</td>
                <td class="text-center">${material.bom_quantity}</td>
                <td class="text-center"><strong>${material.total_quantity}</strong></td>
        <td class="text-center">
                    <span class="${stockClass}">
                        <i class="fas ${stockIcon}"></i> <strong>${currentStock}</strong>
                    </span>
        </td>
                <td class="text-center">${material.unit}</td>
            </tr>
        `;
    });
    
    // Add summary row
    const totalMaterials = bomMaterials.length;
    const availableStock = bomMaterials.filter(material => {
        const stockInfo = stockMaterials.find(stock => stock.material_name === material.material_name);
        return stockInfo && stockInfo.current_stock > 0;
    }).length;
    
    tbody.innerHTML += `
        <tr class="table-info">
            <td colspan="3" class="text-end"><strong>Summary:</strong></td>
            <td class="text-center">
                <span class="badge ${availableStock === totalMaterials ? 'bg-success' : 'bg-warning'}">
                    ${availableStock}/${totalMaterials} materials available
                </span>
            </td>
            <td></td>
        </tr>
    `;
}

// BOM data is now displayed directly without fetch

// Production form is now simplified - no additional JavaScript needed
</script>
@endsection
