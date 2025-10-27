<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
    <style>
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #555; padding: 5px; text-align: center; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h3>Inventory Report</h3>
    <table>
        <thead>
            <tr>
                <th>Material</th>
                <th>Annual Demand</th>
                <th>EOQ</th>
                <th>ROP</th>
                <th>Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['eoqSummary'] as $item)
            <tr>
                <td>{{ $item['material'] }}</td>
                <td>{{ $item['demand'] }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>{{ $item['rop'] }}</td>
                <td>Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
