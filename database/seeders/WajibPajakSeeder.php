<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WajibPajak;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WajibPajakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $petugasIds = [2, 3, 4, 5];
    //     $i = 0;

    //     WajibPajak::whereNull('id_petugas')->get()->each(function ($wp) use ($petugasIds, &$i) {
    //         $wp->id_petugas = $petugasIds[$i % count($petugasIds)];
    //         $wp->save();
    //         $i++;
    //     });
    // }

    public function run(): void
    {
        // Ambil semua ID user dengan role 'Petugas'
        $petugasIds = User::where('role', 'Petugas')->pluck('id')->toArray();

        $i = 0;
        WajibPajak::whereNull('id_petugas')->get()->each(function ($wp) use ($petugasIds, &$i) {
            $wp->id_petugas = $petugasIds[$i % count($petugasIds)];
            $wp->save();
            $i++;
        });
    }
}
