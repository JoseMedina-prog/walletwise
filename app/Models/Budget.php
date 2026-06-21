<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'monthly_amount',
        'alert_threshold',
        'is_active',
    ];

    protected $casts = [
        'monthly_amount'  => 'decimal:2',
        'alert_threshold' => 'integer',
        'is_active'       => 'boolean',
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

    public function spentInPeriod(?CarbonImmutable $start = null, ?CarbonImmutable $end = null): float
    {
        $start ??= CarbonImmutable::now()->startOfMonth();
        $end ??= CarbonImmutable::now()->endOfMonth();

        return (float) Transaction::query()
            ->where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');
    }

    public function percentUsed(?CarbonImmutable $start = null, ?CarbonImmutable $end = null): float
    {
        $monthly = (float) $this->monthly_amount;
        if ($monthly <= 0) {
            return 0.0;
        }
        $spent = $this->spentInPeriod($start, $end);
        return round(($spent / $monthly) * 100, 1);
    }

    public function remaining(?CarbonImmutable $start = null, ?CarbonImmutable $end = null): float
    {
        return round((float) $this->monthly_amount - $this->spentInPeriod($start, $end), 2);
    }

    public function alertLevel(): string
    {
        $pct = $this->percentUsed();
        if ($pct >= 100) return 'over';
        if ($pct >= $this->alert_threshold) return 'warn';
        return 'ok';
    }
}