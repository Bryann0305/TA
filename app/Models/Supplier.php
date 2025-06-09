<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $primaryKey = 'Id_Supplier';
    public $timestamps = false;

    protected $fillable = [
        'Nama_Supplier',
        'Nama_Pegawai',
        'Email',           // ← tambahkan ini!
        'Kontak',
        'Alamat',
        'Status',
    ];

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'supplier_Id_Supplier', 'Id_Supplier');
    }
}
