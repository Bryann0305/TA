@extends('layouts.app')

@section('title', 'Produksi - Dunia Coating')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Produksi</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Produksi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Produksi</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProduksiModal">
                                <i class="fas fa-plus"></i> Tambah Produksi
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="produksiTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produksi</th>
                                        <th>BOM</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($produksi as $index => $prod)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $prod->Nama_Produksi }}</td>
                                        <td>{{ $prod->billOfMaterial->Nama_bill_of_material }}</td>
                                        <td>{{ $prod->Jumlah_Produksi }}</td>
                                        <td>
                                            @if($prod->Status == 'planned')
                                                <span class="badge badge-info">Planned</span>
                                            @elseif($prod->Status == 'in_progress')
                                                <span class="badge badge-warning">In Progress</span>
                                            @elseif($prod->Status == 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span class="badge badge-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" onclick="viewProduksi({{ $prod->Id_Produksi }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($prod->Status != 'completed')
                                                <button type="button" class="btn btn-warning btn-sm" onclick="editProduksi({{ $prod->Id_Produksi }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteProduksi({{ $prod->Id_Produksi }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Add Produksi Modal -->
<div class="modal fade" id="addProduksiModal" tabindex="-1" role="dialog" aria-labelledby="addProduksiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProduksiModalLabel">Tambah Produksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addProduksiForm" action="{{ route('production.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="Nama_Produksi">Nama Produksi</label>
                        <input type="text" class="form-control" id="Nama_Produksi" name="Nama_Produksi" required>
                    </div>
                    <div class="form-group">
                        <label for="bill_of_material_Id_bill_of_material">Bill of Materials</label>
                        <select class="form-control" id="bill_of_material_Id_bill_of_material" name="bill_of_material_Id_bill_of_material" required>
                            <option value="">Pilih BOM</option>
                            @foreach($boms as $bom)
                                <option value="{{ $bom->Id_bill_of_material }}">{{ $bom->Nama_bill_of_material }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Jumlah_Produksi">Jumlah Produksi</label>
                        <input type="number" class="form-control" id="Jumlah_Produksi" name="Jumlah_Produksi" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="Status">Status</label>
                        <select class="form-control" id="Status" name="Status" required>
                            <option value="planned">Planned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Produksi Modal -->
<div class="modal fade" id="viewProduksiModal" tabindex="-1" role="dialog" aria-labelledby="viewProduksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProduksiModalLabel">Detail Produksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Produksi</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Nama Produksi</th>
                                <td id="viewNamaProduksi"></td>
                            </tr>
                            <tr>
                                <th>BOM</th>
                                <td id="viewBom"></td>
                            </tr>
                            <tr>
                                <th>Jumlah</th>
                                <td id="viewJumlah"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="viewStatus"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Produksi Modal -->
<div class="modal fade" id="editProduksiModal" tabindex="-1" role="dialog" aria-labelledby="editProduksiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProduksiModalLabel">Edit Produksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editProduksiForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editNamaProduksi">Nama Produksi</label>
                        <input type="text" class="form-control" id="editNamaProduksi" name="Nama_Produksi" required>
                    </div>
                    <div class="form-group">
                        <label for="editBom">Bill of Materials</label>
                        <select class="form-control" id="editBom" name="bill_of_material_Id_bill_of_material" required>
                            <option value="">Pilih BOM</option>
                            @foreach($boms as $bom)
                                <option value="{{ $bom->Id_bill_of_material }}">{{ $bom->Nama_bill_of_material }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editJumlah">Jumlah Produksi</label>
                        <input type="number" class="form-control" id="editJumlah" name="Jumlah_Produksi" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status</label>
                        <select class="form-control" id="editStatus" name="Status" required>
                            <option value="planned">Planned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#produksiTable').DataTable({
        "responsive": true,
        "autoWidth": false
    });
});

function viewProduksi(id) {
    $.get(`/production/${id}`, function(data) {
        $('#viewNamaProduksi').text(data.Nama_Produksi);
        $('#viewBom').text(data.bill_of_material.Nama_bill_of_material);
        $('#viewJumlah').text(data.Jumlah_Produksi);
        $('#viewStatus').html(getStatusBadge(data.Status));
        
        $('#viewProduksiModal').modal('show');
    });
}

function editProduksi(id) {
    $.get(`/production/${id}/edit`, function(data) {
        $('#editProduksiForm').attr('action', `/production/${id}`);
        $('#editNamaProduksi').val(data.Nama_Produksi);
        $('#editBom').val(data.bill_of_material_Id_bill_of_material);
        $('#editJumlah').val(data.Jumlah_Produksi);
        $('#editStatus').val(data.Status);
        $('#editProduksiModal').modal('show');
    });
}

function deleteProduksi(id) {
    if (confirm('Apakah Anda yakin ingin menghapus produksi ini?')) {
        $.ajax({
            url: `/production/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Terjadi kesalahan saat menghapus produksi');
                }
            }
        });
    }
}

function getStatusBadge(status) {
    switch(status) {
        case 'planned':
            return '<span class="badge badge-info">Planned</span>';
        case 'in_progress':
            return '<span class="badge badge-warning">In Progress</span>';
        case 'completed':
            return '<span class="badge badge-success">Completed</span>';
        case 'cancelled':
            return '<span class="badge badge-danger">Cancelled</span>';
        default:
            return status;
    }
}
</script>
@endpush
