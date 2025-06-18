<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarberSpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $barberSpecialties = [
            // João Silva
            [
                'barber_id' => 1,
                'specialty_id' => 1,
                'experience_years' => 5,
                'is_primary' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'barber_id' => 1,
                'specialty_id' => 2,
                'experience_years' => 3,
                'is_primary' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'barber_id' => 1,
                'specialty_id' => 7,
                'experience_years' => 2,
                'is_primary' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            
            // Pedro Santos
            [
                'barber_id' => 2,
                'specialty_id' => 4,
                'experience_years' => 8,
                'is_primary' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'barber_id' => 2,
                'specialty_id' => 5,
                'experience_years' => 6,
                'is_primary' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'barber_id' => 2,
                'specialty_id' => 6,
                'experience_years' => 4,
                'is_primary' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            
            // Carlos Oliveira
            [
                'barber_id' => 3,
                'specialty_id' => 7,
                'experience_years' => 10,
                'is_primary' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'barber_id' => 3,
                'specialty_id' => 2,
                'experience_years' => 8,
                'is_primary' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'barber_id' => 3,
                'specialty_id' => 3,
                'experience_years' => 5,
                'is_primary' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        foreach($barberSpecialties as $barberSpecialty){
            DB::table('barber_specialty')->insert($barberSpecialty);
        }
    }
}
