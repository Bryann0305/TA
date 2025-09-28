{{-- resources/views/production/partials/all.blade.php --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-primary"><i class="bi bi-list-ul me-2"></i> All Productions</h5>
</div>

@if(!isset($all) || $all->isEmpty())
    <p class="text-muted">Tidak ada data produksi.</p>
@else
    @foreach($all as $prod)
    <div class="card border-0 shadow-lg mb-4">
        {{-- Header --}}
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                    {{ $prod->Nama_Produksi ?? 'Produksi #' . ($prod->Id_Produksi ?? $prod->id) }}
                </h5>
            @php
                $badgeClass = $prod->Status === 'planned' ? 'warning text-dark' : ($prod->Status === 'current' ? 'info' : 'primary');
            @endphp
            <span class="badge bg-{{ $badgeClass }} px-3 py-2">{{ ucfirst($prod->Status) }}</span>
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

            {{-- BOM Summary --}}
            @if($prod->details && $prod->details->count())
                <button class="btn btn-outline-primary btn-sm mt-2" type="button"
                        data-bs-toggle="collapse" data-bs-target="#bomSummaryAll{{ $prod->Id_Produksi }}">
                    <i class="bi bi-list-ul me-1"></i> Lihat BOM Summary
                </button>
                <div class="collapse mt-3" id="bomSummaryAll{{ $prod->Id_Produksi }}">
                    <ul class="list-unstyled border-start ps-3">
                        @foreach($prod->details->groupBy('bill_of_material_id') as $bomId => $group)
                            @php
                                $bom = $group->first()->billOfMaterial ?? null;
                                $rawMaterials = $bom ? $bom->barangs : collect();
                                $jumlahBahanBaku = $rawMaterials->count(); // Jumlah bahan baku yang berbeda dari BOM
                                $quantityPesanan = $prod->productionOrder->pesananProduksi->Jumlah_Pesanan ?? 1;
                            @endphp
                            <li class="mb-3">
                                <h6 class="fw-bold text-primary">{{ $bom->Nama_bill_of_material ?? 'BOM '.$bomId }}</h6>
                                <div class="small text-muted">
                                    <div><strong>ID BOM:</strong> {{ $bom->Id_bill_of_material ?? $bomId }}</div>
                                    <div><strong>Deskripsi:</strong> {{ $bom->deskripsi ?? '-' }}</div>
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
            @endif
        </div>

        {{-- Footer --}}
        <div class="card-footer bg-white d-flex justify-content-end gap-1">
            {{-- View --}}
            <a href="{{ route('production.show', $prod->Id_Produksi) }}" class="btn btn-sm btn-info" title="View">
                <i class="fas fa-eye"></i>
            </a>

            {{-- Delete --}}
            <form action="{{ route('production.destroy', $prod->Id_Produksi) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin hapus produksi ini?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-dark" title="Delete">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>
    @endforeach
@endif
