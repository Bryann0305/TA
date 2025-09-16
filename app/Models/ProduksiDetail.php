<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiDetail extends Model
{
    use HasFactory;

    protected $table = 'produksi_detail';
    protected $fillable = [
    'produksi_id',
    'bill_of_material_id',
    'barang_id',
    'jumlah',
    'status'
];

    public $timestamps = false;

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id', 'Id_Produksi');
    }

    public function billOfMaterial()
    {
        return $this->belongsTo(BillOfMaterial::class, 'bill_of_material_id', 'Id_bill_of_material');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'Id_Bahan');
    }
}
