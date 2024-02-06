<?php

namespace App\Http\Controllers\Agenda;

use App\Http\Controllers\Controller;
use App\Models\HorarioTrabalho;
use App\Models\ProfissionalAgenda;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendaController extends Controller
{
    public function listarDiasHorariosDisponiveis(Request $request, $id)
    {
        $dataEspecifica = $request->data;


        if (empty($dataEspecifica) || !Carbon::createFromFormat('Y-m-d', $dataEspecifica)->isValid()) {
            return response()->json(['message' => 'Data específica inválida.'], 400);
        }

        $usuarioId = User::findOrFail($id); // Obtém o ID do usuário autenticado

        // Verifique se existem agendamentos para o usuário na data especificada
        $agendamentos = ProfissionalAgenda::where('profissional_id', $usuarioId->id)
            ->whereDate('data', $dataEspecifica)

            ->get();

        $horariosDisponiveis = [];

        $horariosDoDia = $this->horariosDisponiveisNoDia($usuarioId->id, Carbon::createFromFormat('Y-m-d', $dataEspecifica), $agendamentos);

        if (!empty($horariosDoDia)) {
            $horariosDisponiveis[] = [
                'data' => $dataEspecifica,
                'dia_semana' => Carbon::createFromFormat('Y-m-d', $dataEspecifica)->isoFormat('dddd'),
                'horarios' => $horariosDoDia,
            ];
        }

        return response()->json($horariosDisponiveis, 200);
    }


    private function horariosDisponiveisNoDia($usuarioId, $data, $agendamentos)
    {
        $registro = HorarioTrabalho::where('user_id', $usuarioId)
            ->where('dia_semana', $data->isoFormat('dddd'))

            ->first();

        if (!$registro) {
            return [];
        }


        $horaInicio = Carbon::parse($registro->hora_inicio);
        $horaFim = Carbon::parse($registro->hora_fim);

        // Crie um array para rastrear os horários já agendados
        $horariosAgendados = [];

        foreach ($agendamentos as $agendamento) {
            $horaInicioAgendada = Carbon::parse($agendamento->hora_inicio);
            $horaFimAgendada = Carbon::parse($agendamento->hora_fim);

            while ($horaInicioAgendada < $horaFimAgendada) {
                $horariosAgendados[] = $horaInicioAgendada->format('H:i');
                $horaInicioAgendada->addHour();
            }
        }

        $horariosDisponiveis = [];

        // Itere pelos horários do registro de trabalho
        while ($horaInicio < $horaFim) {
            $horaAtualFormatada = $horaInicio->format('H:i');

            if (!in_array($horaAtualFormatada, $horariosAgendados)) {
                $horariosDisponiveis[] = $horaAtualFormatada;
            }

            $horaInicio->addHour();
        }

        return $horariosDisponiveis;
    }

    public function definirDisponibilidade(Request $request)
    {
        $request->validate([
            'dias' => 'required|array',
            'horarios' => 'required|array',
        ]);

        $dias = $request->input('dias');
        $horarios = $request->input('horarios');
        $profissionalId = auth()->user()->id; // ou qualquer método que você use para obter o ID do usuário logado

        // Limpa as entradas existentes para o profissional
        ProfissionalAgenda::where('profissional_id', $profissionalId)->delete();

        // Cria as novas entradas com os dias e horários indisponíveis
        foreach ($dias as $dia) {
            foreach ($horarios as $horario) {
                ProfissionalAgenda::create([
                    'motivo' => "",
                    'profissional_id' => $profissionalId,
                    'data' => $dia,
                    'hora_inicio' => $horario['inicio'],
                    'hora_fim' => $horario['fim'],
                    'disponivel' => false,
                    // Adicione outros campos necessários
                ]);
            }
        }

        return response()->json(['message' => 'Disponibilidade definida com sucesso']);
    }
}
