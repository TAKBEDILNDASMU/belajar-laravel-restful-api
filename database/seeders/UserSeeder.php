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
        $user = new User();
        $user->name = "test";
        $user->email = "test@test.com";
        $user->password = Hash::make('test');
        $user->age = 20;
        $user->token = 'test';
        $user->save();
    }
}
