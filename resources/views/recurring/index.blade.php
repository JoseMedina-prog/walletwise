<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Recurrentes" eyebrow="Suscripciones, sueldos, alquileres… automatiza lo que se repite.">
            <x-slot:action>
                <a href="{{ route('recurring.create') }}" class="btn-primary">
                    <x-icon.plus class="w-4 h-4" /> Nuevo recurrente
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
            @endif
            @if (session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            {{-- Summary --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <x-ui.kpi-card label="Activos" :value="$summary['total']" icon="filter" tone="brand" />
                <x-ui.kpi-card label="Pendientes" :value="$summary['due']" icon="calendar" tone="accent" />
                <x-ui.kpi-card label="Ingresos recurrentes" :value="'$' . number_format($summary['income'], 2)" icon="income" tone="income" />
                <x-ui.kpi-card label="Gastos recurrentes" :value="'$' . number_format($summary['expense'], 2)" icon="expense" tone="expense" />
            </div>

            {{-- Filters --}}
            <div class="inline-flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
                @foreach (['active' => 'Activos', 'due' => 'Pendientes', 'all' => 'Todos'] as $slug => $label)
                    <a href="{{ route('recurring.index', ['filter' => $slug]) }}"
                       @class([
                           'inline-flex items-center px-3 py-1.5 rounded-md text-sm font-semibold transition',
                           'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm' => $filter === $slug,
                           'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' => $filter !== $slug,
                       ])>
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- List --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                @forelse ($recurrings as $r)
                    <div @class([
                        'px-5 sm:px-6 py-4',
                        'border-b border-slate-100 dark:border-slate-800' => !$loop->last,
                        'bg-accent-50/30 dark:bg-accent-950/10' => $r->is_due,
                    ])>
                        <div class="flex items-start gap-4">
                            <div @class([
                                'w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0',
                                'bg-income-100 dark:bg-income-950/50 text-income-600 dark:text-income-400' => $r->type === 'income',
                                'bg-expense-100 dark:bg-expense-950/50 text-expense-600 dark:text-expense-400' => $r->type === 'expense',
                            ])>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <h4 class="font-semibold text-slate-900 dark:text-slate-100 truncate">
                                        {{ $r->category->name }}
                                    </h4>
                                    @if ($r->is_due)
                                        <span class="badge-accent">Pendiente</span>
                                    @endif
                                    @if (!$r->is_active)
                                        <span class="badge-neutral">Pausado</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <x-ui.money :amount="$r->amount" :type="$r->type" :signed="false" />
                                    <span>·</span>
                                    <span>{{ $r->label }}</span>
                                    <span>·</span>
                                    <span>Próxima: <span class="num font-semibold text-slate-700 dark:text-slate-200">{{ \Carbon\Carbon::parse($r->next_occurrence)->format('Y-m-d') }}</span></span>
                                </div>
                                @if ($r->description)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 truncate">{{ $r->description }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-1 flex-shrink-0">
                                @if ($r->is_due)
                                    <form method="POST" action="{{ route('recurring.post', $r) }}">
                                        @csrf
                                        <button type="submit"
                                                class="btn-primary !py-1.5 !px-3 text-xs"
                                                title="Registrar transacción ahora"
                                                aria-label="Registrar transacción">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            </svg>
                                            Registrar
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('recurring.edit', $r) }}" class="btn-icon" title="Editar" aria-label="Editar recurrente">
                                    <x-icon.edit class="w-4 h-4" />
                                </a>
                                <form method="POST" action="{{ route('recurring.destroy', $r) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar este recurrente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-expense-500 hover:bg-expense-50 dark:hover:bg-expense-950/30"
                                            title="Eliminar" aria-label="Eliminar recurrente">
                                        <x-icon.trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <x-ui.empty-state
                        icon="empty-box"
                        title="No tienes recurrentes"
                        description="Crea tu primer recurrente y registra automáticamente sueldos, alquileres o suscripciones.">
                        <x-slot:action>
                            <a href="{{ route('recurring.create') }}" class="btn-primary">
                                <x-icon.plus class="w-4 h-4" /> Crear el primero
                            </a>
                        </x-slot:action>
                    </x-ui.empty-state>
                @endforelse
            </x-ui.card>

        </div>
    </div>
</x-app-layout>