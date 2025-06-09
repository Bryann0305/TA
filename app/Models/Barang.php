<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'Id_Bahan';
    public $timestamps = false;

    protected $fillable = [
        'Nama_Bahan', 'Stok', 'Jenis', 'Status', 'kategori_Id_Kategori'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_Id_Kategori', 'Id_Kategori');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'bahan_baku_Id_Bahan', 'Id_Bahan');
    }

    public function billOfMaterials()
    {
        return $this->belongsToMany(
            BillOfMaterial::class,
            'barang_has_bill_of_material',
            'barang_Id_Bahan',
            'bill_of_material_Id_bill_of_material'
        );
    }
}