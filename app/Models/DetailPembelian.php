<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;

    protected $table = 'detail_pembelian';
    protected $primaryKey = 'Id_Detail_Pembelian';
    public $timestamps = true;

    protected $fillable = [
        'pembelian_Id_Pembelian',
        'barang_Id_Barang',
        'Jumlah',
        'Harga_Satuan',
        'Subtotal',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_Id_Pembelian', 'Id_Pembelian');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_Id_Barang', 'Id_Bahan');
    }
}
