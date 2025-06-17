<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
                'name' => 'Admin',
                'email' => 'admin@barbearia.com',
                'password' => Hash::make('1234'), 
                'role' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
        ]);
    }
}
