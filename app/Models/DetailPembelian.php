<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;

    protected $table = 'detail_pembelian';
    protected $primaryKey = 'Id_Detail';
    public $timestamps = false;

    protected $fillable = [
        'Harga_Keseluruhan', 'Jumlah', 'Keterangan', 'pembelian_Id_Pembelian', 'gudang_Id_Gudang', 'bahan_baku_Id_Bahan'
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_Id_Pembelian', 'Id_Pembelian');
    }

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_Id_Gudang', 'Id_Gudang');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'bahan_baku_Id_Bahan', 'Id_Bahan');
    }
}
