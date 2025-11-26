<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SistemaDePontoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário admin
        $admin = \App\Models\User::create([
            'name' => 'Admin Sistema',
            'email' => 'admin@sistema.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'daily_work_hours' => 8
        ]);

        // Criar usuários colaboradores
        $user1 = \App\Models\User::create([
            'name' => 'João Silva',
            'email' => 'joao@sistema.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'daily_work_hours' => 8
        ]);

        $user2 = \App\Models\User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@sistema.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'daily_work_hours' => 6
        ]);

        // Criar alguns registros de ponto
        \App\Models\TimeRecord::create([
            'user_id' => $user1->id,
            'date' => now()->subDays(2),
            'entry_time' => now()->subDays(2)->setTime(8, 0),
            'exit_time' => now()->subDays(2)->setTime(18, 0),
            'lunch_start' => now()->subDays(2)->setTime(12, 0),
            'lunch_end' => now()->subDays(2)->setTime(13, 0),
            'worked_minutes' => 540, // 9 horas
            'expected_minutes' => 480, // 8 horas
            'notes' => 'Hora extra',
            'entry_time_recorded_at' => now()->subDays(2)->setTime(8, 2),
            'exit_time_recorded_at' => now()->subDays(2)->setTime(18, 1),
            'lunch_start_recorded_at' => now()->subDays(2)->setTime(12, 0),
            'lunch_end_recorded_at' => now()->subDays(2)->setTime(13, 0),
        ]);

        \App\Models\TimeRecord::create([
            'user_id' => $user1->id,
            'date' => now()->subDay(),
            'entry_time' => now()->subDay()->setTime(8, 15),
            'exit_time' => now()->subDay()->setTime(17, 0),
            'lunch_start' => now()->subDay()->setTime(12, 0),
            'lunch_end' => now()->subDay()->setTime(13, 0),
            'worked_minutes' => 465, // 7h45min
            'expected_minutes' => 480, // 8 horas
            'notes' => 'Saiu mais cedo',
            'entry_time_recorded_at' => now()->subDay()->setTime(8, 15),
            'exit_time_recorded_at' => now()->subDay()->setTime(17, 0),
            'lunch_start_recorded_at' => now()->subDay()->setTime(12, 0),
            'lunch_end_recorded_at' => now()->subDay()->setTime(13, 0),
        ]);

        // Criar ausências
        \App\Models\Absence::create([
            'user_id' => $user2->id,
            'date' => now()->addDays(3),
            'start_time' => now()->addDays(3)->setTime(8, 0),
            'end_time' => now()->addDays(3)->setTime(18, 0),
            'reason' => 'Consulta médica',
            'description' => 'Consulta oftalmológica agendada',
            'status' => 'pending'
        ]);

        \App\Models\Absence::create([
            'user_id' => $user1->id,
            'date' => now()->subDays(5),
            'start_time' => now()->subDays(5)->setTime(8, 0),
            'end_time' => now()->subDays(5)->setTime(12, 0),
            'reason' => 'Assuntos pessoais',
            'description' => 'Resolver documentação bancária',
            'status' => 'approved',
            'approved_by' => $admin->id,
            'approved_at' => now()->subDays(5)->addHours(2)
        ]);

        $this->command->info('✅ Dados de exemplo criados com sucesso!');
        $this->command->info("📧 Admin: admin@sistema.com | Senha: password");
        $this->command->info("📧 User1: joao@sistema.com | Senha: password");
        $this->command->info("📧 User2: maria@sistema.com | Senha: password");
    }
}
