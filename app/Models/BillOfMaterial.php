<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillOfMaterial extends Model
{
    use HasFactory;

    // ... existing code ...
    protected $table = 'bill_of_material';
    protected $primaryKey = 'Id_bill_of_material';
    public $timestamps = true;

    protected $fillable = [
        'Nama_bill_of_material',
        'Status'
    ];

    public function barangs()
    {
        return $this->belongsToMany(
            Barang::class,
            'barang_has_bill_of_material',
            'bill_of_material_Id_bill_of_material',
            'barang_Id_Bahan'
        );
    }

    public function barang()
    {
        return $this->barangs();
    }

    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'bill_of_material_Id_bill_of_material', 'Id_bill_of_material');
    }

    public function barangHasBill()
    {
        return $this->hasMany(\App\Models\BarangHasBillOfMaterial::class, 'bill_of_material_id', 'Id_bill_of_material');
    }
// ... existing code ...
}
