@php
    $pct = $goal->percent;
    $clamped = max(0, min(100, (float) $pct));
    $overflow = max(0, (float) $pct - 100);
    $isCompleted = $goal->is_completed;
    $barColor = $isCompleted ? 'bg-income-500' : ($goal->on_track ? 'bg-brand-500' : 'bg-accent-500');
@endphp

<x-ui.card padding="p-6">
    <div class="flex items-start justify-between gap-3 mb-4">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 rounded-full" style="background-color: {{ $goal->color }}"></span>
                <h3 class="font-bold text-slate-900 dark:text-slate-100 truncate">{{ $goal->name }}</h3>
                @if ($isCompleted)
                    <span class="badge-income">Completada</span>
                @elseif (!$goal->on_track)
                    <span class="badge-expense">Retrasada</span>
                @endif
            </div>
            @if ($goal->description)
                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">{{ $goal->description }}</p>
            @endif
        </div>
        <a href="{{ route('goals.edit', $goal) }}" class="btn-icon" title="Editar" aria-label="Editar meta">
            <x-icon.edit class="w-4 h-4" />
        </a>
    </div>

    {{-- Amount --}}
    <div class="flex items-baseline gap-2 mb-3">
        <span class="text-2xl font-bold text-slate-900 dark:text-white num">${{ number_format($goal->current_amount, 2) }}</span>
        <span class="text-sm text-slate-500 dark:text-slate-400 num">de ${{ number_format($goal->target_amount, 2) }}</span>
    </div>

    {{-- Progress bar --}}
    <div class="relative h-2.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700 mb-3">
        <div class="absolute inset-y-0 left-0 {{ $barColor }} transition-all duration-500"
             style="width: {{ $clamped }}%"></div>
    </div>

    {{-- Meta info --}}
    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
        <span class="font-bold num">{{ number_format($clamped, 1) }}%</span>
        @if ($goal->target_date)
            <span>Fecha objetivo: <span class="font-semibold text-slate-700 dark:text-slate-200 num">{{ $goal->target_date->format('Y-m-d') }}</span></span>
        @endif
    </div>

    @if (!$isCompleted && $goal->monthly_suggestion)
        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
            <span class="text-slate-500 dark:text-slate-400">Aporte mensual sugerido:</span>
            <span class="font-bold text-brand-600 dark:text-brand-400 num"> ${{ number_format($goal->monthly_suggestion, 2) }}</span>
        </div>
    @endif

    @if ($goal->projected)
        @php $projectedDate = $goal->projected; @endphp
        <div class="text-xs mt-1">
            <span class="text-slate-500 dark:text-slate-400">Finalización estimada:</span>
            <span class="font-semibold text-slate-700 dark:text-slate-200 num"> {{ $projectedDate->format('Y-m-d') }}</span>
        </div>
    @endif
</x-ui.card>