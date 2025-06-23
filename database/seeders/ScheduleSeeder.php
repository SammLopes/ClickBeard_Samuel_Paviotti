<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Scheduling;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedulings = [
            [
                'user_id' => 2,
                'barber_id' => 1,
                'service_id' => 1,
                'scheduling_date' => '2025-06-20',
                'scheduling_time' => '10:00',
                'notes' => 'Quero um corte moderno',
            ],
            [
                'user_id' => 3,
                'barber_id' => 2,
                'service_id' => 1,
                'scheduling_date' => '2025-06-21',
                'scheduling_time' => '14:30',
                'notes' => 'Fazer barba e corte baixo',
            ],
            [
                'user_id' => 4,
                'barber_id' => 3,
                'service_id' => 2,
                'scheduling_date' => '2025-06-22',
                'scheduling_time' => '09:15',
                'notes' => 'Corte degradê + sobrancelha',
            ]
        ];
        foreach($schedulings as $scheduler){
            Scheduling::create($scheduler);
        }
    }
}
