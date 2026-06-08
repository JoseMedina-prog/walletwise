@props(['percent' => 0, 'tone' => 'expense'])  {{-- tone: income | expense --}}

@php
    $safe = max(0, min(100, (float) $percent));
    $barClass = $tone === 'income' ? 'progress-bar-income' : 'progress-bar-expense';
@endphp

<div class="flex items-center gap-3">
    <div class="progress-track flex-1">
        <div class="{{ $barClass }}" style="width: {{ $safe }}%"></div>
    </div>
    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 num min-w-[3.5rem] text-right">{{ number_format($safe, 1) }}%</span>
</div>
