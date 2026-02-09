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
        Schema::table('absences', function (Blueprint $table) {
            $table->enum('impact_type', ['discount', 'neutral', 'bonus'])
                ->default('discount')
                ->after('status')
                ->comment('Impacto no banco de horas: discount=desconta, neutral=não afeta, bonus=adiciona');

            // Índice para relatórios de banco de horas
            $table->index(['user_id', 'impact_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'impact_type']);
            $table->dropColumn('impact_type');
        });
    }
};
