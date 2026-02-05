<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MasterUserSeeder extends Seeder
{
    /**
     * Seed do usuário Master inicial
     * 
     * Cria o primeiro usuário Master do sistema que terá acesso total
     * para cadastrar empresas e gerenciar todo o sistema.
     */
    public function run(): void
    {
        // Verifica se já existe um usuário master
        if (User::where('role', 'master')->exists()) {
            $this->command->info('Usuário Master já existe. Pulando seed...');
            return;
        }

        $master = User::create([
            'company_id' => null, // Master não pertence a nenhuma empresa
            'name' => 'Administrador Master',
            'email' => 'master@sistemaponto.com',
            'password' => Hash::make('Master@123'), // Alterar em produção!
            'role' => 'master',
            'daily_work_hours' => 8,
            'lunch_duration' => 60,
            'active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ Usuário Master criado com sucesso!');
        $this->command->info('📧 Email: ' . $master->email);
        $this->command->warn('🔐 Senha: Master@123 (ALTERAR EM PRODUÇÃO!)');
    }
}
