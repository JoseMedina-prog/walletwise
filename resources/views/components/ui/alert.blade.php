@props([
    'type' => 'success',  // success | error | info | warning
    'dismissible' => false,
])

@php
    $styles = [
        'success' => 'bg-income-50 dark:bg-income-950/30 border-income-200 dark:border-income-900/50 text-income-800 dark:text-income-200',
        'error'   => 'bg-expense-50 dark:bg-expense-950/30 border-expense-200 dark:border-expense-900/50 text-expense-800 dark:text-expense-200',
        'info'    => 'bg-brand-50 dark:bg-brand-950/30 border-brand-200 dark:border-brand-900/50 text-brand-800 dark:text-brand-200',
        'warning' => 'bg-accent-50 dark:bg-accent-950/30 border-accent-200 dark:border-accent-900/50 text-accent-800 dark:text-accent-200',
    ];
    $icons = [
        'success' => 'check',
        'error'   => 'alert',
        'info'    => 'dashboard',
        'warning' => 'alert',
    ];
    $iconBg = [
        'success' => 'bg-income-100 dark:bg-income-900/50 text-income-600 dark:text-income-400',
        'error'   => 'bg-expense-100 dark:bg-expense-900/50 text-expense-600 dark:text-expense-400',
        'info'    => 'bg-brand-100 dark:bg-brand-900/50 text-brand-600 dark:text-brand-400',
        'warning' => 'bg-accent-100 dark:bg-accent-900/50 text-accent-600 dark:text-accent-400',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     {{ $attributes->merge(['class' => 'flex items-start gap-3 border rounded-xl px-4 py-3 ' . $styles[$type]]) }}
     role="alert">
    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $iconBg[$type] }}">
        <x-icon :name="$icons[$type]" class="w-5 h-5" />
    </div>
    <div class="flex-1 text-sm font-medium">{{ $slot }}</div>
    @if ($dismissible)
        <button @click="show = false" class="text-current opacity-60 hover:opacity-100 transition" aria-label="Cerrar">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
