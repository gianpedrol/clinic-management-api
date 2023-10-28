<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfissionalAgendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profissional_agendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procedimento_id')->references('id')->on('procedimentos')->onUpdate('no action')->onDelete('cascade');
            $table->unsignedBigInteger('profissional_id')->references('id')->on('profissionais')->onUpdate('no action')->onDelete('cascade');
            $table->unsignedBigInteger('atendimento_id')->references('id')->on('atendimentos')->onUpdate('no action')->onDelete('cascade');
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');

            $table->tinyInteger('status')->default(1)->comment('1: Agendado, 2: Confirmado, 3: Cancelado, 4: Finalizado');
            $table->boolean('disponivel')->default(true);
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
        Schema::dropIfExists('agendas');
    }
}
