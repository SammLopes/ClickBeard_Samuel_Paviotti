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
                'specialties' => 'Corte masculino, Barba, Bigode',
            ],
            [
                'name' => 'Pedro Santos',
                'email' => 'pedro@barbearia.com',
                'phone' => '11888888888',
                'specialties' => 'Corte infantil, Corte feminino, Escova',
            ],
            [
                'name' => 'Carlos Oliveira',
                'email' => 'carlos@barbearia.com',
                'phone' => '11777777777',
                'specialties' => 'Corte degradê, Barba, Sobrancelha',
            ],
        ];

        foreach ($barbers as $barber) {
            Barber::create($barber);
        }
    }
}
