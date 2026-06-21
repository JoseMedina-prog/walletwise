<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Transacciones" eyebrow="Gestiona todos tus ingresos y gastos.">
            <x-slot:action>
                <a href="{{ route('exports.transactions', array_filter(request()->only(['from', 'to', 'type', 'category_id', 'q']))) }}" class="btn-secondary">
                    <x-icon.download class="w-4 h-4" /> Exportar CSV
                </a>
                <a href="{{ route('transactions.create') }}" class="btn-primary">
                    <x-icon.plus class="w-4 h-4" /> Nueva transacción
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <x-ui.alert type="success">{{ session('status') }}</x-ui.alert>
            @endif
            @if (session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-ui.kpi-card label="Ingresos" :value="'$' . number_format($summary['income'], 2)" icon="income" tone="income" />
                <x-ui.kpi-card label="Gastos"   :value="'$' . number_format($summary['expense'], 2)" icon="expense" tone="expense" />
                <x-ui.kpi-card label="Balance"  :value="'$' . number_format(abs($summary['balance']), 2)"
                               :hint="$summary['balance'] >= 0 ? 'Positivo' : 'Negativo'"
                               icon="balance" tone="brand" />
            </div>

            {{-- Date presets --}}
            <x-ui.date-presets :from="$from" :to="$to" :route="route('transactions.index')" />

            {{-- Filters --}}
            <x-ui.card padding="p-5">
                <div class="flex items-center gap-2 mb-4">
                    <x-icon.filter class="w-4 h-4 text-slate-500 dark:text-slate-400" />
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Filtros</h3>
                </div>
                <form method="GET" action="{{ route('transactions.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                        <x-ui.input id="from" name="from" type="date" label="Desde" :value="$from" />
                        <x-ui.input id="to" name="to" type="date" label="Hasta" :value="$to" />
                        <x-ui.select id="type" name="type" label="Tipo">
                            <option value="">Todos</option>
                            <option value="income"  {{ $type === 'income'  ? 'selected' : '' }}>Ingresos</option>
                            <option value="expense" {{ $type === 'expense' ? 'selected' : '' }}>Gastos</option>
                        </x-ui.select>
                        <x-ui.select id="category_id" name="category_id" label="Categoría">
                            <option value="">Todas</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </x-ui.select>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn-primary flex-1">
                                <x-icon.filter class="w-4 h-4" /> Filtrar
                            </button>
                            <a href="{{ route('transactions.index') }}" class="btn-secondary">Limpiar</a>
                        </div>
                    </div>

                    <div class="relative">
                        <label for="q" class="form-label">Buscar por descripción</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                                </svg>
                            </span>
                            <input id="q"
                                   name="q"
                                   type="search"
                                   value="{{ $q }}"
                                   placeholder="Ej. supermercado, gasolina, café…"
                                   autocomplete="off"
                                   class="form-input pl-9 pr-10" />
                            @if (!empty($q))
                                <a href="{{ route('transactions.index', request()->except('q')) }}"
                                   class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
                                   title="Limpiar búsqueda"
                                   aria-label="Limpiar búsqueda">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </x-ui.card>

            {{-- Table --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th class="text-right">Importe</th>
                                <th class="text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $tx)
                                <tr>
                                    <td class="whitespace-nowrap font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $tx->transaction_date->format('Y-m-d') }}
                                    </td>
                                    <td>
                                        <x-ui.badge :type="$tx->type">
                                            {{ $tx->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                        </x-ui.badge>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $tx->type === 'income' ? 'bg-income-500' : 'bg-expense-500' }}"></span>
                                            {{ $tx->category->name }}
                                        </span>
                                    </td>
                                    <td class="text-slate-600 dark:text-slate-300 max-w-xs truncate">{{ $tx->description ?? '—' }}</td>
                                    <td class="text-right">
                                        <x-ui.money :amount="$tx->amount" :type="$tx->type" />
                                    </td>
                                    <td class="text-right whitespace-nowrap">
                                        <a href="{{ route('transactions.edit', $tx) }}" class="btn-icon" title="Editar" aria-label="Editar transacción">
                                            <x-icon.edit class="w-4 h-4" />
                                        </a>
                                        <form method="POST" action="{{ route('transactions.duplicate', $tx) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="btn-icon" title="Duplicar" aria-label="Duplicar transacción">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/>
                                                </svg>
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('transactions.destroy', $tx) }}" class="inline"
                                              onsubmit="return confirm('¿Eliminar esta transacción?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon text-expense-500 hover:bg-expense-50 dark:hover:bg-expense-950/30" title="Eliminar" aria-label="Eliminar transacción">
                                                <x-icon.trash class="w-4 h-4" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-0">
                                        <x-ui.empty-state
                                            icon="empty-box"
                                            :title="!empty($q) ? 'Sin resultados para «' . $q . '»' : 'No hay transacciones con los filtros aplicados'"
                                            :description="!empty($q) ? 'Prueba con otro término o limpia los filtros activos.' : 'Prueba a limpiar los filtros o crea una nueva.'">
                                            <x-slot:action>
                                                <a href="{{ route('transactions.index') }}" class="btn-secondary">
                                                    Limpiar filtros
                                                </a>
                                                <a href="{{ route('transactions.create') }}" class="btn-primary">
                                                    <x-icon.plus class="w-4 h-4" /> Nueva transacción
                                                </a>
                                            </x-slot:action>
                                        </x-ui.empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($transactions->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
