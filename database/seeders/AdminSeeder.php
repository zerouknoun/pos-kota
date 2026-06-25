<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek agar tidak terjadi error duplikat jika di-run dua kali
        if (!User::where('email', 'admin@kopitakalar.com')->exists()) {
            User::create([
                'name' => 'Fadlika Rahman',
                'email' => 'admin@kopitakalar.com',
                'password' => Hash::make('master123'),
                'role' => 'admin',
            ]);
        }
    }
}