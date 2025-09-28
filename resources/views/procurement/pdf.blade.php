<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order #{{ $order->Id_Pembelian }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 15px;
        }
        
        .info-left {
            width: 50%;
            padding-right: 20px;
        }
        
        .info-right {
            width: 50%;
            padding-left: 20px;
        }
        
        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #666;
        }
        
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        
        .items-table .text-center {
            text-align: center;
        }
        
        .items-table .text-right {
            text-align: right;
        }
        
        .items-table .text-bold {
            font-weight: bold;
        }
        
        .summary-section {
            margin-top: 30px;
            text-align: right;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }
        
        .summary-label {
            font-weight: bold;
        }
        
        .summary-total {
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>PURCHASE ORDER</h1>
        <p>PO #{{ $order->Id_Pembelian }}</p>
        <p>Generated on {{ now()->format('d M Y H:i') }}</p>
    </div>

    {{-- Order Information --}}
    <div class="info-section">
        <div class="info-row">
            <div class="info-left">
                <div class="info-label">FROM:</div>
                <div class="info-value">
                    <strong>{{ $order->supplier->Nama_Supplier ?? 'N/A' }}</strong><br>
                    {{ $order->supplier->Alamat ?? '-' }}<br>
                    {{ $order->supplier->No_Telepon ?? '-' }}
                </div>
            </div>
            <div class="info-right">
                <div class="info-label">ORDER INFO:</div>
                <div class="info-value">
                    <strong>Order Date:</strong> {{ $order->Tanggal_Pemesanan ? $order->Tanggal_Pemesanan->format('d M Y') : '-' }}<br>
                    <strong>Arrival Date:</strong> {{ $order->Tanggal_Kedatangan ? $order->Tanggal_Kedatangan->format('d M Y') : '-' }}<br>
                    <strong>Payment Method:</strong> {{ $order->Metode_Pembayaran ?? '-' }}<br>
                    <strong>Payment Status:</strong> {{ $order->Status_Pembayaran ?? 'Unknown' }}<br>
                    <strong>Receiving Status:</strong> 
                    @php
                        $statuses = $order->detailPembelian->pluck('Status_Penerimaan')->unique();
                        $allPending = $statuses->count() === 1 && $statuses->first() === 'Pending';
                        $allReceived = $statuses->count() === 1 && $statuses->first() === 'Diterima';
                        $mixed = $statuses->count() > 1;
                    @endphp
                    @if($allPending) Pending
                    @elseif($allReceived) Completed
                    @elseif($mixed) Mixed Status
                    @else - @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Item Name</th>
                <th style="width: 100px;">Quantity</th>
                <th style="width: 120px;">Unit Price</th>
                <th style="width: 120px;">Total Price</th>
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
                        <br><small>Unit: {{ $detail->barang->Satuan }}</small>
                    @endif
                </td>
                <td class="text-center">{{ $detail->Jumlah }}</td>
                <td class="text-right">Rp {{ number_format($detail->Harga_Keseluruhan / $detail->Jumlah, 0, ',', '.') }}</td>
                <td class="text-right text-bold">Rp {{ number_format($detail->Harga_Keseluruhan, 0, ',', '.') }}</td>
                <td>{{ $detail->Keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No items found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Cost Summary --}}
    <div class="summary-section">
        <div class="summary-row">
            <span class="summary-label">Items Subtotal:</span>
            <span class="summary-label">Rp {{ number_format($order->detailPembelian->sum('Harga_Keseluruhan'), 0, ',', '.') }}</span>
        </div>
        @if($order->Biaya_Pengiriman && $order->Biaya_Pengiriman > 0)
        <div class="summary-row">
            <span>Shipping Cost:</span>
            <span>Rp {{ number_format($order->Biaya_Pengiriman, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="summary-row summary-total">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($order->detailPembelian->sum('Harga_Keseluruhan') + ($order->Biaya_Pengiriman ?? 0), 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>This document was generated automatically on {{ now()->format('d M Y H:i') }}</p>
        <p>Purchase Order #{{ $order->Id_Pembelian }} | {{ $order->supplier->Nama_Supplier ?? 'N/A' }}</p>
    </div>
</body>
</html>
