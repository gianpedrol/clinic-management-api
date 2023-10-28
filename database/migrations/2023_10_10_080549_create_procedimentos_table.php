<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcedimentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('procedimentos', function (Blueprint $table) {
            $table->id();
            $table->string("nome_procedimento");
            $table->unsignedBigInteger('pacote_id')->nullable();
            $table->string('descricao');
            $table->integer('porcentagem_clinica')->default(0);
            $table->integer('duracao_sessao')->default(60);
            $table->timestamps();
            $table->foreign('pacote_id')->references('id')->on('pacotes')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('procedimentos');
    }
}
