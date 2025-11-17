<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'nama' => 'Admin Desa',
                'alamat' => 'Kantor Desa',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('admin123'),
                'role' => 'Admin'
            ],
        //     [
        //         'username' => 'petugas1',
        //         'nama' => 'Petugas 1',
        //         'alamat' => 'Dusun A',
        //         'email' => 'petugas1@gmail.com',
        //         'password' => Hash::make('petugas123'),
        //         'role' => 'Petugas'
        //     ],
        //     [
        //         'username' => 'petugas2',
        //         'nama' => 'Petugas 2',
        //         'alamat' => 'Dusun B',
        //         'email' => 'petugas2@gmail.com',
        //         'password' => Hash::make('petugas123'),
        //         'role' => 'Petugas'
        //     ],
        //     [
        //         'username' => 'petugas3',
        //         'nama' => 'Petugas 3',
        //         'alamat' => 'Dusun C',
        //         'email' => 'petugas3@gmail.com',
        //         'password' => Hash::make('petugas123'),
        //         'role' => 'Petugas'
        //     ],
        //     [
        //         'username' => 'petugas4',
        //         'nama' => 'Petugas 4',
        //         'alamat' => 'Dusun D',
        //         'email' => 'petugas4@gmail.com',
        //         'password' => Hash::make('petugas123'),
        //         'role' => 'Petugas'
        //     ],
        //     [
        //         'username' => 'bendahara',
        //         'nama' => 'Bendahara Desa',
        //         'alamat' => 'Kantor Desa',
        //         'email' => 'bendahara@gmail.com',
        //         'password' => Hash::make('bendahara123'),
        //         'role' => 'Bendahara'
        //     ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
