<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjadwalan extends Model
{
    protected $table = 'penjadwalan';
    protected $primaryKey = 'Id_Jadwal';
    public $timestamps = false;

    protected $fillable = [
        'Tanggal_Mulai',
        'Tanggal_Selesai',
        'Status'
    ];

    public function produksi()
    {
        return $this->hasMany(Produksi::class, 'penjadwalan_Id_Jadwal', 'Id_Jadwal');
    }
}
