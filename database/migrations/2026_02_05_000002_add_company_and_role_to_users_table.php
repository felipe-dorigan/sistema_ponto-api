<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove a coluna role antiga se existir
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            // Adiciona company_id antes do campo name
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained('companies')
                ->onDelete('cascade')
                ->comment('NULL para usuários Master');

            // Adiciona nova coluna role com enum de 3 níveis
            $table->enum('role', ['master', 'admin', 'user'])
                ->default('user')
                ->after('password')
                ->comment('master: super admin | admin: gestor da empresa | user: funcionário');

            // Índices
            $table->index('company_id');
            $table->index(['company_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropIndex(['users_company_id_index']);
            $table->dropIndex(['users_company_id_role_index']);
            $table->dropColumn(['company_id', 'role']);
        });
    }
};
