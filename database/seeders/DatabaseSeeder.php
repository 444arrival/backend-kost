<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        User::create([
            'name'        => 'Pengelola Kost',
            'username'    => 'admin@gmail.com',
            'password'    => Hash::make('admin123'),
            'no_hp'       => '081299998888',
            'nomor_kamar' => 'ADMIN',
            'role'        => 'admin',
        ]);
    }
}