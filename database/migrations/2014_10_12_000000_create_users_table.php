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
            $table->integer('role_id')->comment(['1' => 'ADMIN', '2' => 'PROFISSIONAL', '3' => 'CLIENTE']);
            $table->bigInteger('whatsapp');
            $table->string('endereço');
            $table->string('estado');
            $table->string('pais');
            $table->string('cidade');
            $table->date('birthdate');
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
