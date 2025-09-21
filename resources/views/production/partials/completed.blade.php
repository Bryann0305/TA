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
                    {{ $prod->Nama_Produksi ?? 'Produksi #' . ($prod->Id_Produksi ?? $prod->id) }}
                </h5>
                <span class="badge bg-success px-3 py-2">Completed</span>
            </div>

            <div class="card-body">
                <p><strong>SPP:</strong> {{ $prod->pesananProduksi->Nomor_Pesanan ?? '-' }}</p>
                <p><strong>Tanggal:</strong> {{ optional($prod->Tanggal_Produksi)->format('d M Y') ?? '-' }}</p>

                @if($pesananDetails->isNotEmpty())
                    <button class="btn btn-outline-primary btn-sm mt-2" type="button"
                            data-bs-toggle="collapse" data-bs-target="#inputHasil{{ $prodId }}">
                        <i class="bi bi-box-seam me-1"></i> Input Hasil Produksi
                    </button>

                    <div class="collapse mt-3" id="inputHasil{{ $prodId }}">
                        <form class="form-complete" data-prod-id="{{ $prodId }}">
                            @csrf
                            @foreach($pesananDetails as $index => $detail)
                                @php
                                    $detailId = $detail->Id_Pesanan ?? $index;
                                    $namaProduk = $detail->Nama_Produk ?? ($detail->barang->Nama_Bahan ?? 'Produk #' . ($index+1));
                                    $jumlahPesanan = $detail->Jumlah ?? ($detail->jumlah ?? 0);
                                @endphp
                                <div class="card mb-2 border rounded">
                                    <div class="card-header bg-light fw-bold">{{ $namaProduk }}</div>
                                    <div class="card-body">
                                        <p><strong>Jumlah Dipesan:</strong> {{ $jumlahPesanan }}</p>

                                        <div class="mb-2">
                                            <label>Jumlah Berhasil</label>
                                            <input type="number" name="hasil[{{ $detailId }}]" class="form-control hasil-input" min="0" value="0" data-max="{{ $jumlahPesanan }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Jumlah Gagal</label>
                                            <input type="number" name="gagal[{{ $detailId }}][jumlah]" class="form-control gagal-input" min="0" value="0" data-max="{{ $jumlahPesanan }}">
                                        </div>
                                        <div class="mb-2">
                                            <label>Keterangan</label>
                                            <input type="text" name="gagal[{{ $detailId }}][keterangan]" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Simpan Hasil Produksi</button>
                        </form>
                    </div>
                @else
                    <p class="text-muted">SPP ini belum memiliki produk yang valid untuk input hasil produksi.</p>
                @endif
            </div>
        </div>
    @endforeach
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.form-complete');

    forms.forEach(form => {
        const hasilInputs = form.querySelectorAll('.hasil-input');
        const gagalInputs = form.querySelectorAll('.gagal-input');

        function validateSum() {
            hasilInputs.forEach((hInput, idx) => {
                const gInput = gagalInputs[idx];
                const max = parseInt(hInput.dataset.max) || 0;
                let sum = parseInt(hInput.value || 0) + parseInt(gInput.value || 0);
                if(sum > max){
                    alert('Jumlah Berhasil + Gagal tidak boleh melebihi Jumlah Pesanan!');
                    hInput.value = 0;
                    gInput.value = 0;
                }
            });
        }

        hasilInputs.forEach(input => input.addEventListener('change', validateSum));
        gagalInputs.forEach(input => input.addEventListener('change', validateSum));

        form.addEventListener('submit', function(e){
            e.preventDefault();
            const prodId = form.dataset.prodId;
            const actionUrl = `/production/${prodId}/complete`;
            const data = new FormData(form);

            fetch(actionUrl, {
                method: 'POST',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(result => {
                if(result.success){
                    alert(result.success);
                    let tab = result.redirect_tab || 'all';
                    window.location.href = `/production?tab=${tab}`;
                } else if(result.error){
                    alert(result.error);
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
@endpush
