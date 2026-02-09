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
            $table->date('hire_date')
                ->nullable()
                ->after('email_verified_at')
                ->comment('Data de admissão do funcionário');

            // Índice para queries de aniversário de empresa, férias, etc
            $table->index('hire_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['hire_date']);
            $table->dropColumn('hire_date');
        });
    }
};
