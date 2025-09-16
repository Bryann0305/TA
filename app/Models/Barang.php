<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'Id_Bahan';
    public $timestamps = false;

    // Kalau Id_Bahan auto increment integer
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'Nama_Bahan',
        'Stok', 
        'Jenis', 
        'Status', 
        'kategori_Id_Kategori', 
        'Unit',
        'Berat', 
        'Satuan', 
        'EOQ', 
        'ROP' // ✅ perbaikan, jangan "Reorder_Point"
    ];

    public function gudang()
{
    return $this->belongsTo(Gudang::class, 'gudang_Id_Gudang', 'Id_Gudang');
}

    // Relasi ke Kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_Id_Kategori', 'Id_Kategori');
    }

    // Relasi ke Detail Pembelian
    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'bahan_baku_Id_Bahan', 'Id_Bahan');
    }

    // Relasi ke Bill of Materials
    public function boms()
    {
        return $this->belongsToMany(
            BillOfMaterial::class, 
            'barang_has_bill_of_material', 
            'barang_Id_Bahan', 
            'bill_of_material_Id_bill_of_material'
        )->withPivot('Jumlah_Bahan');
    }

    

}
