<?php

namespace App\Http\Controllers\Atendimento;

use App\Http\Controllers\Controller;
use App\Models\Atendimento;
use App\Models\Faturamento;
use App\Models\FinanceiroAdmin;
use App\Models\FinanceiroProfissional;
use App\Models\ProfissionalServico;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AtendimentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');

        if (!auth()->user()) {
            return response()->json(['error' => 'Unauthorized access'], 401);
        }
    }


    public function criarAtendimento(Request $request)
    {
        $data = $request->only('client_id', 'servico_id', 'profissional_id', 'convenio_id', 'data', 'hora', 'metodo_pagamento', 'descricao', 'discount');

        // Verifica se o paciente (client_id) existe como usuário e é um cliente (role_id 1)
        $paciente = User::where('id', $data['client_id'])->where('role_id', 1)->first();
        if (!$paciente) {
            return response()->json(['message' => 'Paciente não encontrado ou não é um cliente válido'], 404);
        }

        // Verifica se o profissional (profissional_id) existe como usuário e é um profissional (role_id 2)
        $profissional = User::where('id', $data['profissional_id'])->where('role_id', 2)->first();
        if (!$profissional) {
            return response()->json(['message' => 'Profissional não encontrado ou não é um profissional válido'], 404);
        }

        $servico = Servico::where('id', $data['servico_id'])->first();

        if (!$servico) {
            return response()->json(['message' => 'Serviço não encontrado ou não é um Serviço válido'], 404);
        }

        $servicoProfissional = ProfissionalServico::where("profissional_id", $profissional->id)->where('servico_id', $servico->id)->first();

        $descontoEmPorcentagem = $data['discount'];
        $precoEstimado = $servicoProfissional->price;

        if (is_numeric($descontoEmPorcentagem)) {

            $desconto = ($precoEstimado * $descontoEmPorcentagem) / 100;
            $precoTotal = $precoEstimado - $desconto;
            $valorProfissional = $precoEstimado  - $desconto - ($precoEstimado * $servico->percentual_admin / 100);
            $valorAdmin = $precoTotal * $servico->percentual_admin / 100;
        } else {
            $precoTotal = $precoEstimado;
            $valorProfissional = $precoEstimado  - ($precoEstimado * $servico->percentual_admin / 100);
            $valorAdmin = $precoTotal * $servico->percentual_admin / 100;
        }


        try {
            \DB::beginTransaction();
            // Verifique se a data está no formato Y-m-d e a hora no formato H:i
            $data = $data['data'];
            $hora = $data['hora'];

            if (Carbon::createFromFormat('Y-m-d H:i', "$data $hora", 'UTC')->format('Y-m-d H:i') === "$data $hora") {
                // A data e a hora estão no formato correto
                $atendimento = Atendimento::create([
                    'client_id' => $paciente->id,
                    'servico_id' => $servico->id,
                    'profissional_id' => $profissional->id,
                    'convenio_id' => null,
                    'data' => $data,
                    'hora' => $hora,
                    'status' => $data['status'],
                    'metodo_pagamento' => $data['metodo_pagamento'],
                    'descricao' => $data['descricao'],
                    'preco_estimado' =>  $precoEstimado,
                    'discount' => $data['discount'],
                    'preco_total' => $precoTotal,
                ]);

                FinanceiroProfissional::create([
                    'profissional_id' => $profissional->id,
                    'atendimento_id' => $atendimento->id,
                    'value' => $valorProfissional
                ]);

                FinanceiroAdmin::create([
                    'atendimento_id' => $atendimento->id,
                    'value' => $valorAdmin
                ]);
                \DB::commit();
                return response()->json(['message' => 'Atendimento agendado com sucesso', 'data' => $atendimento], 201);
            } else {
                return response()->json(['message' => 'Data ou hora incorretas'], 400);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Ocorreu um erro ao agendar o atendimento', 'error' => $e->getMessage()], 500);
        }
    }

    public function cancelarAtendimento($id)
    {

        $atendimento = Atendimento::find($id);

        if (!$atendimento) {
            return response()->json(['message' => 'Atendimento não encontrado'], 404);
        }

        try {
            \DB::beginTransaction();
            // Atualiza o status do atendimento para "Cancelado" (ou o valor apropriado)
            $atendimento->update(['status' => 2]);

            // Exclui os registros financeiros relacionados
            FinanceiroProfissional::where('atendimento_id', $atendimento->id)->delete();
            FinanceiroAdmin::where('atendimento_id', $atendimento->id)->delete();
            \DB::commit();

            return response()->json(['message' => 'Atendimento cancelado com sucesso'], 200);
        } catch (\Throwable $e) {
            \DB::rollBack();
            // Lide com exceções ou erros, se necessário
            return response()->json(['message' => 'Ocorreu um erro ao cancelar o atendimento', 'error' => $e->getMessage()], 500);
        }
    }

    public function finalizarAtendimento($id)
    {
        $atendimento = Atendimento::find($id);

        if (!$atendimento) {
            return response()->json(['message' => 'Atendimento não encontrado'], 404);
        }

        $valueAdmin = FinanceiroAdmin::where('atendimento_id', $atendimento->id)->first();

        // Obtém o mês e o ano do atendimento
        $dataAtendimento = Carbon::parse($atendimento->data);
        $mes = $dataAtendimento->month;
        $ano = $dataAtendimento->year;

        // Verifica se já existe um registro de faturamento para o mês e ano do atendimento
        $faturamento = Faturamento::where('mes', $mes)->where('ano', $ano)->first();

        try {
            \DB::beginTransaction();
            // Atualiza o status do atendimento para "Finalizado" (ou o valor apropriado)
            $atendimento->update(['status' => 3]);
            $valueAdmin->update(['receipt' => true]);

            if ($faturamento) {

                $faturamento->valor +=  $valueAdmin->value;
                $faturamento->save();
            } else {
                // Se não existir um registro de faturamento, crie um novo
                Faturamento::create([
                    'mes' => $mes,
                    'ano' => $ano,
                    'valor' =>  $valueAdmin->value,
                ]);
            }

            \DB::commit();
            return response()->json(['message' => 'Atendimento finalizado com sucesso'], 200);
        } catch (\Throwable $e) {
            \DB::rollBack();
            // Lide com exceções ou erros, se necessário
            return response()->json(['message' => 'Ocorreu um erro ao finalizar o atendimento', 'error' => $e->getMessage()], 500);
        }
    }


    public function listarAgendamentosProfissional(Request $request)
    {
        $profissionalId = auth()->user()->id; // Obtém o ID do profissional autenticado

        // Consulta os agendamentos relacionados a este profissional
        $agendamentos = Atendimento::where('profissional_id', $profissionalId)
            ->select('atendimentos.*')
            ->join('users as clientes', 'atendimentos.client_id', '=', 'clientes.id')
            ->join('servicos', 'atendimentos.servico_id', '=', 'servicos.id')
            ->get();

        return response()->json(['message' => 'Lista de agendamentos do profissional', 'data' => $agendamentos], 200);
    }


    public function listarAtendimentosClinicas(Request $request)
    {
        // Consulta os atendimentos e faz as junções necessárias para obter informações de nomes e serviços
        $atendimentos = Atendimento::select('atendimentos.*')
            ->join('users as clientes', 'atendimentos.client_id', '=', 'clientes.id')
            ->join('users as profissionais', 'atendimentos.profissional_id', '=', 'profissionais.id')
            ->join('servicos', 'atendimentos.servico_id', '=', 'servicos.id')
            ->get();

        return response()->json(['message' => 'Lista de atendimentos de clínicas', 'data' => $atendimentos], 200);
    }

    public function detalhesAtendimento($id)
    {
        // Consulta o atendimento com base no ID fornecido
        $atendimento = Atendimento::where('id', $id)
            ->select('atendimentos.*')
            ->join('users as clientes', 'atendimentos.client_id', '=', 'clientes.id')
            ->join('users as profissionais', 'atendimentos.profissional_id', '=', 'profissionais.id')
            ->join('servicos', 'atendimentos.servico_id', '=', 'servicos.id')
            ->first();

        if (!$atendimento) {
            return response()->json(['message' => 'Atendimento não encontrado'], 404);
        }

        return response()->json(['message' => 'Detalhes do atendimento', 'data' => $atendimento], 200);
    }
}
