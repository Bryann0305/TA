<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pesanan Produksi</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Pesanan Produksi</h2>

    <table>
        <thead>
            <tr>
                <th>ID Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal Pesanan</th>
                <th>Jumlah Pesanan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['orders'] as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->customer_name ?? '-' }}</td>
                    <td>{{ $order->tanggal_order }}</td>
                    <td>{{ $order->total }}</td>
                    <td>{{ $order->status }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Tidak ada data pesanan</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
