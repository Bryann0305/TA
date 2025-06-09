<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'gudang';
    protected $primaryKey = 'Id_Gudang';
    public $timestamps = false;

    protected $fillable = [
        'Lokasi', 'Kapasitas'
    ];

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'gudang_Id_Gudang', 'Id_Gudang');
    }
}
