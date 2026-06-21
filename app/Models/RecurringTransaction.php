<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'type', 'amount', 'description',
        'frequency', 'interval', 'start_date', 'next_occurrence',
        'end_date', 'is_active',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'interval'         => 'integer',
        'is_active'        => 'boolean',
        'start_date'       => 'date',
        'next_occurrence'  => 'date',
        'end_date'         => 'date',
        'last_posted_at'   => 'date',
    ];

    public const FREQUENCIES = [
        'daily'   => 'Diaria',
        'weekly'  => 'Semanal',
        'monthly' => 'Mensual',
        'yearly'  => 'Anual',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDue(Builder $query, ?CarbonImmutable $now = null): Builder
    {
        $now ??= CarbonImmutable::now();
        return $query->where('is_active', true)
            ->where('next_occurrence', '<=', $now->toDateString())
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now->toDateString());
            });
    }

    /**
     * Avanza la próxima ocurrencia según la frecuencia e intervalo.
     */
    public function advanceOccurrence(CarbonImmutable|string|null $fromDate = null): CarbonImmutable
    {
        $base = $fromDate
            ? ($fromDate instanceof CarbonImmutable ? $fromDate : CarbonImmutable::parse($fromDate))
            : CarbonImmutable::parse($this->next_occurrence);

        return match ($this->frequency) {
            'daily'   => $base->addDays($this->interval),
            'weekly'  => $base->addWeeks($this->interval),
            'monthly' => $base->addMonthsNoOverflow($this->interval),
            'yearly'  => $base->addYearsNoOverflow($this->interval),
            default   => $base->addMonthsNoOverflow($this->interval),
        };
    }

    /**
     * Crea la transacción a partir del recurrente. Devuelve la transacción creada.
     */
    public function postNow(): ?Transaction
    {
        if (!$this->is_active) {
            return null;
        }

        $today = CarbonImmutable::now();
        if ($this->end_date && $today->greaterThan(CarbonImmutable::parse($this->end_date))) {
            $this->update(['is_active' => false]);
            return null;
        }

        $txDate = CarbonImmutable::parse($this->next_occurrence)->greaterThan($today)
            ? $today
            : CarbonImmutable::parse($this->next_occurrence);

        $tx = Transaction::create([
            'user_id'          => $this->user_id,
            'category_id'      => $this->category_id,
            'type'             => $this->type,
            'amount'           => $this->amount,
            'description'      => $this->description,
            'transaction_date' => $txDate->toDateString(),
        ]);

        $this->forceFill([
            'last_posted_at'  => $today->toDateString(),
            'next_occurrence' => $this->advanceOccurrence()->toDateString(),
        ])->save();

        return $tx;
    }

    /**
     * Etiqueta legible de la frecuencia para mostrar en UI.
     */
    public function frequencyLabel(): string
    {
        $freq = self::FREQUENCIES[$this->frequency] ?? $this->frequency;
        return $this->interval === 1 ? $freq : "Cada {$this->interval} {$freq}";
    }

    public function isDue(): bool
    {
        return CarbonImmutable::parse($this->next_occurrence)
            ->lessThanOrEqualTo(CarbonImmutable::now());
    }
}