<?php

use App\Http\Controllers\Atendimento\AtendimentoController;
use App\Http\Controllers\Auth\AuthController;
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

    Route::post('registrar/atendimento', [AtendimentoController::class, 'criarAtendimento']);
    Route::post('atendimento/cancelar/{id}', [AtendimentoController::class, 'cancelarAtendimento']);
    Route::post('atendimento/finalizar/{id}', [AtendimentoController::class, 'finalizarAtendimento']);
    Route::post('atendimento/finalizar-procedimento', [AtendimentoController::class, 'finalizarProcedimentoProfissional']);
    Route::get('agendamentos/profissional', [AtendimentoController::class, 'listarAgendamentosProfissional']);
    Route::get('atendimentos/clinicas', [AtendimentoController::class, 'listarAtendimentosClinicas']);
    Route::get('atendimento/detalhes/{id}', [AtendimentoController::class, 'detalhesAtendimento']);
});
