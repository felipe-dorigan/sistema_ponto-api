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
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password'); // admin ou user
            $table->integer('daily_work_hours')->default(8)->after('role'); // horas de trabalho diárias (em horas)
            $table->integer('lunch_duration')->default(60)->after('daily_work_hours'); // duração do almoço em minutos
            $table->boolean('active')->default(true)->after('lunch_duration'); // usuário ativo no sistema
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'daily_work_hours', 'lunch_duration', 'active']);
        });
    }
};
