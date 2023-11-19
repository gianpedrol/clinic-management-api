<?php

namespace App\Http\Controllers\Atendimento;

use App\Http\Controllers\Controller;
use App\Models\Atendimento;
use App\Models\Faturamento;
use App\Models\FinanceiroAdmin;
use App\Models\FinanceiroProfissional;
use App\Models\Pacote;
use App\Models\Procedimento;
use App\Models\ProcedimentoAtendimento;
use App\Models\ProfissionalAgenda;
use App\Models\ProfissionalProcedimento;
use App\Models\ProfissionalServico;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AtendimentoController extends Controller
{
    /*  public function __construct()
    {
        $this->middleware('auth:api');

        if (!auth()->user()) {
            return response()->json(['error' => 'Unauthorized access'], 401);
        }
    }*/


    public function criarAtendimento(Request $request)
    {
        $data = $request->only('client_id', 'tipo_servico', 'convenio_id', 'procedimentos', 'data', 'hora', 'metodo_pagamento', 'descricao', 'discount', 'receipt','pacote_id');

        
        // Verifica se o paciente (client_id) existe como usuário e é um cliente (role_id 1)
        $paciente = User::where('id', $data['client_id'])->first();
        if (!$paciente) {
            return response()->json(['message' => 'Paciente não encontrado ou não é um cliente válido'], 404);
        }

        if ($data['tipo_servico'] == 1) {

            $pacote = Pacote::where('id', $data['pacote_id'])->first();
            $pacote['procedimentos'] = Procedimento::where("pacote_id", $pacote->id)->get();

            if (!$pacote) {
                return response()->json(['message' => 'Serviço não encontrado ou não é um Serviço válido'], 404);
            }

            $valorTotalAtendimento = $pacote->valor;
            $percentualClinica = $pacote->percentual_admin; // Percentual da clínica
            $discount = $data['discount']; // Valor do desconto

            // Certifique-se de que o desconto não exceda o valor do pacote
            if (
                $discount > $valorTotalAtendimento
            ) {
                $discount = $valorTotalAtendimento;
            }

            // Subtrair o desconto da porcentagem da clínica
            $percentualClinica -= $discount;

            // Certifique-se de que o percentual da clínica não seja negativo
            if ($percentualClinica < 0) {
                $percentualClinica = 0; // Defina um mínimo de 0 para a porcentagem da clínica
            }

            // Calcular o valor que ficará para a clínica com base na porcentagem
            $valorClinica = ($percentualClinica / 100) * $valorTotalAtendimento;

            // Calcular o valor que ficará para o profissional (dividir o restante entre os procedimentos)
            $valorProfissional = $valorTotalAtendimento - $valorClinica;

            // Criar o Atendimento
            $atendimento = Atendimento::create([
                'client_id' => $data['client_id'],
                'tipo_servico' => $data['tipo_servico'],
                'convenio_id' => $data['convenio_id'],
                'metodo_pagamento' => $data['metodo_pagamento'],
                'descricao' => $data['descricao'],
                'discount' => $data['discount'],
                'preco_estimado' => $pacote->valor,
                'preco_total' => $pacote->valor
            ]);

            if ($data['receipt'] == true) {
                $atendimento->update(['receipt' => true]);
                // Registrar os valores no banco de dados
                FinanceiroAdmin::create([
                    'atendimento_id' => $atendimento->id,
                    'value_atendimento' => $valorTotalAtendimento,
                    'value_clinica' => $valorClinica,
                    'receipt' => $data['receipt']
                ]);

                $mesAtual = date('m');
                $anoAtual = date('Y');

                $faturamentoMensal = Faturamento::where('mes', $mesAtual)->where('ano', $anoAtual)->first();

                // Se não houver um registro para o mês e ano atuais, crie um novo
                if (!$faturamentoMensal) {
                    Faturamento::create([
                        'mes' => $mesAtual,
                        'ano' => $anoAtual,
                        'valor_total_atendimentos' => $valorTotalAtendimento,
                        'value_retido_clinica' => $valorClinica,
                    ]);
                } else {
                    // Caso contrário, atualize os valores existentes
                    $faturamentoMensal->valor_total_atendimentos += $valorTotalAtendimento;
                    $faturamentoMensal->value_retido_clinica += $valorClinica;
                    $faturamentoMensal->save();
                }
            }

            // Loop through the procedures in the request
            foreach ($data['procedimentos'] as $procedureData) {
                $date = Carbon::createFromFormat('d/m/Y', $procedureData['data'])->format('Y-m-d');
                // Create a procedure record for each procedure
                $procedure = ProcedimentoAtendimento::create([
                    'atendimento_id' => $atendimento->id,
                    'procedimento_id' => $procedureData['procedimento_id'],
                    'data' => $date,
                    'hora_inicio' => $procedureData['hora_inicio'],
                    'hora_fim' => $procedureData['hora_fim'],
                    'profissional_id' => $procedureData['profissional_id'],
                    'valor_procedimento_profissional' => $valorProfissional
                ]);

                // Create and save the agenda record for the professional
                ProfissionalAgenda::create([
                    'profissional_id' => $procedureData['profissional_id'],
                    'data' =>  $date,
                    'hora_inicio' => $procedureData['hora_inicio'],
                    'hora_fim' => $procedureData['hora_fim'],
                ]);
            }
        } else if ($data['tipo_servico'] == 2) {


            $totalAtendimento = 0;


            // Loop through the procedures in the request
            foreach ($data['procedimentos'] as $procedureData) {
                $date = Carbon::createFromFormat('d/m/Y', $procedureData['data'])->format('Y-m-d');
                $procedimento = Procedimento::where("id", $procedureData['procedimento_id'])->first();
                // Create a procedure record for each procedure

                $profissional = User::where('id',$procedureData['profissional_id'])->first();

                if($profissional->role_id != 2){
                    return response()->json(['message' => 'Não é um profissional'], 400);
                }
        
                $procedimentoProfissional = ProfissionalProcedimento::where('user_id', $profissional->id)->where('procedimento_id', $procedimento->id)->first();
        
        
                // Verificar se há um desconto, subtrair do percentual da clínica
                $percentualClinica = $procedimento->percentual_clinic;
                if (isset($data['discount'])) {
                    $percentualClinica -= $data['discount'];
                }

                // Verificar se o percentual da clínica é negativo e ajustá-lo para um mínimo de 0
                if ($percentualClinica < 0) {
                    $percentualClinica = 0;
                }

                // Calcular o valor para o profissional (preço do procedimento do profissional)
                $precoProfissional = $procedimentoProfissional->first()->price;

                // Calcular o valor que ficará para a clínica com base no percentual
                $valorClinica = ($percentualClinica / 100) * $precoProfissional;

                // Adicionar o valor do procedimento ao valor total do atendimento
                $totalAtendimento += $precoProfissional;

                            // Criar o Atendimento
            $atendimento = Atendimento::create([
                'client_id' => $data['client_id'],
                'tipo_servico' => $data['tipo_servico'],
                'convenio_id' => $data['convenio_id'],
                'metodo_pagamento' => $data['metodo_pagamento'],
                'descricao' => $data['descricao'],
                'discount' => $data['discount'],
                'preco_estimado' =>  $totalAtendimento,
                'preco_total' =>  $totalAtendimento
            ]);

                ProcedimentoAtendimento::create([
                    'atendimento_id' => $atendimento->id,
                    'procedimento_id' => $procedimento->id,
                    'data' =>  $date,
                    'hora_inicio' => $procedureData['hora_inicio'],
                    'hora_fim' => $procedureData['hora_fim'],
                    'profissional_id' => $procedureData['profissional_id'],
                    'valor_procedimento_profissional' => $precoProfissional
                ]);


                // Create and save the agenda record for the professional
                ProfissionalAgenda::create([
                    'profissional_id' => $procedureData['profissional_id'],
                    'data' =>  $date,
                    'hora_inicio' => $procedureData['hora_inicio'],
                    'hora_fim' => $procedureData['hora_fim'],
                ]);
            }

            // Atualizar o valor total do atendimento no registro de atendimento
            $percentualClinica = $procedimento->percentual_clinic;
            if (isset($data['discount'])) {
                $percentualClinica -= $data['discount'];
            }


            
            // Verificar se o percentual da clínica é negativo e ajustá-lo para um mínimo de 0
            $percentualClinica = max(0, $percentualClinica);

            $valorClinica = ($percentualClinica / 100) * $totalAtendimento;

            if ($data['receipt'] == true) {

                $atendimento->update(['receipt' => true]);


                // Criar o registro de FinanceiroAdmin
                FinanceiroAdmin::create([
                    'atendimento_id' => $atendimento->id,
                    'value_atendimento' => $totalAtendimento,
                    'value_clinica' => $valorClinica,
                    'receipt' => true,
                ]);

                // Obter o mês e o ano atuais
                $mes = date('n'); // Mês atual
                $ano = date('Y'); // Ano atual

                // Verificar se já existe um registro de faturamento para este mês e ano
                $faturamento = Faturamento::where('mes', $mes)->where('ano', $ano)->first();

                if ($faturamento) {
                    // Atualizar o registro de faturamento existente
                    $faturamento->valor_total_atendimentos += $totalAtendimento;
                    $faturamento->value_retido_clinica += $valorClinica;
                    $faturamento->save();
                } else {
                    // Criar um novo registro de faturamento
                    Faturamento::create([
                        'mes' => $mes,
                        'ano' => $ano,
                        'valor_total_atendimentos' => $totalAtendimento,
                        'value_retido_clinica' => $valorClinica,
                    ]);
                }
            }
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

        if ($atendimento->receipt == false) {
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
        } else {
            try {
                \DB::beginTransaction();
                // Atualiza o status do atendimento para "Finalizado" (ou o valor apropriado)
                $atendimento->update(['status' => 3]);

                /** CRIAR  */

                \DB::commit();
                return response()->json(['message' => 'Atendimento finalizado com sucesso'], 200);
            } catch (\Throwable $e) {
                \DB::rollBack();
                // Lide com exceções ou erros, se necessário
                return response()->json(['message' => 'Ocorreu um erro ao finalizar o atendimento', 'error' => $e->getMessage()], 500);
            }
        }
    }

    public function finalizarProcedimentoProfissional(Request $request)
    {

        $data = $request->only('profissional_id', 'atendimento_id', 'procedimento_id');

        $profissional = User::find($data['profissional_id']);
        $atendimento = Atendimento::find($data['atendimento_id']);
        $procedimentoAtendimento = ProcedimentoAtendimento::where('atendimento_id', $atendimento->id)->where('profissional_id', $profissional->id)->first();

        try {
            \DB::beginTransaction();

            FinanceiroProfissional::create([
                'profissional_id' => $profissional->id,
                'atendimento_id' => $atendimento->id,
                'procedimento_id' => $procedimentoAtendimento->procedimento_id,
                'value' => $procedimentoAtendimento->valor_procedimento_profissional
            ]);
            /** CRIAR  */

            \DB::commit();
            return response()->json(['message' => 'Procedimento finalizado com sucesso'], 200);
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
