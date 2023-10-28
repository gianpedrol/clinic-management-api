<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcedimentoAtendimentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedimento_atendimentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('atendimento_id');
            $table->unsignedBigInteger('procedimento_id');
            $table->unsignedBigInteger('profissional_id');
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->tinyInteger('status')->default(1)->comment('1: Agendado, 2: Confirmado, 3: Cancelado, 4: Finalizado');
            $table->float('valor_procedimento_profissional', 8, 2);
            $table->timestamps();

            $table->foreign('procedimento_id')->references('id')->on('procedimentos')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('atendimento_id')->references('id')->on('atendimentos')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('procedimento_atendimentos');
    }
}
