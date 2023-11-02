<?php

namespace Database\Seeders;

use App\Models\Procedimento;
use App\Models\ProfissionalProcedimento;
use App\Models\User;
use Arr;
use Illuminate\Database\Seeder;

class ProfissionalProcedimentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            // Your seeder code here
        } catch (\Exception $e) {
            // Log or print the exception message for debugging
            \Log::error($e->getMessage());
        }
        // Get all professional users
        $professionalUsers = User::where('role_id', 2)->get(); // Assuming '2' is the role_id for professionals

        // Get all procedures
        $procedures = Procedimento::all();

        foreach ($professionalUsers as $professionalUser) {
            // Determine the number of procedures each professional performs (randomly)
            $numProcedures = rand(1, count($procedures));

            // Randomly select $numProcedures procedures for the professional
            $selectedProcedures = Arr::random($procedures->toArray(), $numProcedures);

            // Create associations for the selected procedures
            foreach ($selectedProcedures as $procedure) {
                ProfissionalProcedimento::create([
                    'user_id' => $professionalUser->id,
                    'procedimento_id' => $procedure['id'],
                    'price' => 120.00, // Set the price as needed
                ]);
            }
        }
    }
}
