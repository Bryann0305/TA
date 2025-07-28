<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $table = 'pembelian';
    protected $primaryKey = 'Id_Pembelian';
    public $timestamps = false;

    protected $fillable = [
        'Status',
        'Total_Biaya',
        'Tanggal_Pemesanan',
        'Tanggal_Kedatangan',
        'Status_Pembayaran',
        'user_Id_User',
        'supplier_Id_Supplier'
    ];

    protected $casts = [
        'Tanggal_Pemesanan' => 'datetime',
        'Tanggal_Kedatangan' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_Id_User', 'Id_User');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_Id_Supplier', 'Id_Supplier');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelian_Id_Pembelian', 'Id_Pembelian');
    }
}
