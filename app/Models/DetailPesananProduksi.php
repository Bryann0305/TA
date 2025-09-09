<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesananProduksi extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanan_produksi';
    protected $primaryKey = 'Id_Detail';
    public $timestamps = false; // optional jika tidak ada kolom created_at/updated_at
    protected $fillable = [
        'pesanan_produksi_Id_Pesanan',
        'barang_Id_Bahan',
        'Jumlah'
    ];

    // Relasi ke Pesanan Produksi
    public function pesananProduksi()
    {
        return $this->belongsTo(PesananProduksi::class, 'pesanan_produksi_Id_Pesanan', 'Id_Pesanan');
    }

    // Relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_Id_Bahan', 'Id_Bahan');
    }
}
