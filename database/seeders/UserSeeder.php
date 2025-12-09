<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Ebrahim',
                'email' => 'ebrahimkhalil.dev@gmail.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create($userData);
            $token = $user->createToken('api-token')->plainTextToken;
            $this->command->info("Created user {$user->email} with token: $token");
        }
    }
}

