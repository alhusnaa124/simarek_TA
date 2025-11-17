<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pbb extends Model
{
    use HasFactory;

    protected $table = 'pbb';

    protected $fillable = [
        'nop',
        'no_wp',
        'alamat_op',
        'luas_tnh',
        'luas_bgn',
        'pajak_terhutang',
        'jenis',
        'dharma_tirta',
        'tahun',
    ];

    public function wajibPajak()
    {
        return $this->belongsTo(WajibPajak::class, 'no_wp', 'id');
    }

    public function formulir()
    {
        return $this->belongsToMany(FormulirTarikan::class, 'formulir_pbb', 'id_pbb', 'id_formulir')
            ->withPivot('tahun');
    }

    public function isLunas()
    {
        try {
            return $this->formulir()
                ->wherePivot('tahun', $this->tahun)
                ->whereNotNull('tgl_bayar')
                ->exists();
        } catch (\Throwable $e) {
            return false; // Anggap belum lunas jika relasi gagal
        }
    }
}
