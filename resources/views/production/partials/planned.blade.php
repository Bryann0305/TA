{{-- resources/views/production/partials/planned.blade.php --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-primary"><i class="bi bi-clipboard-data me-2"></i> Planned Productions</h5>
    <a href="{{ route('production.create') }}" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-circle me-1"></i> New Production
    </a>
</div>

@if(!$planned || $planned->isEmpty())
    <p class="text-muted">Tidak ada produksi planned.</p>
@else
    @foreach($planned as $prod)
        @php
            $pesananDetails = $prod->pesananProduksi->detail ?? collect();
            $prodId = $prod->Id_Produksi ?? $prod->id;
        @endphp

        <div class="card border-0 shadow-lg mb-4">
            {{-- Header --}}
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    {{ $prod->Nama_Produksi ?? 'Produksi #' . $prodId }}
                </h5>
                <span class="badge bg-warning text-dark px-3 py-2">Planned</span>
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
                @if($pesananDetails->isNotEmpty())
                    <button class="btn btn-outline-primary btn-sm mt-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#bomSummaryPlanned{{ $prodId }}">
                        <i class="bi bi-list-ul me-1"></i> Lihat BOM Summary
                    </button>

                    <div class="collapse mt-3" id="bomSummaryPlanned{{ $prodId }}">
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
                {{-- View --}}
                <a href="{{ route('production.show', ['id' => $prodId, 'tab' => 'planned']) }}" class="btn btn-sm btn-info" title="View">
                    <i class="fas fa-eye"></i>
                </a>

                {{-- Edit --}}
                <a href="{{ route('production.edit', $prodId) }}" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>

                {{-- Approve --}}
                @if($prod->Status === 'planned')
                    <form action="{{ route('production.approve', $prodId) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                @endif

                {{-- Delete --}}
                <form action="{{ route('production.destroy', $prodId) }}" method="POST"
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
