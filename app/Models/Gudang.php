<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Barang;

class Gudang extends Model
{
    protected $table = 'gudang'; 
    public $timestamps = false;
    protected $primaryKey = 'Id_Gudang'; 
    protected $fillable = ['Nama_Gudang', 'Kapasitas', 'latitude', 'longitude', 'alamat', 'Lokasi'];


    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'gudang_Id_Gudang', 'Id_Gudang');
    }

   public function inventories()
{
    return $this->hasMany(Barang::class, 'gudang_Id_Gudang', 'Id_Gudang');
}

public function barangs()
{
    return $this->hasMany(Barang::class, 'gudang_Id_Gudang', 'Id_Gudang');
}

public function biayaGudang()
{
    return $this->hasMany(BiayaGudang::class, 'gudang_Id_Gudang', 'Id_Gudang');
}

}
