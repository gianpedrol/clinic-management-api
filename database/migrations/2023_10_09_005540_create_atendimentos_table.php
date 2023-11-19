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
            $table->unsignedBigInteger('convenio_id')->nullable();
            $table->integer('tipo_servico')->comment('Tipo de Serviço: 1 - pacote, 2 - avulso');

            $table->unsignedBigInteger('servico_id')->nullable();
            $table->integer('status')->comment('Status: 1 - confirmado, 2 - cancelado, 3 - finalizado, 4 - agendado')->default(4);

            $table->integer('metodo_pagamento')->comment('Payment Method: 1 - Pix, 2 - Débito, 3 - Crédito, 4 - GRATUITO');

            $table->text('descricao');
            $table->float('preco_estimado', 8, 2);
            $table->integer('discount')->default(0);
            $table->float('preco_total', 8, 2);
            $table->boolean('receipt')->default(false);
            $table->timestamps();
            $table->foreign('client_id')->references('id')->on('users')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('convenio_id')->references('id')->on('convenios')->onUpdate('no action')->onDelete('cascade');
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
