<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;

    protected $table = 'kelompok';

    protected $fillable = [
        'nama_kelompok',
    ];

    public function wajibPajak()
    {
        return $this->hasMany(WajibPajak::class, 'id_kelompok');
    }

    public function formulirTarikan()
    {
        return $this->hasOne(FormulirTarikan::class, 'id_kelompok');
    }
}
