<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtendimentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atendimentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('servico_id');
            $table->unsignedBigInteger('profissional_id');
            $table->unsignedBigInteger('convenio_id')->nullable();
            $table->date('data');
            $table->time('hora');
            $table->integer('status')->comment(['1' => 'confirmado', '2' => 'cancelado', '3' => 'finalizado']);
            $table->integer('metodo_pagamento')->comment(['1' => 'Pix', '2' => 'Débito', '3' => 'Crédito']);
            $table->text('descricao');
            $table->float('preco_estimado', 8, 2);
            $table->integer('discount')->default(0);
            $table->float('preco_total', 8, 2);
            $table->foreign('servico_id')->references('id')->on('servicos')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('profissional_id')->references('id')->on('users')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('users')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('convenio_id')->references('id')->on('convenios')->onUpdate('no action')->onDelete('cascade');
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
        Schema::dropIfExists('atendimentos');
    }
}
