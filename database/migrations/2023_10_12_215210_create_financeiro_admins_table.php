<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinanceiroAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financeiro_admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('atendimento_id');
            $table->float('value', 8, 2);
            $table->boolean('receipt')->default(false);
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
        Schema::dropIfExists('financeiro_admins');
    }
}
