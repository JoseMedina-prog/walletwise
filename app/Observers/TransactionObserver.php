<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Services\BudgetAlertService;
use Carbon\CarbonImmutable;

class TransactionObserver
{
    public function __construct(private BudgetAlertService $alerts) {}

    public function created(Transaction $transaction): void
    {
        $this->evaluate($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        // Solo re-evaluar si cambió amount, type o category_id
        if ($transaction->wasChanged(['amount', 'type', 'category_id', 'transaction_date'])) {
            $this->evaluate($transaction);
        }
    }

    public function deleted(Transaction $transaction): void
    {
        // Al borrar también re-evaluar (puede bajar el %)
        $this->evaluate($transaction, deleted: true);
    }

    private function evaluate(Transaction $transaction, bool $deleted = false): void
    {
        if ($transaction->type !== 'expense' && !$deleted) {
            return;
        }

        $budgets = $this->alerts->evaluateForTransaction($transaction);
        if ($budgets->isEmpty()) {
            return;
        }

        $user = $transaction->user;
        $period = CarbonImmutable::parse($transaction->transaction_date)->startOfMonth()->format('Y-m');

        $this->alerts->notify($user, $budgets, $period);
    }
}