<h5>Detail Pembelian</h5>
<table class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>Barang</th>
            <th>Gudang</th>
            <th>Jumlah</th>
            <th>Harga Keseluruhan</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pembelian->detailPembelian as $detail)
            <tr>
                <td>{{ $detail->barang->Nama_Bahan ?? '-' }}</td>
                <td>{{ $detail->gudang->Nama_Gudang ?? '-' }}</td>
                <td>{{ $detail->Jumlah }}</td>
                <td>Rp {{ number_format($detail->Harga_Keseluruhan, 0, ',', '.') }}</td>
                <td>{{ $detail->Keterangan ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Belum ada detail pembelian.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<a href="{{ route('procurement.detail-pembelian.create', $pembelian->Id_Pembelian) }}" class="btn btn-primary">Tambah Item</a>
