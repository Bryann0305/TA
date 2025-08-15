@forelse($produksi as $order)
    <div class="card mb-3">
        <div class="card-body">
            {{-- Judul Produksi --}}
            <h5 class="card-title">Produksi: {{ $order->Hasil_Produksi ?? '-' }}</h5>

            {{-- Tanggal & ID Produksi --}}
            <p class="card-text mb-1">
                <i class="bi bi-calendar"></i>
                {{ $order->Tanggal_Produksi ? \Carbon\Carbon::parse($order->Tanggal_Produksi)->format('d M Y') : '-' }}
                &nbsp; • &nbsp; Batch ID: {{ $order->Id_Produksi }}
            </p>

            {{-- Status Produksi --}}
            <p class="card-text mb-1 text-muted">
                <i class="bi bi-diagram-3"></i> Status: {{ ucfirst($order->Status) }}
            </p>

            {{-- Detail Pesanan Produksi --}}
            @if($order->pesananProduksi)
                <p class="card-text mb-1">
                    <strong>Pesanan:</strong>
                    {{ $order->pesananProduksi->Jumlah_Pesanan }} unit -
                    {{ $order->pesananProduksi->Tanggal_Pesanan
                        ? \Carbon\Carbon::parse($order->pesananProduksi->Tanggal_Pesanan)->format('d M Y')
                        : '-' }}
                </p>
            @endif

            {{-- Detail Jadwal Produksi --}}
            @if($order->jadwal)
                <p class="card-text mb-1">
                    <strong>Jadwal:</strong>
                    {{ $order->jadwal->Tanggal_Mulai ? \Carbon\Carbon::parse($order->jadwal->Tanggal_Mulai)->format('d M') : '-' }}
                    -
                    {{ $order->jadwal->Tanggal_Selesai ? \Carbon\Carbon::parse($order->jadwal->Tanggal_Selesai)->format('d M Y') : '-' }}
                    ({{ $order->jadwal->Status ?? '-' }})
                </p>
            @endif

            {{-- Detail BOM --}}
            @if($order->bom)
                <div class="mt-3">
                    <strong>BOM: {{ $order->bom->Nama_bill_of_material ?? 'BOM #' . $order->bom->Id_bill_of_material }}</strong>
                    <p class="text-muted mb-1">
                        Status:
                        <span class="badge bg-{{ 
                            $order->bom->Status == 'approved' ? 'success' : 
                            ($order->bom->Status == 'draft' ? 'warning' : 'danger') 
                        }}">
                            {{ ucfirst($order->bom->Status ?? '-') }}
                        </span>
                    </p>

                    @if($order->bom->barang->isEmpty())
                        <p class="text-muted">Tidak ada bahan baku dalam BOM.</p>
                    @else
                        <ul class="list-group list-group-sm list-group-flush">
                            @foreach($order->bom->barang as $barang)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $barang->Nama_Bahan }} ({{ $barang->Jenis }})
                                    <span class="badge bg-primary rounded-pill">{{ $barang->pivot->Jumlah ?? 0 }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            {{-- Badge Status --}}
            <span class="badge bg-light text-primary border border-primary mt-2">{{ ucfirst($order->Status) }}</span>

            {{-- Progress Bar --}}
            @php
                $jumlahBerhasil = $order->Jumlah_Berhasil ?? 0;
                $jumlahGagal = $order->Jumlah_Gagal ?? 0;
                $totalProduksi = $jumlahBerhasil + $jumlahGagal;
                $progress = $totalProduksi > 0 ? ($jumlahBerhasil / $totalProduksi) * 100 : 0;

                $barColor = 'bg-primary';
                if ($progress < 30) $barColor = 'bg-danger';
                elseif ($progress < 70) $barColor = 'bg-warning';
            @endphp

            <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar {{ $barColor }}" role="progressbar"
                     style="width: {{ $progress }}%; transition: width 0.6s ease;"
                     aria-valuenow="{{ $jumlahBerhasil }}"
                     aria-valuemin="0"
                     aria-valuemax="{{ $totalProduksi }}">
                </div>
            </div>
            <small class="text-muted">{{ round($progress) }}% Complete</small>

            {{-- Tombol Aksi dengan Dropdown --}}
            <div class="dropdown mt-3">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    Aksi
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('production.edit', $order->Id_Produksi) }}">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('production.show', $order->Id_Produksi) }}">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                    </li>
                    @if($order->Status !== 'Selesai')
                        <li>
                            <form method="POST" action="{{ route('production.update-status', $order->Id_Produksi) }}" class="px-3">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="Status" value="Selesai">
                                <button type="submit" class="dropdown-item text-success p-0 mt-2">
                                    <i class="bi bi-check-circle"></i> Mark Complete
                                </button>
                            </form>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@empty
    <p class="text-muted">Tidak ada data produksi.</p>
@endforelse
