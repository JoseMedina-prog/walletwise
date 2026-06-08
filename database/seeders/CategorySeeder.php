<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'income' => ['Salario', 'Freelance', 'Inversiones', 'Otros ingresos'],
            'expense' => ['Comida', 'Transporte', 'Vivienda', 'Ocio', 'Salud', 'Educación', 'Otros gastos'],
        ];

        $users = User::all();

        foreach ($users as $user) {
            foreach ($defaults as $type => $names) {
                foreach ($names as $name) {
                    Category::firstOrCreate([
                        'user_id' => $user->id,
                        'name' => $name,
                        'type' => $type,
                    ]);
                }
            }
        }
    }
}
