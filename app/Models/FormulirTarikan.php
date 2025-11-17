<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormulirTarikan extends Model
{
    use HasFactory;

    protected $table = 'formulir_tarikan';

    protected $primaryKey = 'id'; // ⬅️ Tambahkan ini!

    public $incrementing = false; // Karena pakai string seperti FR-04
    protected $keyType = 'string'; // Agar tidak di-cast ke integer

    protected $fillable = [
        'id',
        'id_kelompok',
        'total',
        'jadwal_pembayaran',
        'status',
        'tgl_bayar',
        'bukti',
        'id_setoran',
        'id_petugas',
        'tahun',
    ];

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'id_kelompok');
    }

    public function setoran()
    {
        return $this->belongsTo(SetoranPbb::class, 'id_setoran');
    }
    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_petugas');
    }

    public function pbbs()
    {
        return $this->belongsToMany(Pbb::class, 'formulir_pbb', 'id_formulir', 'id_pbb')
            ->wherePivot('tahun', $this->tahun); // ⬅️ Ini wajib kalau kamu pakai multi-tahun
    }
}
