@props([
    'title' => '',
    'eyebrow' => null,
    'back' => null,
])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        @if ($back)
            <a href="{{ $back }}" class="back-link flex-shrink-0" aria-label="Volver">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </a>
        @endif
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-[26px] font-bold tracking-tight text-slate-900 dark:text-white truncate">{{ $title }}</h1>
            @if ($eyebrow)
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $eyebrow }}</p>
            @endif
        </div>
    </div>
    @isset($action)
        <div class="flex items-center gap-2 flex-shrink-0">{{ $action }}</div>
    @endisset
</div>
