<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WajibPajak extends Model
{
    use HasFactory;

    protected $table = 'wajib_pajak';

    protected $fillable = [
        'nama_wp',
        'alamat_wp',
        'id_kelompok',
        'id_petugas',
        'bagian',
    ];

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas','id');
    }
    

    public function pbb()
    {
        return $this->hasMany(Pbb::class, 'no_wp', 'id');
    }
}

