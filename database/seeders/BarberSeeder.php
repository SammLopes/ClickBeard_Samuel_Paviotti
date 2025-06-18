<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Barber;

class BarberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Adicionar truncate da tabela de agendamento, despois dos barbeiros
        //Barber::truncate();
        $barbers = [
            [
                'name' => 'João Silva',
                'email' => 'joao@barbearia.com',
                'phone' => '11999999999',
                'age' => 28, 
                'hire_date' => '2023-01-15' 
            ],
            [
                'name' => 'Pedro Santos',
                'email' => 'pedro@barbearia.com',
                'phone' => '11888888888',
                'age' => 32, 
                'hire_date' => '2022-06-10' 
            ],
            [
                'name' => 'Carlos Oliveira',
                'email' => 'carlos@barbearia.com',
                'phone' => '11777777777',
                'age' => 35, 
                'hire_date' => '2021-03-20' 
            ],
        ];

        foreach ($barbers as $barber) {
            Barber::create($barber);
        }
    }
}
