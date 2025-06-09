@extends('layouts.app')

@section('title', 'Bill of Materials - Dunia Coating')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Bill of Materials</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Bill of Materials</li>
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
                        <h3 class="card-title">Daftar Bill of Materials</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBomModal">
                                <i class="fas fa-plus"></i> Tambah BOM
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="bomTable">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama BOM</th>
                                        <th>Status</th>
                                        <th>Produksi Terkait</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($boms as $index => $bom)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $bom->Nama_bill_of_material }}</td>
                                        <td>
                                            @if($bom->Status == 'draft')
                                                <span class="badge badge-warning">Draft</span>
                                            @elseif($bom->Status == 'approved')
                                                <span class="badge badge-success">Approved</span>
                                            @else
                                                <span class="badge badge-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($bom->produksi->count() > 0)
                                                <span class="badge badge-info">{{ $bom->produksi->count() }} Produksi</span>
                                            @else
                                                <span class="badge badge-secondary">Belum digunakan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-sm" onclick="viewBom({{ $bom->Id_bill_of_material }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($bom->Status == 'draft')
                                                <button type="button" class="btn btn-warning btn-sm" onclick="editBom({{ $bom->Id_bill_of_material }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteBom({{ $bom->Id_bill_of_material }})">
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

<!-- Add BOM Modal -->
<div class="modal fade" id="addBomModal" tabindex="-1" role="dialog" aria-labelledby="addBomModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBomModalLabel">Tambah Bill of Materials</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addBomForm" action="{{ route('bill-of-materials.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="Nama_bill_of_material">Nama BOM</label>
                        <input type="text" class="form-control" id="Nama_bill_of_material" name="Nama_bill_of_material" required>
                    </div>
                    <div class="form-group">
                        <label for="Status">Status</label>
                        <select class="form-control" id="Status" name="Status" required>
                            <option value="draft">Draft</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
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

<!-- View BOM Modal -->
<div class="modal fade" id="viewBomModal" tabindex="-1" role="dialog" aria-labelledby="viewBomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewBomModalLabel">Detail Bill of Materials</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi BOM</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Nama BOM</th>
                                <td id="viewNamaBom"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="viewStatus"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Produksi Terkait</h6>
                        <div id="viewProduksi"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit BOM Modal -->
<div class="modal fade" id="editBomModal" tabindex="-1" role="dialog" aria-labelledby="editBomModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBomModalLabel">Edit Bill of Materials</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editBomForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="editNamaBom">Nama BOM</label>
                        <input type="text" class="form-control" id="editNamaBom" name="Nama_bill_of_material" required>
                    </div>
                    <div class="form-group">
                        <label for="editStatus">Status</label>
                        <select class="form-control" id="editStatus" name="Status" required>
                            <option value="draft">Draft</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
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
    $('#bomTable').DataTable({
        "responsive": true,
        "autoWidth": false
    });
});

function viewBom(id) {
    $.get(`/bill-of-materials/${id}`, function(data) {
        $('#viewNamaBom').text(data.Nama_bill_of_material);
        $('#viewStatus').html(getStatusBadge(data.Status));
        
        let produksiHtml = '<table class="table table-bordered">';
        if (data.produksi && data.produksi.length > 0) {
            data.produksi.forEach(prod => {
                produksiHtml += `
                    <tr>
                        <td>${prod.Nama_Produksi}</td>
                        <td>${prod.Status}</td>
                    </tr>
                `;
            });
        } else {
            produksiHtml += '<tr><td colspan="2">Belum ada produksi terkait</td></tr>';
        }
        produksiHtml += '</table>';
        $('#viewProduksi').html(produksiHtml);
        
        $('#viewBomModal').modal('show');
    });
}

function editBom(id) {
    $.get(`/bill-of-materials/${id}/edit`, function(data) {
        $('#editBomForm').attr('action', `/bill-of-materials/${id}`);
        $('#editNamaBom').val(data.Nama_bill_of_material);
        $('#editStatus').val(data.Status);
        $('#editBomModal').modal('show');
    });
}

function deleteBom(id) {
    if (confirm('Apakah Anda yakin ingin menghapus BOM ini?')) {
        $.ajax({
            url: `/bill-of-materials/${id}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || 'Terjadi kesalahan saat menghapus BOM');
                }
            }
        });
    }
}

function getStatusBadge(status) {
    switch(status) {
        case 'draft':
            return '<span class="badge badge-warning">Draft</span>';
        case 'approved':
            return '<span class="badge badge-success">Approved</span>';
        case 'rejected':
            return '<span class="badge badge-danger">Rejected</span>';
        default:
            return status;
    }
}
</script>
@endpush 