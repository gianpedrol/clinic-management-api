<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAtendimentoServicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atendimento_servicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('atendimento_id');
            $table->unsignedBigInteger('profissional_id');
            $table->unsignedBigInteger('servico_id');
            $table->dateTime('data_hora_atendimento');
            $table->float('preco_estimado_servico');
            $table->integer('discount')->default(0);
            $table->float('preco_total_servico');
            $table->foreign('profissional_id')->references('id')->on('profissionals')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('servico_id')->references('id')->on('servicos')->onUpdate('no action')->onDelete('cascade');
            $table->foreign('atendimento_id')->references('id')->on('atendimentos')->onUpdate('no action')->onDelete('cascade');
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
        Schema::dropIfExists('atendimento_servicos');
    }
}
