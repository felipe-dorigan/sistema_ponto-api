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
        Schema::create('time_record_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_record_id')
                ->constrained('time_records')
                ->onDelete('cascade')
                ->comment('Registro de ponto a ser ajustado');

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Usuário que solicitou o ajuste');

            $table->enum('field_to_change', [
                'entry_time',
                'exit_time',
                'lunch_start',
                'lunch_end',
                'date',
                'notes'
            ])->comment('Campo que será alterado');

            $table->text('current_value')->nullable()->comment('Valor atual do campo');
            $table->text('requested_value')->comment('Novo valor solicitado');
            $table->text('reason')->comment('Justificativa da solicitação');

            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->comment('Status da solicitação');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Administrador que revisou a solicitação');

            $table->timestamp('reviewed_at')->nullable()->comment('Data da revisão');
            $table->text('admin_notes')->nullable()->comment('Observações do administrador');

            $table->timestamps();

            // Índices para melhorar performance de consultas
            $table->index('time_record_id');
            $table->index('user_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['time_record_id', 'status']);
            $table->index('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_record_adjustments');
    }
};
