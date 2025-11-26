<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Criar o schema logs se não existir
        DB::statement('CREATE SCHEMA IF NOT EXISTS logs');
        
        // Verificar se a tabela já existe antes de criar
        if (!Schema::hasTable('logs.api_logs')) {
            Schema::create('logs.api_logs', function (Blueprint $table) {
                $table->id();
                $table->string('level', 20); // error, warning, notice
                $table->string('url');
                $table->string('method', 10);
                $table->ipAddress('ip')->nullable();
                $table->json('input')->nullable();
                $table->string('exception')->nullable();
                $table->text('message')->nullable();
                $table->longText('trace')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('logs.api_logs');
        // Verificar se o schema existe e está vazio antes de removê-lo
        $tablesInSchema = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'logs'");
        if (empty($tablesInSchema)) {
            DB::statement('DROP SCHEMA IF EXISTS logs');
        }
    }
};
