<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\BudgetAlertNotification;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class BudgetAlertService
{
    /**
     * Evalúa presupuestos que cubre esta transacción y crea notificaciones
     * (idempotente por user+budget+periodo).
     *
     * @return Collection<int, Budget>
     */
    public function evaluateForTransaction(Transaction $transaction): Collection
    {
        if ($transaction->type !== 'expense') {
            return collect();
        }

        $period = CarbonImmutable::parse($transaction->transaction_date)
            ->startOfMonth()
            ->format('Y-m');

        $budgets = Budget::query()
            ->where('user_id', $transaction->user_id)
            ->where('is_active', true)
            ->where('category_id', $transaction->category_id)
            ->get();

        return $budgets->filter(function (Budget $budget) use ($period) {
            $percent = $budget->percentUsed();
            if ($percent < $budget->alert_threshold) {
                // Para "over" sí disparamos incluso si el threshold es 80% (sigue siendo alerta)
                return $percent >= 100;
            }
            return $this->shouldNotify($budget, $period);
        })->values();
    }

    /**
     * Notifica al usuario de los presupuestos afectados.
     *
     * @param  Collection<int, Budget>  $budgets
     */
    public function notify(User $user, Collection $budgets, string $period): void
    {
        foreach ($budgets as $budget) {
            $percent = $budget->percentUsed();
            $level = $budget->alertLevel();

            // Solo notificar si está en warn u over (no en ok)
            if (!in_array($level, ['warn', 'over'], true)) {
                continue;
            }

            // Dedup: ¿ya existe notificación para este budget + period?
            $exists = $user->notifications()
                ->where('type', BudgetAlertNotification::class)
                ->where('data->budget_id', $budget->id)
                ->where('data->period', $period)
                ->exists();

            if ($exists) {
                continue;
            }

            $user->notify(new BudgetAlertNotification(
                budget: $budget,
                level: $level,
                percent: $percent,
                spent: $budget->spentInPeriod(),
                period: $period,
            ));
        }
    }

    /**
     * ¿Debería notificarse? Sí si cruzamos el threshold (warn) o si ya está excedido (over).
     * Para over, re-evaluamos siempre (puede haber bajado o subido).
     */
    private function shouldNotify(Budget $budget, string $period): bool
    {
        $percent = $budget->percentUsed();
        return $percent >= $budget->alert_threshold;
    }
}