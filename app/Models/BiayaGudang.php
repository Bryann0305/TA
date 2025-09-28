<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaGudang extends Model
{
    use HasFactory;

    protected $table = 'biaya_gudang';

    protected $fillable = [
        'gudang_Id_Gudang',
        'biaya_sewa',
        'biaya_listrik',
        'biaya_air',
        'tanggal_biaya',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_biaya' => 'date',
        'biaya_sewa' => 'decimal:2',
        'biaya_listrik' => 'decimal:2',
        'biaya_air' => 'decimal:2'
    ];

    // Relasi ke Gudang
    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_Id_Gudang', 'Id_Gudang');
    }

    // Accessor untuk total biaya
    public function getTotalBiayaAttribute()
    {
        return $this->biaya_sewa + $this->biaya_listrik + $this->biaya_air;
    }
}
