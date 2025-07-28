<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesananProduksi extends Model
{
    protected $table = 'pesanan_produksi';
    protected $primaryKey = 'Id_Pesanan';
    public $timestamps = false;

    protected $fillable = [
        'Jumlah_Pesanan',
        'Status',
        'Tanggal_Pesanan',
        'user_Id_User',
        'pelanggan_Id_Pelanggan',
        'Surat_Perintah_Produksi'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_Id_User', 'Id_User');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_Id_Pelanggan', 'Id_Pelanggan');
    }

    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'pesanan_produksi_Id_Pesanan', 'Id_Pesanan');
    }
}
