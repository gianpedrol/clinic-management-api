<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // Call your custom seeders here
        $this->call([
            PacoteSeeder::class,
            ProcedimentoSeeder::class,
            UsersSeeder::class,
            ProfissionalProcedimentosSeeder::class,
            ProfissionalAgendaSeeder::class,
            UserHorarioTrabalhoSeeder::class
        ]);
    }
}
