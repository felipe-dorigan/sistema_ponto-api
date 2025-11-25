<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('entry_time')->nullable(); // horário de entrada
            $table->time('exit_time')->nullable(); // horário de saída
            $table->time('lunch_start')->nullable(); // início do almoço
            $table->time('lunch_end')->nullable(); // fim do almoço
            $table->integer('worked_minutes')->default(0); // minutos trabalhados no dia
            $table->integer('expected_minutes')->default(480); // minutos esperados (8h = 480min)
            $table->text('notes')->nullable(); // observações
            $table->timestamps();

            // Índice para buscar registros por usuário e data
            $table->unique(['user_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('time_records');
    }
}
