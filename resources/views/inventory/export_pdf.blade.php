<!DOCTYPE html>
<html>
<head>
    <title>Inventory Export PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Inventory Export</h2>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->Nama_Bahan }}</td>
                <td>{{ $item->Id_Bahan }}</td>
                <td>{{ $item->kategori->Nama_Kategori ?? 'Unknown' }}</td>
                <td>{{ $item->Stok }}</td>
                <td>{{ $item->Status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
