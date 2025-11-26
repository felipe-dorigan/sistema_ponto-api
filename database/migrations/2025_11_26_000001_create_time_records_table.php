<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->time('entry_time')->nullable();
            $table->time('exit_time')->nullable();
            $table->time('lunch_start')->nullable();
            $table->time('lunch_end')->nullable();
            $table->integer('worked_minutes')->default(0);
            $table->integer('expected_minutes')->default(480); // 8 horas = 480 minutos
            $table->text('notes')->nullable();
            
            // Timestamps de quando cada campo foi registrado
            $table->timestamp('entry_time_recorded_at')->nullable(); // Quando a entrada foi registrada
            $table->timestamp('exit_time_recorded_at')->nullable(); // Quando a saída foi registrada
            $table->timestamp('lunch_start_recorded_at')->nullable(); // Quando o início do almoço foi registrado
            $table->timestamp('lunch_end_recorded_at')->nullable(); // Quando o fim do almoço foi registrado
            
            $table->timestamps();

            // Índice único para garantir um registro por usuário por dia
            $table->unique(['user_id', 'date']);
            
            // Índices para melhorar performance de consultas
            $table->index('date');
            $table->index(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_records');
    }
};
