<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=>'Alta Sierra Cafe Xilitla',
            'email'=>'altasierracafexilitla@outlook.com',
            'password'=>Hash::make('Cafesierraalta@2024'),
            'isAdmin'=>1
        ]);
    }
}
