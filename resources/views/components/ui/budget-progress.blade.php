@props([
    'percent' => 0,
    'level' => 'ok',
    'spent' => 0,
    'monthly' => 0,
    'showLabels' => true,
])

@php
    $clamped = max(0, min(100, (float) $percent));
    $overflow = max(0, (float) $percent - 100);

    $barColor = match($level) {
        'over' => 'bg-expense-500',
        'warn' => 'bg-accent-500',
        default => 'bg-income-500',
    };

    $textColor = match($level) {
        'over' => 'text-expense-600 dark:text-expense-400',
        'warn' => 'text-accent-600 dark:text-accent-400',
        default => 'text-income-600 dark:text-income-400',
    };
@endphp

<div>
    @if ($showLabels)
        <div class="flex items-center justify-between gap-2 mb-1.5 text-xs">
            <span class="text-slate-600 dark:text-slate-400">
                $<span class="num">{{ number_format($spent, 2) }}</span>
                <span class="text-slate-400 dark:text-slate-500">/ $<span class="num">{{ number_format($monthly, 2) }}</span></span>
            </span>
            <span class="font-bold {{ $textColor }} num">{{ number_format($clamped, 0) }}%</span>
        </div>
    @endif

    <div class="relative h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700" role="progressbar"
         aria-valuenow="{{ $clamped }}" aria-valuemin="0" aria-valuemax="100">
        <div class="absolute inset-y-0 left-0 {{ $barColor }} transition-all duration-500"
             style="width: {{ $clamped }}%"></div>
    </div>

    @if ($overflow > 0)
        <p class="mt-1.5 text-xs font-semibold text-expense-600 dark:text-expense-400">
            Excedido por $<span class="num">{{ number_format($overflow, 2) }}</span>
        </p>
    @endif
</div>