<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOfMaterial extends Model
{
    use HasFactory;

    protected $table = 'bill_of_material';
    protected $primaryKey = 'Id_bill_of_material';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Nama_bill_of_material',
        'Status'
    ];

    // Relasi ke tabel pivot: barang_has_bill_of_material
    public function barangHasBill()
    {
        return $this->hasMany(
            BarangHasBillOfMaterial::class,
            'bill_of_material_Id_bill_of_material',
            'Id_bill_of_material'
        );
    }

    // Relasi many-to-many ke Barang langsung
    public function barangs()  // ubah dari 'barang' menjadi 'barangs'
{
    return $this->belongsToMany(
        Barang::class,
        'barang_has_bill_of_material',
        'bill_of_material_Id_bill_of_material',
        'barang_Id_Bahan'
    )->withPivot('Jumlah_Bahan'); // supaya bisa akses jumlah bahan di pivot
}
    // Relasi ke tabel Produksi
    public function produksi()
    {
        return $this->hasMany(
            Produksi::class,
            'bill_of_material_Id_bill_of_material',
            'Id_bill_of_material'
        );
    }
}
