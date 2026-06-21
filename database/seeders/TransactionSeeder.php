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
                $isIncome = mt_rand(1, 100) <= 40;
                $cat = $isIncome ? $incomeCats->random() : $expenseCats->random();
                $date = Carbon::now()->subDays(mt_rand(0, 60));

                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $cat->id,
                    'type' => $isIncome ? 'income' : 'expense',
                    'amount' => $isIncome
                        ? round(mt_rand(50000, 400000) / 100, 2)
                        : round(mt_rand(500, 25000) / 100, 2),
                    'description' => $isIncome
                        ? $incomeDescriptions[array_rand($incomeDescriptions)]
                        : $expenseDescriptions[array_rand($expenseDescriptions)],
                    'transaction_date' => $date->toDateString(),
                ]);
            }
        }
    }
}