<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'description', 'target_amount', 'current_amount',
        'target_date', 'start_date', 'color', 'icon', 'is_completed', 'completed_at',
    ];

    protected $casts = [
        'target_amount'  => 'decimal:2',
        'current_amount' => 'decimal:2',
        'is_completed'   => 'boolean',
        'target_date'    => 'date',
        'start_date'     => 'date',
        'completed_at'   => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(GoalContribution::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_completed', false);
    }

    public function percentReached(): float
    {
        $target = (float) $this->target_amount;
        if ($target <= 0) return 0.0;
        return round(((float) $this->current_amount / $target) * 100, 1);
    }

    /**
     * Si tiene target_date, calcula si está en ritmo.
     * Devuelve true si se proyecta completar a tiempo o ya se completó.
     */
    public function isOnTrack(): bool
    {
        if (!$this->target_date) return true;
        if ($this->is_completed) return true;

        $start = $this->start_date
            ? CarbonImmutable::parse($this->start_date)
            : CarbonImmutable::parse($this->created_at);
        $end   = CarbonImmutable::parse($this->target_date);
        $now   = CarbonImmutable::now();

        if ($end->lessThanOrEqualTo($start)) return true;

        $totalDays   = (int) $start->diffInDays($end);
        $elapsedDays = (int) $start->diffInDays($now);
        if ($elapsedDays <= 0) return true;

        $expectedPercent = min(100, ($elapsedDays / max($totalDays, 1)) * 100);
        return $this->percentReached() >= ($expectedPercent - 5); // 5% tolerancia
    }

    /**
     * Estima cuándo se completará si se mantiene el ritmo actual.
     */
    public function projectedCompletionDate(): ?CarbonImmutable
    {
        if ($this->is_completed) {
            return $this->completed_at ? CarbonImmutable::parse($this->completed_at) : null;
        }
        if (!$this->start_date || (float) $this->current_amount <= 0) return null;

        $start = CarbonImmutable::parse($this->start_date);
        $now   = CarbonImmutable::now();
        $elapsedDays = max(1, (int) $start->diffInDays($now));
        $remaining = (float) $this->target_amount - (float) $this->current_amount;

        if ($remaining <= 0) return $now;

        $dailyRate = (float) $this->current_amount / $elapsedDays;
        if ($dailyRate <= 0) return null;

        $daysToGo = (int) ceil($remaining / $dailyRate);
        return $now->addDays($daysToGo);
    }

    /**
     * Sugerencia de aporte mensual para llegar al objetivo a tiempo.
     */
    public function suggestedMonthlyContribution(): ?float
    {
        if (!$this->target_date || $this->is_completed) return null;
        $end = CarbonImmutable::parse($this->target_date);
        $now = CarbonImmutable::now();
        if ($end->lessThanOrEqualTo($now)) return null;

        $monthsLeft = max(1, (int) ceil($now->diffInMonths($end)));
        $remaining  = (float) $this->target_amount - (float) $this->current_amount;
        return round($remaining / $monthsLeft, 2);
    }

    /**
     * Marca como completada si llega al 100%.
     */
    public function checkCompletion(): bool
    {
        if (!$this->is_completed && $this->percentReached() >= 100) {
            $this->forceFill([
                'is_completed' => true,
                'completed_at' => CarbonImmutable::now()->toDateString(),
                'current_amount' => $this->target_amount,
            ])->save();
            return true;
        }
        return false;
    }
}