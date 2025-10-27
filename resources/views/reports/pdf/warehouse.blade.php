<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Gudang</h2>

    <table>
        <thead>
            <tr>
                <th>Total Kapasitas</th>
                <th>Kapasitas Terpakai</th>
                <th>Persentase Penggunaan</th>
                <th>Total Biaya Penyimpanan</th>
                <th>Rata-rata Biaya per Barang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $data['totalCapacity'] ?? 0 }}</td>
                <td>{{ $data['usedCapacity'] ?? 0 }}</td>
                <td>{{ $data['usageRate'] ?? 0 }}%</td>
                <td>Rp {{ number_format($data['storageCost'] ?? 0, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($data['avgCostPerItem'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
