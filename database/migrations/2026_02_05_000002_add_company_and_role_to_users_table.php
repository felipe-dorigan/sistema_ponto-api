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
        if (!Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('companies')
                    ->onDelete('cascade')
                    ->comment('NULL para usuários Master');

                $table->index('company_id');
            });
        }

        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['master', 'admin', 'user'])
                    ->default('user')
                    ->after('password')
                    ->comment('master: super admin | admin: gestor da empresa | user: funcionário');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->index(['company_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'company_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropIndex(['users_company_id_index']);
                $table->dropIndex(['users_company_id_role_index']);
                $table->dropColumn(['company_id']);
            });
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['role']);
            });
        }
    }
};
