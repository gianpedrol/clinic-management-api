<?php

namespace Database\Seeders;

use App\Models\Pacote;
use App\Models\Procedimento;
use Illuminate\Database\Seeder;

class ProcedimentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insert Procedimento records with pacote_id set to null
        Procedimento::create([
            'nome_procedimento' => 'Procedimento 1 (No Pacote)',
            'descricao' => 'Description for Procedimento 1 (No Pacote)',
            'porcentagem_clinica' => 20.0,
        ]);

        Procedimento::create([
            'nome_procedimento' => 'Procedimento 2 (No Pacote)',
            'descricao' => 'Description for Procedimento 2 (No Pacote)',
            'porcentagem_clinica' => 15.0,
        ]);

        // Insert Procedimento records associated with Pacotes
        $pacote1 = Pacote::find(1); // Replace '1' with the actual ID of 'Pacote 1'
        $pacote2 = Pacote::find(2);

        Procedimento::create([
            'nome_procedimento' => 'Procedimento 3 (In Pacote 1)',
            'pacote_id' => $pacote1->id,
            'descricao' => 'Description for Procedimento 3 (In Pacote 1)',
            'porcentagem_clinica' => 10.0,
        ]);

        Procedimento::create([
            'nome_procedimento' => 'Procedimento 4 (In Pacote 2)',
            'pacote_id' => $pacote2->id,
            'descricao' => 'Description for Procedimento 4 (In Pacote 2)',
            'porcentagem_clinica' => 5.0,
        ]);

        Procedimento::create([
            'nome_procedimento' => 'Procedimento 5 (No Pacote)',
            'descricao' => 'Description for Procedimento 5 (No Pacote)',
            'porcentagem_clinica' => 25.0,
        ]);

        Procedimento::create([
            'nome_procedimento' => 'Procedimento 6 (No Pacote)',
            'descricao' => 'Description for Procedimento 6 (No Pacote)',
            'porcentagem_clinica' => 18.0,
        ]);

        Procedimento::create([
            'nome_procedimento' => 'Procedimento 7 (No Pacote)',
            'descricao' => 'Description for Procedimento 7 (No Pacote)',
            'porcentagem_clinica' => 30.0,
        ]);

        // Add more procedures with pacote_id set to null
        // ...

        // Insert more procedures associated with Pacotes
        $pacote3 = Pacote::find(3); // Replace '1' with the actual ID of 'Pacote 1'
        $pacote4 = Pacote::find(4);

        Procedimento::create([
            'nome_procedimento' => 'Procedimento 8 (In Pacote 3)',
            'pacote_id' => $pacote3->id,
            'descricao' => 'Description for Procedimento 8 (In Pacote 3)',
            'porcentagem_clinica' => 12.0,
        ]);

        Procedimento::create([
            'nome_procedimento' => 'Procedimento 9 (In Pacote 4)',
            'pacote_id' => $pacote4->id,
            'descricao' => 'Description for Procedimento 9 (In Pacote 4)',
            'porcentagem_clinica' => 8.0,
        ]);
    }
}
