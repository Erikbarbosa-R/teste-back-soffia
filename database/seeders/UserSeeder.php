<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'nome' => 'João Silva',
                'email' => 'joao.silva@example.com',
                'password' => Hash::make('senha123'),
                'telefone' => '(11) 98765-4321',
                'is_valid' => true,
            ],
            [
                'nome' => 'Maria Oliveira',
                'email' => 'maria.oliveira@example.com',
                'password' => Hash::make('senha123'),
                'telefone' => '(21) 91234-5678',
                'is_valid' => false,
            ],
            [
                'nome' => 'Pedro Souza',
                'email' => 'pedro.souza@example.com',
                'password' => Hash::make('senha123'),
                'telefone' => '(31) 99876-5432',
                'is_valid' => true,
            ],
            [
                'nome' => 'Ana Costa',
                'email' => 'ana.costa@example.com',
                'password' => Hash::make('senha123'),
                'telefone' => '(41) 99877-6655',
                'is_valid' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}





