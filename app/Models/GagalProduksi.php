<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GagalProduksi extends Model
{
    use HasFactory;
    // ... existing code ...
    protected $table = 'gagal_produksi';
    protected $primaryKey = 'Id_Gagal';
    public $timestamps = false;

    protected $fillable = [
        'Total_Gagal', 'Keterangan', 'produksi_Id_Produksi'
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'produksi_Id_Produksi', 'Id_Produksi');
    }
// ... existing code ...
}
