<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';
    protected $primaryKey = 'Id_Pembelian';
    public $timestamps = false; // kalau tidak ada created_at dan updated_at

    protected $fillable = [
        'Total_Biaya',
        'Tanggal_Pemesanan',
        'Tanggal_Kedatangan',
        'Status_Pembayaran',
        'Metode_Pembayaran',
        'Nama_Barang',
        'user_Id_User',
        'supplier_Id_Supplier',
    ];

    protected $casts = [
        'Nama_Barang' => 'array', // supaya JSON otomatis di-convert ke array
        'Tanggal_Pemesanan' => 'datetime',
        'Tanggal_Kedatangan' => 'datetime',
    ];

    // Relasi ke Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_Id_Supplier', 'Id_Supplier');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_Id_User', 'id');
    }

    public function detailPembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelian_Id_Pembelian', 'Id_Pembelian');
    }
}
