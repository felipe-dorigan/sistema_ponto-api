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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cnpj', 14)->unique()->comment('CNPJ sem pontuação');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 2)->nullable()->comment('UF - Sigla do estado');
            $table->string('zip_code', 8)->nullable()->comment('CEP sem pontuação');
            $table->integer('max_users')->default(50)->comment('Limite de usuários permitidos');
            $table->boolean('active')->default(true)->comment('Empresa ativa no sistema');
            $table->timestamps();

            // Índices para melhorar performance
            $table->index('cnpj');
            $table->index('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
