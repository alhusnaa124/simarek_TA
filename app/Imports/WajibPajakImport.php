<?php

namespace App\Imports;

use App\Models\Pbb;
use App\Models\User;
use App\Models\WajibPajak;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Exception;

class WajibPajakImport implements ToModel, WithHeadingRow
{
    private $petugasIds = [];
    private $importedCount = 0;

    public function __construct()
    {
        $this->validatePetugasData();

        $this->petugasIds = [
            'Petugas 1' => User::where('label_petugas', 'Petugas 1')->value('id'),
            'Petugas 2' => User::where('label_petugas', 'Petugas 2')->value('id'),
            'Petugas 3' => User::where('label_petugas', 'Petugas 3')->value('id'),
            'Petugas 4' => User::where('label_petugas', 'Petugas 4')->value('id'),
            'Petugas 5' => User::where('label_petugas', 'Petugas 5')->value('id'),
        ];
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    private function validatePetugasData()
    {
        $required = ['Petugas 1', 'Petugas 2', 'Petugas 3', 'Petugas 4', 'Petugas 5'];
        $missing = [];

        foreach ($required as $label) {
            if (!User::where('label_petugas', $label)->exists()) {
                $missing[] = $label;
            }
        }

        if (!empty($missing)) {
            throw new Exception('Data petugas belum lengkap: ' . implode(', ', $missing));
        }
    }

    public function model(array $row)
    {
        $row = collect($row)->mapWithKeys(function ($v, $k) {
            $key = strtolower(str_replace([' ', '.'], '_', $k));
            return [$key => $v];
        });

        if (empty($row['no']) || empty($row['pajak_terhutang'])) {
            return null;
        }

        $alamatOp = strtolower($row['alamat_op'] ?? '');
        $alamatWp = strtolower($row['alamat_wp'] ?? '');
        $namaWp = strtolower($row['nama_wp'] ?? '');

        $idPetugas = null;

        if (
            str_contains($namaWp, 'bengkok') || str_contains($namaWp, 'bk') ||
            str_contains($namaWp, 'tanah desa') || str_contains($namaWp, 'tanah kemakmuran') ||
            str_contains($namaWp, 'makam') || str_contains($namaWp, 'bondo')
        ) {
            $idPetugas = $this->petugasIds['Petugas 5'];
        }

        if (!$idPetugas) {
            if (str_contains($alamatWp, 'kecepit') || str_contains($alamatOp, 'kecepit')) {
                $idPetugas = $this->petugasIds['Petugas 1'];
            } elseif (str_contains($alamatWp, 'kedungpandan') || str_contains($alamatOp, 'kedungpandan')) {
                $idPetugas = $this->petugasIds['Petugas 2'];
            } elseif (str_contains($alamatWp, 'emprak') || str_contains($alamatOp, 'emprak')) {
                $idPetugas = $this->petugasIds['Petugas 3'];
            } elseif (str_contains($alamatWp, 'penunggalan') || str_contains($alamatOp, 'penunggalan')) {
                $idPetugas = $this->petugasIds['Petugas 4'];
            }
        }

        $nopSawah = ['17002', '17003', '17004', '17005', '17007', '17009', '17012', '17013'];

        $wajibPajak = WajibPajak::firstOrCreate([
            'nama_wp'   => $row['nama_wp'] ?? '',
            'alamat_wp' => $row['alamat_wp'] ?? '',
        ], [
            'id_petugas' => $idPetugas,
        ]);

        if ($idPetugas && $wajibPajak->id_petugas !== $idPetugas) {
            $wajibPajak->update(['id_petugas' => $idPetugas]);
        }

        //Mengambil 5 karakter, mulai dari index ke-8
        $kodeNop = substr(trim($row['nop']), 8, 5);
        $jenisTanah = in_array($kodeNop, $nopSawah) ? 'Sawah' : 'Darat';

        $nop = trim($row['nop']);
        $tahun = $row['tahun'] ?? date('Y');

        $exists = Pbb::where('nop', $nop)->where('tahun', $tahun)->exists();

        if (!$exists) {
            Pbb::create([
                'no_wp'           => $wajibPajak->id,
                'nop'             => $nop,
                'alamat_op'       => $row['alamat_op'] ?? '',
                'luas_bgn'        => $row['luas_bgn'] ?? 0,
                'luas_tnh'        => floatval($row['luas_tnh'] ?? 0),
                'tahun'           => $tahun,
                'jenis'           => $jenisTanah,
                'dharma_tirta'    => ($jenisTanah === 'Sawah') ? (floatval($row['luas_tnh']) * 0.07 * 202.0) : 0,
                'pajak_terhutang' => $row['pajak_terhutang'] ?? 0,

            ]);

            $this->importedCount++;
        }

        return $wajibPajak;
    }
}
