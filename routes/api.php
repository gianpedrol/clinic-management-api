<?php

use App\Http\Controllers\Atendimento\AtendimentoController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout',  [AuthController::class, 'logout']);
    Route::post('refresh',  [AuthController::class, 'refresh']);
    Route::post('me',  [AuthController::class, 'me']);
});


Route::post('registrar/atendimento', [AtendimentoController::class, 'criarAtendimento']);
Route::middleware('auth:api')->group(function () {

    /**Rotas de Usurario */

    Route::post('register/user', [UserController::class, 'registerUser']);
    Route::get('lista/usuarios', [UserController::class, 'listUsers']);
    Route::get('detalhes/usuario/{id}', [UserController::class, 'showUser']);
    Route::delete('delete/user/{id}', [UserController::class, 'deleteUser']);

    /**Rotas de atendimento */


    Route::post('registrar/atendimento', [AtendimentoController::class, 'criarAtendimento']);
    Route::post('atendimento/cancelar/{id}', [AtendimentoController::class, 'cancelarAtendimento']);
    Route::post('atendimento/finalizar/{id}', [AtendimentoController::class, 'finalizarAtendimento']);
    Route::post('atendimento/finalizar-procedimento', [AtendimentoController::class, 'finalizarProcedimentoProfissional']);
    Route::get('agendamentos/profissional', [AtendimentoController::class, 'listarAgendamentosProfissional']);
    Route::get('atendimentos/clinicas', [AtendimentoController::class, 'listarAtendimentosClinicas']);
    Route::get('atendimento/detalhes/{id}', [AtendimentoController::class, 'detalhesAtendimento']);


    /** Rotas de Serviço */

    Route::post('adicionar/servico', [ServiceController::class, 'createService']);
    Route::get('lista/servicos', [ServiceController::class, 'listServicos']);
    Route::get('detalhe/servico/{id}', [ServiceController::class, 'showServico']);
    Route::put('atualizar/servico/{id}', [ServiceController::class, 'updateServico']);
});
