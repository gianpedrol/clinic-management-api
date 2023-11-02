<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfissionalAgenda;

class ProfissionalAgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Agenda para Profissional 1
        ProfissionalAgenda::create([
            'procedimento_id' => 1,
            'profissional_id' => 2,
            'atendimento_id' => 1,
            'data' => '2023-10-31',
            'hora_inicio' => '09:00:00',
            'hora_fim' => '10:00:00',
            'status' => 1,
            'disponivel' => false,
        ]);

        // Agenda para Profissional 2
        ProfissionalAgenda::create([
            'procedimento_id' => 2,
            'profissional_id' => 4,
            'atendimento_id' => 2,
            'data' => '2023-11-01',
            'hora_inicio' => '14:00:00',
            'hora_fim' => '15:00:00',
            'status' => 1,
            'disponivel' => false,
        ]);

        // Agenda para Profissional 2
        ProfissionalAgenda::create([
            'motivo' => 'almoço',
            'procedimento_id' => null,
            'profissional_id' => 4,
            'atendimento_id' => null,
            'data' => '2023-11-01',
            'hora_inicio' => '11:00:00',
            'hora_fim' => '13:00:00',
            'status' => 1,
            'disponivel' => false,
        ]);
        // Adicione mais registros de agenda conforme necessário para outros profissionais.

    }
}
