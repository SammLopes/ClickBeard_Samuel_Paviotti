<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;
use Carbon\Carbon;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        $specialites = 
        [
            [
                'name' => 'Corte Masculino',
                'description' => 'Especialização em cortes de cabelo masculinos tradicionais e modernos',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Barba e Bigode',
                'description' => 'Especialização em cuidados com barba, bigode e aparagem',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Sobrancelha',
                'description' => 'Especialização em design e cuidados com sobrancelhas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Corte Infantil',
                'description' => 'Especialização em cortes para crianças',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Corte Feminino',
                'description' => 'Especialização em cortes de cabelo femininos',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Escova Progressiva',
                'description' => 'Especialização em tratamentos capilares e alisamentos',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Corte Degradê',
                'description' => 'Especialização em cortes modernos com degradê',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Tratamentos Capilares',
                'description' => 'Especialização em hidratação e tratamentos para cabelo',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        foreach($specialites as $spe){
            Specialty::create($spe);
        }
    }
}
