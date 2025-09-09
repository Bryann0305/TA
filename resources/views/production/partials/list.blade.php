@forelse($produksi as $p)
    <div class="card mb-3">
        <div class="card-body">
            <h5>{{ $p->productionOrder->Nama_Produksi ?? 'Produksi #' . $p->Id_Produksi }}</h5>
            <p>Status: 
                <span class="badge bg-{{ $p->Status == 'completed' ? 'success' : ($p->Status == 'planned' ? 'warning' : 'primary') }}">
                    {{ ucfirst($p->Status) }}
                </span>
            </p>

            {{-- Progress Bar --}}
            @php
                $target = $p->productionOrder->pesananProduksi->Jumlah_Pesanan ?? 0;
                $progress = $target > 0 ? ($p->Jumlah_Berhasil / $target) * 100 : 0;
            @endphp
            <div class="progress mb-2">
                <div class="progress-bar" role="progressbar"
                    style="width: {{ $progress }}%"
                    aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                    {{ number_format($progress, 0) }}%
                </div>
            </div>

            <p>Jumlah Berhasil: {{ $p->Jumlah_Berhasil }} | Jumlah Gagal: {{ $p->Jumlah_Gagal }}</p>

            {{-- BOM & Barang dari produksi_detail --}}
            @if($p->details->count() > 0)
                <h6>BOM & Items:</h6>
                <ul>
                    @foreach($p->details as $detail)
                        <li>
                            {{ $detail->billOfMaterial->Nama_bill_of_material ?? '-' }} - 
                            {{ $detail->barang->Nama_Bahan ?? '-' }} ({{ $detail->jumlah }})
                        </li>
                    @endforeach
                </ul>
            @endif

            <a href="{{ route('production.show', $p->Id_Produksi) }}" class="btn btn-sm btn-info">Detail</a>
        </div>
    </div>
@empty
    <p class="text-muted">Tidak ada data produksi.</p>
@endforelse
