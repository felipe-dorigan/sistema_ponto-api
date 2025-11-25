<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Criar usuário admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'daily_work_hours' => 8,
        ]);

        // Criar usuários comuns de exemplo
        User::create([
            'name' => 'João Silva',
            'email' => 'joao@sistema.com',
            'password' => Hash::make('senha123'),
            'role' => 'user',
            'daily_work_hours' => 8,
        ]);

        User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@sistema.com',
            'password' => Hash::make('senha123'),
            'role' => 'user',
            'daily_work_hours' => 6,
        ]);
    }
}
