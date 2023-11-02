<?php

use App\Http\Controllers\Agenda\AgendaController;
use App\Http\Controllers\Atendimento\AtendimentoController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Pacote\PacoteController;
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

Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout']);



Route::middleware('auth:api')->group(function () {

    /**Rotas de Usurario */

    Route::post('register/user', [UserController::class, 'registerUser']);
    Route::get('lista/usuarios', [UserController::class, 'listUsers']);
    Route::get('detalhes/usuario/{id}', [UserController::class, 'showUser']);
    Route::delete('delete/user/{id}', [UserController::class, 'deleteUser']);

    Route::get('profissional/{id}/agenda', [AgendaController::class, 'listarDiasHorariosDisponiveis']);

    /**Rotas de atendimento */


    Route::post('registrar/atendimento', [AtendimentoController::class, 'criarAtendimento']);
    Route::post('atendimento/cancelar/{id}', [AtendimentoController::class, 'cancelarAtendimento']);
    Route::post('atendimento/finalizar/{id}', [AtendimentoController::class, 'finalizarAtendimento']);
    Route::post('atendimento/finalizar-procedimento', [AtendimentoController::class, 'finalizarProcedimentoProfissional']);
    Route::get('agendamentos/profissional', [AtendimentoController::class, 'listarAgendamentosProfissional']);
    Route::get('atendimentos/clinicas', [AtendimentoController::class, 'listarAtendimentosClinicas']);
    Route::get('atendimento/detalhes/{id}', [AtendimentoController::class, 'detalhesAtendimento']);


    /** Rotas de Serviço */
    Route::get('lista/usuarios/procedimento/{id}', [UserController::class, 'listUsuariosProcedimentos']);

    Route::post('adicionar/servico', [PacoteController::class, 'createService']);
    Route::get('lista/pacotes', [PacoteController::class, 'listPacotes']);
    Route::get('lista/procedimentos', [PacoteController::class, 'listaProcedimentos']);
    Route::get('detalhe/pacote/{id}', [PacoteController::class, 'showPacote']);
    Route::get('detalhe/procedimento/{id}', [PacoteController::class, 'showProcedimento']);
    Route::put('atualizar/servico/{id}', [PacoteController::class, 'updateServico']);
});
