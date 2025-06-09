<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{

    protected $table = 'user';
    protected $primaryKey = 'Id_User';
    public $timestamps = false; // jika tidak ada kolom created_at/updated_at

    protected $fillable = [
        'Nama', 'Email', 'Password', 'Role', 'No_telp', 'Alamat', 'Status'
    ];

    protected $hidden = [
        'Password',
    ];

    // Jika password field Anda bernama 'Password' (huruf besar P)
    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function PesananProduksi()
    {
        return $this->hasMany(PesananProduksi::class, 'Id_User', 'Id_User');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'Id_User', 'Id_User');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'Id_User', 'Id_User');
    }


}