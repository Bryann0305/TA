<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;
    // ... existing code ...
    protected $table = 'notifikasi';
    protected $primaryKey = 'Id_Notifikasi';
    public $timestamps = false;

    protected $fillable = [
        'Pesan', 'Status', 'user_Id_User'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_Id_User', 'Id_User');
    }
// ... existing code ...
}
