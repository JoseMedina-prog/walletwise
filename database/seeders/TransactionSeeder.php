<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $incomeDescriptions = ['Pago mensual', 'Proyecto web', 'Dividendos', 'Venta extra'];
        $expenseDescriptions = ['Compra supermercado', 'Gasolina', 'Cena con amigos', 'Suscripción', 'Recarga transporte', 'Café', 'Farmacia'];

        foreach (User::all() as $user) {
            $incomeCats = Category::where('user_id', $user->id)->where('type', 'income')->get();
            $expenseCats = Category::where('user_id', $user->id)->where('type', 'expense')->get();

            if ($incomeCats->isEmpty() || $expenseCats->isEmpty()) {
                continue;
            }

            for ($i = 0; $i < 20; $i++) {
                $isIncome = fake()->boolean(40);
                $cat = $isIncome ? $incomeCats->random() : $expenseCats->random();
                $date = Carbon::now()->subDays(rand(0, 60));

                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $cat->id,
                    'type' => $isIncome ? 'income' : 'expense',
                    'amount' => $isIncome ? fake()->randomFloat(2, 500, 4000) : fake()->randomFloat(2, 5, 250),
                    'description' => $isIncome
                        ? fake()->randomElement($incomeDescriptions)
                        : fake()->randomElement($expenseDescriptions),
                    'transaction_date' => $date->toDateString(),
                ]);
            }
        }
    }
}
