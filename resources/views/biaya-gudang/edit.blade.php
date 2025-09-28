@extends('layouts.sidebar')

@section('content')
<div class="container">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Biaya Gudang</h2>
        <a href="{{ route('biaya-gudang.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('biaya-gudang.update', $biayaGudang->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="gudang_Id_Gudang" class="form-label">Pilih Gudang <span class="text-danger">*</span></label>
                            <select class="form-select @error('gudang_Id_Gudang') is-invalid @enderror" id="gudang_Id_Gudang" name="gudang_Id_Gudang" required>
                                <option value="">-- Pilih Gudang --</option>
                                @foreach($gudangs as $gudang)
                                    <option value="{{ $gudang->Id_Gudang }}" 
                                            {{ (old('gudang_Id_Gudang', $biayaGudang->gudang_Id_Gudang) == $gudang->Id_Gudang) ? 'selected' : '' }}>
                                        {{ $gudang->Nama_Gudang }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gudang_Id_Gudang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tanggal_biaya" class="form-label">Tanggal Biaya <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_biaya') is-invalid @enderror" 
                                   id="tanggal_biaya" name="tanggal_biaya" 
                                   value="{{ old('tanggal_biaya', $biayaGudang->tanggal_biaya->format('Y-m-d')) }}" required>
                            @error('tanggal_biaya')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="biaya_sewa" class="form-label">Biaya Sewa <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control @error('biaya_sewa') is-invalid @enderror currency-input" 
                                       id="biaya_sewa" name="biaya_sewa" 
                                       value="{{ old('biaya_sewa', number_format($biayaGudang->biaya_sewa, 0, ',', '.')) }}" 
                                       placeholder="0" required>
                            </div>
                            @error('biaya_sewa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="biaya_listrik" class="form-label">Biaya Listrik <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control @error('biaya_listrik') is-invalid @enderror currency-input" 
                                       id="biaya_listrik" name="biaya_listrik" 
                                       value="{{ old('biaya_listrik', number_format($biayaGudang->biaya_listrik, 0, ',', '.')) }}" 
                                       placeholder="0" required>
                            </div>
                            @error('biaya_listrik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="biaya_air" class="form-label">Biaya Air <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control @error('biaya_air') is-invalid @enderror currency-input" 
                                       id="biaya_air" name="biaya_air" 
                                       value="{{ old('biaya_air', number_format($biayaGudang->biaya_air, 0, ',', '.')) }}" 
                                       placeholder="0" required>
                            </div>
                            @error('biaya_air')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                              id="keterangan" name="keterangan" rows="3" 
                              placeholder="Tambahkan keterangan (opsional)">{{ old('keterangan', $biayaGudang->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('biaya-gudang.index') }}" class="btn btn-light">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Format mata uang untuk input
        $('.currency-input').on('input', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = new Intl.NumberFormat('id-ID').format(value);
            }
        });

        // Format mata uang saat focus out
        $('.currency-input').on('blur', function() {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = new Intl.NumberFormat('id-ID').format(value);
            }
        });

        // Format mata uang saat focus in
        $('.currency-input').on('focus', function() {
            this.value = this.value.replace(/[^\d]/g, '');
        });

        // Submit form - convert formatted values back to numbers
        $('form').on('submit', function() {
            $('.currency-input').each(function() {
                this.value = this.value.replace(/[^\d]/g, '');
            });
        });
    });
</script>
@endpush
