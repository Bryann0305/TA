<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengadaan (Procurement)</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Pengadaan Barang</h2>

    <table>
        <thead>
            <tr>
                <th>ID Pembelian</th>
                <th>Tanggal Pembelian</th>
                <th>Total Biaya</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['purchases'] as $purchase)
                <tr>
                    <td>{{ $purchase->Id_Pembelian ?? '-' }}</td>
                    <td>{{ $purchase->Tanggal_Pembelian ?? '-' }}</td>
                    <td>Rp {{ number_format($purchase->Total_Biaya ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Tidak ada data pembelian</td></tr>
            @endforelse
        </tbody>
    </table>

    <p><strong>Total Pengadaan:</strong> Rp {{ number_format($data['totalPurchases'] ?? 0, 0, ',', '.') }}</p>
    <p><strong>Inventory Turns:</strong> {{ $data['inventoryTurns'] ?? 0 }}</p>
</body>
</html>
