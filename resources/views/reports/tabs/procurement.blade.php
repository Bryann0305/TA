<div class="card mb-4">
    <div class="card-header">Purchases</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped table-sm align-middle mb-0">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID Pembelian</th>
                    <th>Tanggal</th>
                    <th>Total Biaya</th>
                    <th>Metode Pembayaran</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['purchases'] as $p)
                    <tr class="text-center">
                        <td>{{ $p->Id_Pembelian }}</td>
                        <td>{{ $p->Tanggal_Pemesanan }}</td>
                        <td>{{ $p->Total_Biaya }}</td>
                        <td>{{ $p->Metode_Pembayaran }}</td>
                        <td>{{ $p->Status_Pembayaran }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
