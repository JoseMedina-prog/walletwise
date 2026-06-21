<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Editar meta" :eyebrow="$goal->name . ' · ' . number_format($goal->percentReached(), 1) . '% completado'">
            <x-slot:action>
                <a href="{{ route('goals.index') }}" class="btn-secondary">
                    <x-icon name="empty-box" class="w-4 h-4" /> Volver
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
            @endif

            {{-- Edit form --}}
            <x-ui.card padding="p-7 sm:p-8">
                <form method="POST" action="{{ route('goals.update', $goal) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-ui.input id="name" name="name" type="text" label="Nombre" required
                                :value="old('name', $goal->name)" />

                    <x-ui.input id="description" name="description" type="text" label="Descripción (opcional)"
                                :value="old('description', $goal->description)" />

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.input id="target_amount" name="target_amount" type="number" label="Objetivo" required
                                    step="0.01" min="0.01" :value="old('target_amount', $goal->target_amount)" />
                        <x-ui.input id="current_amount" name="current_amount" type="number" label="Ahorrado actual"
                                    step="0.01" min="0" :value="old('current_amount', $goal->current_amount)" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.input id="start_date" name="start_date" type="date" label="Fecha de inicio"
                                    :value="old('start_date', $goal->start_date?->toDateString())" />
                        <x-ui.input id="target_date" name="target_date" type="date" label="Fecha objetivo"
                                    :value="old('target_date', $goal->target_date?->toDateString())" />
                    </div>

                    <x-ui.input id="color" name="color" type="color" label="Color"
                                :value="old('color', $goal->color)" />

                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <form method="POST" action="{{ route('goals.destroy', $goal) }}" class="inline"
                              onsubmit="return confirm('¿Eliminar esta meta y todos sus aportes?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-secondary text-expense-600 dark:text-expense-400">
                                <x-icon.trash class="w-4 h-4" /> Eliminar meta
                            </button>
                        </form>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('goals.index') }}" class="btn-secondary">Cancelar</a>
                            <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                        </div>
                    </div>
                </form>
            </x-ui.card>

            {{-- Contributions --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="section-title">Aportes</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Histórico de contribuciones a esta meta.</p>
                </div>

                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                    <form method="POST" action="{{ route('goals.contributions.store', $goal) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                        @csrf
                        <x-ui.input id="amount" name="amount" type="number" label="Importe" required
                                    step="0.01" min="0.01" placeholder="100.00" />
                        <x-ui.input id="contribution_date" name="contribution_date" type="date" label="Fecha" required
                                    :value="now()->toDateString()" />
                        <x-ui.input id="note" name="note" type="text" label="Nota (opcional)" placeholder="Ahorro del mes" />
                        <x-ui.button type="submit" variant="primary">Añadir aporte</x-ui.button>
                    </form>
                </div>

                @php $contributions = $goal->contributions()->latest('contribution_date')->get(); @endphp
                @if ($contributions->isEmpty())
                    <div class="p-6 text-center text-sm text-slate-500 dark:text-slate-400">
                        Aún no hay aportes registrados.
                    </div>
                @else
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($contributions as $c)
                            <div class="px-6 py-3 flex items-center gap-4">
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100 num">+${{ number_format($c->amount, 2) }}</div>
                                    @if ($c->note)
                                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $c->note }}</div>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-500 dark:text-slate-400 num">{{ $c->contribution_date->format('Y-m-d') }}</span>
                                <form method="POST" action="{{ route('goals.contributions.destroy', [$goal, $c]) }}"
                                      onsubmit="return confirm('¿Eliminar este aporte?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon text-expense-500 hover:bg-expense-50 dark:hover:bg-expense-950/30" title="Eliminar aporte">
                                        <x-icon.trash class="w-4 h-4" />
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>