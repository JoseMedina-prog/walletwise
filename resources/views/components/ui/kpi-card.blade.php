@props([
    'label' => '',
    'value' => '',
    'hint' => '',
    'icon' => null,
    'tone' => 'brand',     // brand | income | expense | accent
    'trend' => null,       // string e.g. '+12.4%' or null
    'trendTone' => null,   // 'income' | 'expense' | 'neutral'
])

@php
    $toneStyles = [
        'brand'   => 'from-brand-500 to-brand-600 shadow-brand-500/30',
        'income'  => 'from-income-500 to-income-600 shadow-income-500/30',
        'expense' => 'from-expense-500 to-expense-600 shadow-expense-500/30',
        'accent'  => 'from-accent-500 to-accent-600 shadow-accent-500/30',
    ];
    $valueColor = [
        'brand'   => 'text-slate-900 dark:text-white',
        'income'  => 'amount-income',
        'expense' => 'amount-expense',
        'accent'  => 'text-accent-700 dark:text-accent-300',
    ];
    $trendStyles = [
        'income'  => 'bg-income-50 text-income-700 dark:bg-income-950/50 dark:text-income-300',
        'expense' => 'bg-expense-50 text-expense-700 dark:bg-expense-950/50 dark:text-expense-300',
        'neutral' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300',
    ];
@endphp

<div class="group relative card p-5 transition duration-200 ease-out hover:shadow-md hover:-translate-y-0.5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <p class="section-eyebrow">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold tracking-tight {{ $valueColor[$tone] }}">
                {{ $value }}
            </p>
            <div class="mt-1.5 flex items-center gap-2">
                @if ($trend)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold {{ $trendStyles[$trendTone ?? 'neutral'] }}">
                        {{ $trend }}
                    </span>
                @endif
                @if ($hint)
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $hint }}</p>
                @endif
            </div>
        </div>
        @if ($icon)
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-white shadow-lg bg-gradient-to-br {{ $toneStyles[$tone] }} flex-shrink-0">
                <x-icon :name="$icon" class="w-5 h-5" />
            </div>
        @endif
    </div>
</div>
