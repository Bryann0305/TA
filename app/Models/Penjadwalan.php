<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjadwalan extends Model
{
    use HasFactory;

    protected $table = 'penjadwalan';
    protected $primaryKey = 'Id_Jadwal';
    public $timestamps = false;

    protected $fillable = [
        'Tanggal_Mulai',
        'Tanggal_Selesai',
        'Status',
        'production_order_id',
    ];

    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }
}
    