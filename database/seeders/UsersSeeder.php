<?php

namespace Database\Seeders;

use App\Models\User;
use Hash;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'name' => 'Profissional 1',
            'email' => 'profissional1@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2, // 'PROFISSIONAL'
            'whatsapp' => '1234567890',
            'endereço' => '123 Street',
            'estado' => 'Some State',
            'pais' => 'Some Country',
            'cidade' => 'Some City',
            'birthdate' => '1990-01-01',
        ]);

        User::create([
            'name' => 'Cliente 1',
            'email' => 'cliente1@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 3, // 'CLIENTE'
            'whatsapp' => '9876543210',
            'endereço' => '456 Avenue',
            'estado' => 'Another State',
            'pais' => 'Another Country',
            'cidade' => 'Another City',
            'birthdate' => '1995-05-05',
        ]);
        
        User::create([
            'name' => 'Profissional 2',
            'email' => 'profissional2@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2, // 'PROFISSIONAL'
            'whatsapp' => '2345678901',
            'endereço' => '456 Avenue',
            'estado' => 'Another State',
            'pais' => 'Another Country',
            'cidade' => 'Another City',
            'birthdate' => '1985-08-15',
        ]);

        User::create([
            'name' => 'Cliente 2',
            'email' => 'cliente2@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 3, // 'CLIENTE'
            'whatsapp' => '8765432109',
            'endereço' => '789 Road',
            'estado' => 'Yet Another State',
            'pais' => 'Yet Another Country',
            'cidade' => 'Yet Another City',
            'birthdate' => '1980-03-20',
        ]);

        User::create([
            'name' => 'Profissional 3',
            'email' => 'profissional3@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 2, // 'PROFISSIONAL'
            'whatsapp' => '3456789012',
            'endereço' => '123 Street',
            'estado' => 'Some State',
            'pais' => 'Some Country',
            'cidade' => 'Some City',
            'birthdate' => '1992-12-10',
        ]);

        User::create([
            'name' => 'Cliente 3',
            'email' => 'cliente3@example.com',
            'password' => Hash::make('password123'),
            'role_id' => 3, // 'CLIENTE'
            'whatsapp' => '7654321098',
            'endereço' => '567 Lane',
            'estado' => 'New State',
            'pais' => 'New Country',
            'cidade' => 'New City',
            'birthdate' => '1998-06-25',
        ]);

        // Add more User records with 'PROFISSIONAL' or 'CLIENTE' role_id as needed
    }
}
