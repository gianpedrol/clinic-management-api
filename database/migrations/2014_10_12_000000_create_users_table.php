<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('role_id')->comment('User Role: 1 - ADMIN, 2 - PROFISSIONAL, 3 - CLIENTE');
            $table->integer('status')->comment('[0 => inativo, 1 => ativo, 2 => Pendente]')->default(0);
            $table->bigInteger('whatsapp')->nullable();
            $table->string('endereço')->nullable();
            $table->string('estado')->nullable();
            $table->string('pais')->nullable();
            $table->string('cidade')->nullable();
            $table->date('birthdate')->nullable();
            $table->boolean('special_user')->default(false)->comment('usuario que não terá desconto da porcentagem da clinica');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
