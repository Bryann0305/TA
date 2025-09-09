<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DetailPesananProduksi;
use App\Models\User;
use App\Models\Pelanggan;

class PesananProduksi extends Model
{
    use HasFactory;

    protected $table = 'pesanan_produksi';
    protected $primaryKey = 'Id_Pesanan';
    public $timestamps = false; 

    protected $fillable = [
        'Nomor_Pesanan',
        'Jumlah_Pesanan',
        'Status',
        'Tanggal_Pesanan',
        'user_Id_User',
        'pelanggan_Id_Pelanggan'
    ];

    // **CAST TANGGAL AGAR OTOMATIS MENJADI CARBON**
    protected $casts = [
        'Tanggal_Pesanan' => 'datetime:Y-m-d',
    ];

    public function detail()
    {
        return $this->hasMany(DetailPesananProduksi::class, 'pesanan_produksi_Id_Pesanan', 'Id_Pesanan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_Id_User', 'Id_User');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_Id_Pelanggan', 'Id_Pelanggan');
    }

    public function productionOrder()
    {
        return $this->hasOne(ProductionOrder::class, 'pesanan_produksi_id');
    }
}
