<?php

namespace Database\Seeders;

use App\Models\Pacote;
use Illuminate\Database\Seeder;

class PacoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pacote::create([
            'id' => 1,
            'pacote' => 'Pacote 1',
            'valor' => 1000.00,
            'percentual_admin' => 20,
            'descricao' => 'Description for Pacote 1',
        ]);

        Pacote::create([
            'id' => 2,
            'pacote' => 'Pacote 2',
            'valor' => 950.00,
            'percentual_admin' => 20,
            'descricao' => 'Description for Pacote 2',
        ]);

        Pacote::create([
            'id' => 3,
            'pacote' => 'Pacote 3',
            'valor' => 1200.00,
            'percentual_admin' => 15,
            'descricao' => 'Description for Pacote 3',
        ]);

        Pacote::create([
            'id' => 4,
            'pacote' => 'Pacote 4',
            'valor' => 800.00,
            'percentual_admin' => 10,
            'descricao' => 'Description for Pacote 4',
        ]);

        // Add more Pacote records as needed
    }
}
