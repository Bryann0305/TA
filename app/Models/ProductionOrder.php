<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $table = 'production_order';
    protected $fillable = [
        'Nama_Produksi',
        'Tanggal_Produksi',
        'Status',
        'pesanan_produksi_id',
    ];

    public function pesananProduksi()
    {
        return $this->belongsTo(PesananProduksi::class, 'pesanan_produksi_id', 'Id_Pesanan');
    }

    public function penjadwalan()
    {
        return $this->hasOne(Penjadwalan::class, 'production_order_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'Id_Barang', 'Id_Barang');
    }

    public function billOfMaterial()
    {
        return $this->belongsTo(BillOfMaterial::class, 'Id_BOM', 'Id_bill_of_material');
    }

}
