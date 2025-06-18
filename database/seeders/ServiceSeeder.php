<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Specialty;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $services = [
            [
                'name' => 'Corte Masculino',
                'description' => 'Corte de cabelo masculino tradicional',
                'specialty_id' => 1,
                'price' => 25.00,
                'duration_minutes' => 30,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Corte + Barba',
                'description' => 'Corte de cabelo + barba completa',
                'specialty_id' => 2,
                'price' => 35.00,
                'duration_minutes' => 45,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Apenas Barba',
                'description' => 'Apenas barba e bigode',
                'specialty_id' => 2,
                'price' => 15.00,
                'duration_minutes' => 20,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Corte Infantil',
                'description' => 'Corte especial para crianças',
                'specialty_id' => 4,
                'price' => 20.00,
                'duration_minutes' => 25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'name' => 'Corte Degradê',
                'description' => 'Corte moderno com degradê',
                'specialty_id' => 7,
                'price' => 30.00,
                'duration_minutes' => 40,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        foreach($services as $service){
            Service::create($service);
        }

    }
}
