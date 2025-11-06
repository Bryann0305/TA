@if(!$completed || $completed->isEmpty())
    <p class="text-muted">Tidak ada produksi selesai.</p>
@else
    @foreach($completed as $prod)
        @php
            $pesananDetails = $prod->pesananProduksi->detail ?? collect();
            $prodId = $prod->Id_Produksi ?? $prod->id;
        @endphp

        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-dark">
                    {{ $prod->Nama_Produksi ?? 'Produksi #' . $prodId }}
                </h5>
                <span class="badge bg-success px-3 py-2">Completed</span>
            </div>

            <div class="card-body">
                <p><strong>SPP:</strong> {{ $prod->pesananProduksi->Nomor_Pesanan ?? '-' }}</p>
                <p><strong>Tanggal:</strong> {{ optional($prod->Tanggal_Produksi)->format('d M Y') ?? '-' }}</p>

                {{-- Loop semua produk dalam produksi --}}
                @if($prod->details && $prod->details->count())
                    <button class="btn btn-outline-primary btn-sm mt-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#inputHasil{{ $prodId }}">
                        <i class="bi bi-box-seam me-1"></i> Input Hasil Produksi
                    </button>

                    <div class="collapse mt-3" id="inputHasil{{ $prodId }}">
                        <form class="form-complete" data-prod-id="{{ $prodId }}">
                            @csrf

                            @foreach($prod->details as $detail)
                                @php
                                    $barang = $detail->barang ?? null;
                                    $namaProdukJadi = $barang->Nama_Bahan ?? 'Produk Tidak Dikenal';
                                    $jumlahPesananProduk = optional($prod->pesananProduksi)->Jumlah_Pesanan ?? 0;
                                @endphp

                                <div class="card mb-3 border rounded">
                                    <div class="card-header bg-light fw-bold">
                                        {{ $namaProdukJadi }}
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Jumlah Dipesan:</strong> {{ $jumlahPesananProduk }}</p>

                                        <div class="mb-2">
                                            <label>Jumlah Berhasil</label>
                                            <input type="number" 
                                                name="hasil[{{ $detail->id }}]" 
                                                class="form-control hasil-input" 
                                                min="0" value="0" 
                                                data-max="{{ $jumlahPesananProduk }}" 
                                                required>
                                        </div>

                                        <div class="mb-2">
                                            <label>Jumlah Gagal</label>
                                            <input type="number" 
                                                name="gagal[{{ $detail->id }}][jumlah]" 
                                                class="form-control gagal-input" 
                                                min="0" value="0" 
                                                data-max="{{ $jumlahPesananProduk }}">
                                        </div>

                                        <div class="mb-2">
                                            <label>Keterangan</label>
                                            <input type="text" 
                                                name="gagal[{{ $detail->id }}][keterangan]" 
                                                class="form-control">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-primary btn-sm mt-2">Simpan Hasil Produksi</button>
                        </form>
                    </div>
                @else
                    <p class="text-muted">Produksi ini belum memiliki produk detail untuk diinput hasilnya.</p>
                @endif

                {{-- Detail hasil produksi --}}
                @if($prod->Jumlah_Berhasil > 0 || $prod->Jumlah_Gagal > 0)
                    <button class="btn btn-outline-info btn-sm mt-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#detailHasil{{ $prodId }}">
                        <i class="bi bi-info-circle me-1"></i> Detail Informasi
                    </button>

                    <div class="collapse mt-3" id="detailHasil{{ $prodId }}">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Hasil Produksi</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="alert alert-success">
                                            <h6 class="alert-heading">Jumlah Berhasil</h6>
                                            <p class="mb-0"><strong>{{ $prod->Jumlah_Berhasil ?? 0 }}</strong> unit</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-danger">
                                            <h6 class="alert-heading">Jumlah Gagal</h6>
                                            <p class="mb-0"><strong>{{ $prod->Jumlah_Gagal ?? 0 }}</strong> unit</p>
                                        </div>
                                    </div>
                                </div>

                                @if($prod->gagalProduksi && $prod->gagalProduksi->count() > 0)
                                    <div class="mt-3">
                                        <h6>Detail Gagal Produksi:</h6>
                                        <ul class="list-group">
                                            @foreach($prod->gagalProduksi as $gagal)
                                                <li class="list-group-item">
                                                    <strong>Total Gagal:</strong> {{ $gagal->Total_Gagal }} unit<br>
                                                    <strong>Keterangan:</strong> {{ $gagal->Keterangan ?? '-' }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@endif