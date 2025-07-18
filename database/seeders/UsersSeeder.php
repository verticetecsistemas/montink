<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => 'Usuário ' . $i,
                'email' => 'usuario' . $i . '@exemplo.com',
                'password' => Hash::make('senha123'),
            ]);
        }
    }
}