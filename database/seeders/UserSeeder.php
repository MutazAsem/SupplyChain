<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Abod',
            'last_name' => 'alwosabi',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
            'phone' => '774370569',
            'gender' => 'male',
            'status' => true,
        ]);

        User::create([
            'name' => 'Mutaz',
            'last_name' => 'Asem',
            'email' => 'mu@admin.com',
            'password' => Hash::make('m12345678'),
            'phone' => '774743134',
            'gender' => 'male',
            'status' => true,
        ]);
    }
}
//php artisan db:seed --class=UserSeeder