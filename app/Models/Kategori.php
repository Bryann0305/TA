<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $primaryKey = 'Id_Kategori';
    public $timestamps = false;

    protected $fillable = [
        'Nama_Kategori', 'Status'
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'kategori_Id_Kategori', 'Id_Kategori');
    }
}
