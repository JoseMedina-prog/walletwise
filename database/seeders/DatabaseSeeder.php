<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@walletwise.test'],
            [
                'name'     => 'Demo User',
                'password' => Hash::make('password'),
                'role'     => User::ROLE_USER,
                'is_active' => true,
            ]
        );

        $this->call(AdminSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(TransactionSeeder::class);
    }
}
