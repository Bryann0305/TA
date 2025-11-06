@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Laporan Produksi per Tahun</h4>

        <form method="GET" action="{{ route('laporan.produksi') }}" class="d-flex align-items-center">
            <label class="me-2 fw-semibold">Filter Tahun:</label>
            <select name="tahun" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                @for ($i = date('Y'); $i >= 2020; $i--)
                    <option value="{{ $i }}" {{ (request('tahun') == $i) ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </form>
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered align-middle">
            <thead class="table-primary text-center">
                <tr>
                    <th width="10%">ID Produksi</th>
                    <th width="25%">Nama Produksi</th>
                    <th width="20%">Tanggal Produksi</th>
                    <th width="20%">Jumlah Berhasil</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($data['productions'] as $prod)
                    <tr class="text-center">
                        <td>{{ $prod->Id_Produksi }}</td>
                        <td>{{ $prod->Nama_Produksi ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($prod->Tanggal_Produksi)->format('d M Y') }}</td>
                        <td>{{ $prod->Jumlah_Berhasil }}</td>
                        <td>
                            <span class="badge {{ $prod->Status === 'Selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $prod->Status }}
                            </span>
                        </td>
                    </tr>

                    {{-- Detail BOM --}}
                    <tr>
                        <td colspan="5" class="p-0">
                            <table class="table table-sm table-bordered mb-0 bg-light">
                                <thead class="table-secondary text-center">
                                    <tr>
                                        <th>BOM</th>
                                        <th>Bahan</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (isset($data['productionDetails'][$prod->Id_Produksi]) && count($data['productionDetails'][$prod->Id_Produksi]) > 0)
                                        @foreach ($data['productionDetails'][$prod->Id_Produksi] as $detail)
                                            <tr class="text-center">
                                                <td>{{ str_replace('BOM - ', '', $detail->Nama_bill_of_material) }}</td>
                                                <td>{{ $detail->Nama_Bahan }}</td>
                                                <td>{{ $detail->jumlah }}</td>
                                                <td>{{ $detail->status }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada detail bahan.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Tidak ada data produksi pada tahun ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
