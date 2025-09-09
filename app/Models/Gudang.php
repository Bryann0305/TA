<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
