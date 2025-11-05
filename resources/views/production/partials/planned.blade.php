{{-- resources/views/production/partials/planned.blade.php --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-primary">
        <i class="bi bi-clipboard-data me-2"></i> Planned Productions
    </h5>
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
            $tanggalProduksi = $prod->Tanggal_Produksi 
                ? \Carbon\Carbon::parse($prod->Tanggal_Produksi)->format('d M Y') 
                : null;
            $tanggalMulai = optional($prod->penjadwalan)->Tanggal_Mulai 
                ? \Carbon\Carbon::parse($prod->penjadwalan->Tanggal_Mulai)->format('d M Y') 
                : null;
            $tanggalSelesai = optional($prod->penjadwalan)->Tanggal_Selesai 
                ? \Carbon\Carbon::parse($prod->penjadwalan->Tanggal_Selesai)->format('d M Y') 
                : null;
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

                {{-- Tanggal --}}
                <p class="mb-2">
                    <i class="bi bi-calendar-event me-2"></i>
                    <strong>Tanggal Produksi:</strong> {{ $tanggalProduksi ?? '-' }} <br>
                    <strong>Rencana Mulai:</strong> {{ $tanggalMulai ?? '-' }} <br>
                    <strong>Rencana Selesai:</strong> {{ $tanggalSelesai ?? '-' }}
                </p>

                {{-- BOM Summary --}}
                @if($pesananDetails->isNotEmpty() && $prod->details->isNotEmpty())
                    <button class="btn btn-outline-primary btn-sm mt-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#bomSummaryPlanned{{ $prodId }}">
                        <i class="bi bi-list-ul me-1"></i> Lihat BOM Summary
                    </button>

                    <div class="collapse mt-3" id="bomSummaryPlanned{{ $prodId }}">
                        <ul class="list-unstyled border-start ps-3">
                            @foreach($prod->details as $detail)
                                @php
                                    $barang = $detail->barang ?? null;
                                    $bom = $detail->billOfMaterial ?? null;
                                    $rawMaterials = $bom ? $bom->barangs : collect();
                                    $quantityPesanan = optional($prod->pesananProduksi)->Jumlah_Pesanan ?? 1;
                                @endphp

                                @if($barang && $bom)
                                    <li class="mb-3">
                                        <h6 class="fw-bold text-primary mb-1">
                                            {{ $barang->Nama_Bahan ?? 'Produk Tanpa Nama' }}
                                        </h6>
                                        <div class="small text-muted">
                                            <strong>BOM:</strong> {{ $bom->Nama_bill_of_material ?? 'Tidak diketahui' }}
                                            <ul class="list-unstyled ms-3 mt-2">
                                                @forelse($rawMaterials as $material)
                                                    <li>
                                                        → {{ $material->Nama_Bahan }} 
                                                        ({{ $material->pivot->Jumlah_Bahan * $quantityPesanan }} {{ $material->Satuan }})
                                                    </li>
                                                @empty
                                                    <li class="text-muted">Tidak ada bahan baku pada BOM ini.</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </li>
                                @else
                                    <li class="text-muted">Produk tidak memiliki BOM atau data bahan belum lengkap.</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-muted mt-2">SPP ini belum memiliki produk yang valid untuk BOM summary.</p>
                @endif
            </div>

            {{-- Footer --}}
            <div class="card-footer bg-white d-flex justify-content-end gap-1">
                {{-- View --}}
                <a href="{{ route('production.show', ['id' => $prodId, 'tab' => 'planned']) }}" 
                   class="btn btn-sm btn-info" title="View">
                    <i class="fas fa-eye"></i>
                </a>

                {{-- Edit --}}
                <a href="{{ route('production.edit', $prodId) }}" 
                   class="btn btn-sm btn-warning" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>

                {{-- Approve --}}
                @if($prod->Status === 'planned')
                    <form action="{{ route('production.approve', $prodId) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" title="Approve">
                            <i class="fas fa-check"></i>
                        </button>
                    </form>
                @endif

                {{-- Delete --}}
                <form action="{{ route('production.destroy', $prodId) }}" 
                      method="POST"
                      onsubmit="return confirm('Yakin ingin hapus produksi ini?')" 
                      class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-dark" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@endif
