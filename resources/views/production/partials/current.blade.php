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

                {{-- Info Produksi Ulang --}}
                @if(str_contains($prod->Hasil_Produksi ?? '', 'Produksi Ulang'))
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Produksi Ulang:</strong> {{ $prod->Hasil_Produksi }}
                        <br><small class="text-muted">Produksi ini dibuat untuk mengulang bagian yang gagal dari produksi sebelumnya.</small>
                        <br><strong>Jumlah Dipesan:</strong> {{ $prod->pesananProduksi->Jumlah_Pesanan ?? 0 }} unit (sudah dikurangi dengan yang berhasil)
                    </div>
                @endif

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
                                    $rawMaterials = $bom ? $bom->barangs : collect();
                                    $jumlahBahanBaku = $rawMaterials->count(); // Jumlah bahan baku yang berbeda dari BOM
                                    
                                    // Untuk produksi ulang, gunakan jumlah yang gagal (dari detail produksi)
                                    if(str_contains($prod->Hasil_Produksi ?? '', 'Produksi Ulang')) {
                                        $quantityPesanan = $group->sum('jumlah'); // Jumlah yang gagal dari detail
                                    } else {
                                        $quantityPesanan = optional($prod->pesananProduksi)->Jumlah_Pesanan ?? 1;
                                    }
                                @endphp
                                <li class="mb-3">
                                    <h6 class="fw-bold text-primary">{{ $bom->Nama_bill_of_material ?? 'BOM '.$bomId }}</h6>
                                    <div class="small text-muted">
                                        <div><strong>Produk:</strong> {{ $group->pluck('barang.Nama_Bahan')->filter()->join(', ') }}</div>
                                        <div><strong>Jumlah per BOM:</strong> {{ $jumlahBahanBaku }} bahan baku</div>
                                        <div><strong>Total kebutuhan:</strong> {{ $jumlahBahanBaku * $quantityPesanan }} unit</div>
                                        <div class="mt-2">
                                            <strong>Detail Bahan Baku:</strong>
                                            <ul class="list-unstyled ms-3">
                                                @foreach($rawMaterials as $material)
                                                    <li>• {{ $material->Nama_Bahan }} ({{ $material->pivot->Jumlah_Bahan * $quantityPesanan }} {{ $material->Satuan }})</li>
                                                @endforeach
                                            </ul>
                                        </div>
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
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                {{-- Info Produksi Ulang --}}
                @if(str_contains($prod->Hasil_Produksi ?? '', 'Produksi Ulang'))
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>
                        Produksi ulang untuk mengatasi kegagalan sebelumnya
                    </div>
                @endif

                <div class="d-flex gap-1">
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
        </div>
    @endforeach
@endif
