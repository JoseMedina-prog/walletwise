<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Presupuestos" eyebrow="Controla tus límites mensuales por categoría.">
            <x-slot:action>
                <a href="{{ route('budgets.create') }}" class="btn-primary">
                    <x-icon.plus class="w-4 h-4" /> Nuevo presupuesto
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
            @endif

            {{-- Summary --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <x-ui.kpi-card label="Presupuesto total" :value="'$' . number_format($summary['total'], 2)" icon="balance" tone="brand" />
                <x-ui.kpi-card label="Gastado este mes" :value="'$' . number_format($summary['spent'], 2)" icon="expense" tone="expense" />
                <x-ui.kpi-card label="En alerta" :value="$summary['warn_count']" icon="filter" tone="accent" />
                <x-ui.kpi-card label="Excedidos" :value="$summary['over_count']" icon="expense" tone="expense" />
            </div>

            {{-- Period info --}}
            <div class="inline-flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 bg-white/80 dark:bg-slate-900/80 backdrop-blur border border-slate-200 dark:border-slate-800 rounded-lg px-3.5 py-2">
                <x-icon.calendar class="w-3.5 h-3.5" />
                Período actual:
                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ now()->translatedFormat('F Y') }}</span>
            </div>

            {{-- List --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                @if ($budgets->isEmpty())
                    <x-ui.empty-state
                        icon="empty-box"
                        title="Aún no tienes presupuestos"
                        description="Define límites mensuales por categoría y te avisaremos cuando te acerques al umbral.">
                        <x-slot:action>
                            <a href="{{ route('budgets.create') }}" class="btn-primary">
                                <x-icon.plus class="w-4 h-4" /> Crear el primero
                            </a>
                        </x-slot:action>
                    </x-ui.empty-state>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($budgets as $budget)
                            <div class="px-5 sm:px-6 py-5 hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition">
                                <div class="flex items-start gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-semibold text-slate-900 dark:text-slate-100 truncate">
                                                {{ $budget->category->name }}
                                            </h4>
                                            @if (!$budget->is_active)
                                                <span class="badge-neutral">Pausado</span>
                                            @elseif ($budget->level === 'over')
                                                <span class="badge-expense">Excedido</span>
                                            @elseif ($budget->level === 'warn')
                                                <span class="badge-accent">En alerta</span>
                                            @endif
                                        </div>
                                        <x-ui.budget-progress
                                            :percent="$budget->percent"
                                            :level="$budget->level"
                                            :spent="$budget->spent"
                                            :monthly="$budget->monthly_amount" />
                                    </div>

                                    <div class="flex items-center gap-1 flex-shrink-0">
                                        <a href="{{ route('budgets.edit', $budget) }}" class="btn-icon" title="Editar" aria-label="Editar presupuesto">
                                            <x-icon.edit class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('budgets.destroy', $budget) }}" class="inline"
                                              onsubmit="return confirm('¿Eliminar este presupuesto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon text-expense-500 hover:bg-expense-50 dark:hover:bg-expense-950/30"
                                                    title="Eliminar" aria-label="Eliminar presupuesto">
                                                <x-icon.trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>