<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksi';
    protected $primaryKey = 'Id_Produksi';
    public $timestamps = false;

    protected $fillable = [
        'Nama_Produksi',
        'Jumlah_Produksi',
        'Hasil_Produksi',
        'Status',
        'Tanggal_Produksi',
        'Keterangan',
        'Jumlah_Berhasil',
        'Jumlah_Gagal',
        'bahan_baku_Id_Bahan',
        'pesanan_produksi_Id_Pesanan',
        'penjadwalan_Id_Jadwal',
        'bill_of_material_Id_bill_of_material'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'bahan_baku_Id_Bahan', 'Id_Bahan');
    }

    public function pesananProduksi()
    {
        return $this->belongsTo(PesananProduksi::class, 'pesanan_produksi_Id_Pesanan', 'Id_Pesanan');
    }

    public function jadwal()
    {
        return $this->belongsTo(Penjadwalan::class, 'penjadwalan_Id_Jadwal', 'Id_Jadwal');
    }

    public function billOfMaterial()
    {
        return $this->belongsTo(BillOfMaterial::class, 'bill_of_material_Id_bill_of_material', 'Id_bill_of_material');
    }
}
