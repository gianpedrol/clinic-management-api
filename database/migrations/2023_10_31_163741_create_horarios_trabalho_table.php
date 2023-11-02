<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHorariosTrabalhoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios_trabalho', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Chave estrangeira para o usuário
            $table->string('dia_semana'); // Dia da semana (por exemplo, segunda, terça, etc.)
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('horarios_trabalho');
    }
}
