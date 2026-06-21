<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class BudgetAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Budget $budget,
        public string $level,
        public float $percent,
        public float $spent,
        public string $period,
    ) {}

    /**
     * Solo canal database (in-app). Email/mail lo añadimos en otra ronda.
     *
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(mixed $notifiable): DatabaseMessage
    {
        $categoryName = $this->budget->category?->name ?? 'Categoría';

        return new DatabaseMessage([
            'budget_id'      => $this->budget->id,
            'category_id'    => $this->budget->category_id,
            'category_name'  => $categoryName,
            'level'          => $this->level,
            'percent'        => $this->percent,
            'spent'          => $this->spent,
            'monthly_amount' => (float) $this->budget->monthly_amount,
            'period'         => $this->period,
            'title'          => match ($this->level) {
                'over' => "Excediste el presupuesto de {$categoryName}",
                'warn' => "Alerta: {$categoryName} cerca del límite",
                default => "Presupuesto de {$categoryName} actualizado",
            },
            'body'           => match ($this->level) {
                'over' => "Has gastado {$this->percent}% de \${$this->budget->monthly_amount} este mes.",
                'warn' => "Llevas {$this->percent}% del presupuesto (umbral {$this->budget->alert_threshold}%).",
                default => "Nivel de gasto: {$this->period}%.",
            },
        ]);
    }
}