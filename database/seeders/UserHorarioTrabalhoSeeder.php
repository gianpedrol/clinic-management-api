<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\HorarioTrabalho;

class UserHorarioTrabalhoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtém todos os usuários que desejam definir seus horários de trabalho
        $usuarios = User::where('role_id', 2)->get();

        // Define os horários de trabalho para cada usuário
        foreach ($usuarios as $usuario) {
            // Segunda a sexta das 9h às 19h
            $diasSemana = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

            $horaInicio = '09:00:00';
            $horaFim = '19:00:00';

            foreach ($diasSemana as $dia) {
                HorarioTrabalho::create([
                    'user_id' => $usuario->id,
                    'dia_semana' => $dia,
                    'hora_inicio' => $horaInicio,
                    'hora_fim' => $horaFim,
                ]);
            }
        }
    }
}
