<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Metas de ahorro" eyebrow="Visualiza el progreso hacia tus objetivos.">
            <x-slot:action>
                <a href="{{ route('goals.create') }}" class="btn-primary">
                    <x-icon.plus class="w-4 h-4" /> Nueva meta
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
            @endif

            {{-- Summary --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <x-ui.kpi-card label="Ahorro objetivo" :value="'$' . number_format($summary['total_target'], 2)" icon="balance" tone="brand" />
                <x-ui.kpi-card label="Ahorrado" :value="'$' . number_format($summary['total_current'], 2)" icon="income" tone="income" />
                <x-ui.kpi-card label="Activas" :value="$summary['active']" icon="filter" tone="accent" />
                <x-ui.kpi-card label="Completadas" :value="$summary['completed']" icon="report" tone="income" />
            </div>

            {{-- Filters --}}
            <div class="inline-flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
                @foreach (['active' => 'Activas', 'completed' => 'Completadas', 'all' => 'Todas'] as $slug => $label)
                    <a href="{{ route('goals.index', ['filter' => $slug]) }}"
                       @class([
                           'inline-flex items-center px-3 py-1.5 rounded-md text-sm font-semibold transition',
                           'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' => $filter === $slug,
                           'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' => $filter !== $slug,
                       ])>
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Grid --}}
            @if ($goals->isEmpty())
                <x-ui.card padding="p-0" class="overflow-hidden">
                    <x-ui.empty-state
                        icon="empty-box"
                        title="Aún no tienes metas"
                        description="Define un objetivo y empieza a registrar aportes para alcanzarlo.">
                        <x-slot:action>
                            <a href="{{ route('goals.create') }}" class="btn-primary">
                                <x-icon.plus class="w-4 h-4" /> Crear la primera
                            </a>
                        </x-slot:action>
                    </x-ui.empty-state>
                </x-ui.card>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach ($goals as $g)
                        @include('goals._card', ['goal' => $g])
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>