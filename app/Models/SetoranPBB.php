<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SetoranPBB extends Model
{
    use HasFactory;

    protected $table = 'setoran_pbb';

    protected $fillable = [
        'id_petugas',
        'id_verifikator',
        'tanggal_setor',
        'jumlah_setor',
        'status',
        'bukti',
        'catatan',
        'tahun',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'id_verifikator');
    }

    public function formulirTarikans()
    {
        return $this->hasMany(FormulirTarikan::class, 'id_setoran');
    }
}
