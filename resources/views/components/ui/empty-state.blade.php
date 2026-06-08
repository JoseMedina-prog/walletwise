@props([
    'icon' => 'empty-box',
    'title' => '',
    'description' => null,
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-10 px-6']) }}>
    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
        <x-icon :name="$icon" class="w-7 h-7 text-slate-400 dark:text-slate-500" />
    </div>
    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-sm">{{ $description }}</p>
    @endif
    @if ($action)
        <div class="mt-4">{{ $action }}</div>
    @endif
</div>
