<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-primary">
        <i class="bi bi-gear me-2"></i> Current Productions
    </h5>
</div>

@if(!$current || $current->isEmpty())
    <p class="text-muted">Tidak ada produksi current.</p>
@else
    @foreach($current as $prod)
        <div class="card border-0 shadow-lg mb-4">
            {{-- Header --}}
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    {{ $prod->Nama_Produksi ?? 'Produksi #' . ($prod->Id_Produksi ?? $prod->id) }}
                </h5>
                <span class="badge bg-info px-3 py-2">Current</span>
            </div>

            {{-- Body --}}
            <div class="card-body">
                <p class="mb-1">
                    <i class="bi bi-file-earmark-text me-2"></i> 
                    <strong>SPP:</strong> {{ $prod->pesananProduksi->Nomor_Pesanan ?? '-' }}
                </p>
                <p class="mb-2">
                    <i class="bi bi-calendar-event me-2"></i>
                    <strong>Tanggal:</strong> {{ optional($prod->Tanggal_Produksi)->format('d M Y') ?? '-' }}
                </p>

                @php
                    $pesananDetails = $prod->pesananProduksi->detail ?? collect();
                    $prodId = $prod->Id_Produksi ?? $prod->id;
                @endphp

                @if($pesananDetails->isNotEmpty())
                    <button class="btn btn-outline-primary btn-sm mt-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#bomSummaryCurrent{{ $prodId }}">
                        <i class="bi bi-list-ul me-1"></i> Lihat BOM Summary
                    </button>

                    <div class="collapse mt-3" id="bomSummaryCurrent{{ $prodId }}">
                        <ul class="list-unstyled border-start ps-3">
                            @foreach($prod->details->groupBy('bill_of_material_id') as $bomId => $group)
                                @php
                                    $bom = $group->first()->billOfMaterial ?? null;
                                    $jumlahDipakai = $group->sum('jumlah');
                                @endphp
                                <li class="mb-3">
                                    <h6 class="fw-bold text-primary">{{ $bom->Nama_BOM ?? 'BOM '.$bomId }}</h6>
                                    <div class="small text-muted">
                                        <div>Barang: {{ $group->pluck('barang.Nama_Bahan')->filter()->join(', ') }}</div>
                                        <div>Jumlah per BOM: {{ $jumlahDipakai }}</div>
                                        <div>Total kebutuhan: {{ $jumlahDipakai * (optional($prod->pesananProduksi)->Jumlah_Pesanan ?? 1) }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-muted">SPP ini belum memiliki produk yang valid untuk BOM summary.</p>
                @endif
            </div>

            {{-- Footer --}}
            <div class="card-footer bg-white d-flex justify-content-end gap-1">
                <a href="{{ route('production.show', ['id' => $prod->Id_Produksi, 'tab' => 'current']) }}" class="btn btn-sm btn-info" title="View">
                    <i class="fas fa-eye"></i>
                </a>

                {{-- Move to Completed --}}
                <form action="{{ route('production.moveToCompleted', $prod->Id_Produksi) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" title="Move to Completed">
                        <i class="fas fa-check-double"></i>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@endif
