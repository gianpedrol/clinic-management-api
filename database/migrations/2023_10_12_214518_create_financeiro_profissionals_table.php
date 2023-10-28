<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceiroProfissionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financeiro_profissionals', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('profissional_id')->references('id')->on('users')->onUpdate('no action')->onDelete('cascade');
            $table->unsignedInteger('atendimento_id')->references('id')->on('atendimentos')->onUpdate('no action')->onDelete('cascade');
            $table->unsignedInteger('procedimento_id')->references('id')->on('procedimento')->onUpdate('no action')->onDelete('cascade');
            $table->float('value', 8, 2);
            $table->boolean('receipt')->default(false);

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
        Schema::dropIfExists('financeiro_profissionals');
    }
}
