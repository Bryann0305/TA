<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangHasBillOfMaterial extends Model
{
    use HasFactory;

    protected $table = 'barang_has_bill_of_material';
    public $timestamps = false;

    protected $fillable = [
        'barang_id', 'bill_of_material_id'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'Id_Bahan');
    }

    public function billOfMaterial()
    {
        return $this->belongsTo(BillOfMaterial::class, 'bill_of_material_id', 'Id_bill_of_material');
    }
}
