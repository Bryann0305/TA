<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produksi</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Produksi</h2>

    <table>
        <thead>
            <tr>
                <th>ID Produksi</th>
                <th>Nama Produksi</th>
                <th>Tanggal Produksi</th>
                <th>Jumlah Berhasil</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['productions'] as $prod)
                <tr>
                    <td>{{ $prod->Id_Produksi }}</td>
                    <td>{{ $prod->Nama_Produksi }}</td>
                    <td>{{ $prod->Tanggal_Produksi }}</td>
                    <td>{{ $prod->Jumlah_Berhasil }}</td>
                    <td>{{ $prod->Status }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Tidak ada data produksi</td></tr>
            @endforelse
        </tbody>
    </table>

    <p><strong>Total Produksi:</strong> {{ $data['totalProductions'] }}</p>
    <p><strong>Produksi Selesai:</strong> {{ $data['completedProductions'] }}</p>
</body>
</html>
