<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'Id_Pelanggan';
    public $timestamps = false;

    protected $fillable = [
        'Nama_Pelanggan', 'Nomor_Telp', 'Alamat', 'Status'
    ];

    public function pesananProduksi()
    {
        return $this->hasMany(PesananProduksi::class, 'pelanggan_Id_Pelanggan', 'Id_Pelanggan');
    }
}
