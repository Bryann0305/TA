{{-- resources/views/production/partials/all.blade.php --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-primary">
        <i class="bi bi-list-ul me-2"></i> All Productions
    </h5>
</div>

@if(!isset($all) || $all->isEmpty())
    <p class="text-muted">Tidak ada data produksi.</p>
@else
    @foreach($all as $prod)
        @php
            // Ambil ID produksi dan status
            $prodId = $prod->Id_Produksi ?? $prod->id;
            $status = strtolower($prod->Status ?? 'unknown');

            // Tentukan warna badge
            $badgeClass = match($status) {
                'planned' => 'warning text-dark',
                'current' => 'info',
                'completed' => 'success',
                default => 'secondary'
            };

            // Format tanggal
            $tanggalProduksi = $prod->Tanggal_Produksi
                ? \Carbon\Carbon::parse($prod->Tanggal_Produksi)->format('d M Y')
                : '-';
            $tanggalMulai = optional($prod->penjadwalan)->Tanggal_Mulai
                ? \Carbon\Carbon::parse($prod->penjadwalan->Tanggal_Mulai)->format('d M Y')
                : '-';
            $tanggalSelesai = optional($prod->penjadwalan)->Tanggal_Selesai
                ? \Carbon\Carbon::parse($prod->penjadwalan->Tanggal_Selesai)->format('d M Y')
                : '-';

            // Relasi pesanan dan detail
            $pesanan = $prod->pesananProduksi ?? null;
            $pesananDetails = $pesanan->detail ?? collect();
        @endphp

        <div class="card border-0 shadow-lg mb-4">
            {{-- Header --}}
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    {{ $prod->Nama_Produksi ?? 'Produksi #' . $prodId }}
                </h5>
                <span class="badge bg-{{ $badgeClass }} px-3 py-2">
                    {{ ucfirst($status) }}
                </span>
            </div>

            {{-- Body --}}
            <div class="card-body">
                <p class="mb-1">
                    <i class="bi bi-file-earmark-text me-2"></i> 
                    <strong>SPP:</strong> {{ $pesanan->Nomor_Pesanan ?? '-' }}
                </p>

                {{-- ✅ Format tanggal diseragamkan --}}
                <p class="mb-2">
                    <i class="bi bi-calendar-event me-2"></i>
                    <strong>Tanggal Produksi:</strong> {{ $tanggalProduksi }} <br>
                    <strong>Rencana Mulai:</strong> {{ $tanggalMulai }} <br>
                    <strong>Rencana Selesai:</strong> {{ $tanggalSelesai }}
                </p>

                {{-- BOM Summary --}}
                @if($prod->details && $prod->details->count())
                    <button class="btn btn-outline-primary btn-sm mt-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#bomSummaryAll{{ $prodId }}">
                        <i class="bi bi-list-ul me-1"></i> Lihat BOM Summary
                    </button>

                    <div class="collapse mt-3" id="bomSummaryAll{{ $prodId }}">
                        <ul class="list-unstyled border-start ps-3">
                            @foreach($prod->details->groupBy('bill_of_material_id') as $bomId => $group)
                                @php
                                    $bom = $group->first()->billOfMaterial ?? null;
                                    $rawMaterials = $bom ? $bom->barangs : collect();
                                    $jumlahBahanBaku = $rawMaterials->count();
                                    $quantityPesanan = $pesanan->Jumlah_Pesanan ?? 1;
                                @endphp
                                <li class="mb-3">
                                    <h6 class="fw-bold text-primary">
                                        {{ $bom->Nama_bill_of_material ?? 'BOM '.$bomId }}
                                    </h6>
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
                                                    <li>• {{ $material->Nama_Bahan }} 
                                                        ({{ $material->pivot->Jumlah_Bahan * $quantityPesanan }} {{ $material->Satuan }})
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-muted mt-2">Tidak ada data BOM untuk produksi ini.</p>
                @endif
            </div>

            {{-- Footer --}}
            <div class="card-footer bg-white d-flex justify-content-end gap-1">
                {{-- View --}}
                <a href="{{ route('production.show', $prodId) }}" 
                   class="btn btn-sm btn-info" title="View">
                    <i class="fas fa-eye"></i>
                </a>

                {{-- Delete --}}
                <form action="{{ route('production.destroy', $prodId) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin hapus produksi ini?')" class="d-inline">
                    @csrf 
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-dark" title="Delete">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    @endforeach
@endif
