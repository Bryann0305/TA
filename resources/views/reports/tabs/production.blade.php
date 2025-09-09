@foreach($data['productions'] as $prod)
<tr class="text-center">
    <td>{{ $prod->Id_Produksi }}</td>
    <td>{{ $prod->Nama_Produksi ?? '-' }}</td>
    <td>{{ $prod->Tanggal_Produksi }}</td>
    <td>{{ $prod->Jumlah_Berhasil }}</td>
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
                            <td>{{ $detail->Nama_bill_of_material }}</td>
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
