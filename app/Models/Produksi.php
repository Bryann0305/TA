<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksi'; 
    protected $primaryKey = 'Id_Produksi';
    public $timestamps = false;

    protected $fillable = [
        'Hasil_Produksi',
        'Status',
        'Tanggal_Produksi',
        'Keterangan',
        'Jumlah_Berhasil',
        'Jumlah_Gagal',
        'bahan_baku_Id_Bahan',
        'pesanan_produksi_Id_Pesanan',
        'penjadwalan_Id_Jadwal',
        'bill_of_material_Id_bill_of_material',
        'production_order_id',
    ];

    // Accessor status realtime
    public function getRealtimeStatusAttribute()
    {
        $today = now()->toDateString();

        if ($this->Status === 'completed') return 'completed';
        if ($today < $this->Tanggal_Produksi) return 'planned';
        if ($today == $this->Tanggal_Produksi) return 'current';
        return 'overdue';
    }

    // Relasi
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id', 'id');
    }

    public function pesananProduksi()
    {
        return $this->belongsTo(PesananProduksi::class, 'pesanan_produksi_Id_Pesanan', 'Id_Pesanan');
    }

    public function penjadwalan()
    {
        return $this->belongsTo(Penjadwalan::class, 'penjadwalan_Id_Jadwal', 'Id_Jadwal');
    }

    public function billOfMaterial()
    {
        return $this->belongsTo(BillOfMaterial::class, 'bill_of_material_Id_bill_of_material', 'Id_bill_of_material');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'bahan_baku_Id_Bahan', 'Id_Bahan');
    }

    public function gagalProduksi()
    {
        return $this->hasMany(GagalProduksi::class, 'produksi_Id_Produksi', 'Id_Produksi');
    }

    public function details()
    {
        return $this->hasMany(ProduksiDetail::class, 'produksi_id', 'Id_Produksi');
    }
}
