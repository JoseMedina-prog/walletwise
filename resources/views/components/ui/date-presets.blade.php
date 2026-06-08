@props([
    'from' => '',
    'to'   => '',
    'route' => '',  // ruta base a la que enlazar (sin query params)
])

@php
    use App\Support\DateRange;
    $active = DateRange::detectActive($from, $to);
@endphp

<div class="flex flex-wrap items-center gap-1.5" role="group" aria-label="Filtros rápidos de período">
    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider me-1">Período:</span>
    @foreach (DateRange::catalog() as $slug => $label)
        @php
            $range = DateRange::preset($slug);
            $url = $route . '?from=' . $range['from'] . '&to=' . $range['to'];
        @endphp
        <a href="{{ $url }}"
           @class([
               'inline-flex items-center h-8 px-3 rounded-lg text-xs font-semibold transition',
               'bg-brand-600 text-white shadow-sm' => $active === $slug,
               'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/50' => $active !== $slug,
           ])>
            {{ $label }}
        </a>
    @endforeach
</div>
