<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\BarberSeeder;
use Database\Seeders\SpecialtySeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\BarberSpecialtySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            SpecialtySeeder::class,
            BarberSeeder::class,
            ServiceSeeder::class,
            BarberSpecialtySeeder::class
        ]);
    }
}
