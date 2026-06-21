<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Reportes" eyebrow="Análisis detallado por período.">
            <x-slot:action>
                @if (\Illuminate\Support\Facades\Route::has('exports.transactions'))
                    <a href="{{ route('exports.transactions', array_filter(request()->only(['from', 'to', 'type', 'category_id', 'q']))) }}" class="btn-secondary">
                        <x-icon.download class="w-4 h-4" /> Exportar CSV
                    </a>
                @endif
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Date presets --}}
            <x-ui.date-presets :from="$from" :to="$to" :route="route('reports.index')" />

            {{-- Filters --}}
            <x-ui.card padding="p-5">
                <div class="flex items-center gap-2 mb-4">
                    <x-icon.filter class="w-4 h-4 text-slate-500 dark:text-slate-400" />
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200">Filtros</h3>
                </div>
                <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
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
                                <x-icon.filter class="w-4 h-4" /> Aplicar
                            </button>
                            <a href="{{ route('reports.index') }}" class="btn-secondary">Limpiar</a>
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
                                <a href="{{ route('reports.index', request()->except('q')) }}"
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

            {{-- Summary cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.kpi-card label="Ingresos"   :value="'$' . number_format($income, 2)" icon="income" tone="income" />
                <x-ui.kpi-card label="Gastos"     :value="'$' . number_format($expense, 2)" icon="expense" tone="expense" />
                <x-ui.kpi-card label="Balance"    :value="'$' . number_format(abs($balance), 2)" icon="balance" tone="brand" />
                <x-ui.kpi-card label="Movimientos" :value="number_format($count)" icon="receipt" tone="accent" />
            </div>

            {{-- Period info --}}
            <div class="inline-flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 bg-white/80 dark:bg-slate-900/80 backdrop-blur border border-slate-200 dark:border-slate-800 rounded-lg px-3.5 py-2">
                <x-icon.calendar class="w-3.5 h-3.5" />
                Mostrando datos del
                <span class="font-semibold text-slate-700 dark:text-slate-200 num">{{ \Carbon\Carbon::parse($from)->format('Y-m-d') }}</span>
                al
                <span class="font-semibold text-slate-700 dark:text-slate-200 num">{{ \Carbon\Carbon::parse($to)->format('Y-m-d') }}</span>
            </div>

            {{-- Breakdowns --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-ui.card padding="p-0" class="overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-expense-50 dark:bg-expense-950/40 flex items-center justify-center text-expense-600 dark:text-expense-400">
                            <x-icon.expense class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="section-title">Gastos por categoría</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Distribución en el período</p>
                        </div>
                    </div>
                    @if ($breakdownExpense->isEmpty())
                        <x-ui.empty-state icon="empty-box" title="Sin gastos en el período"
                            description="Cuando registres gastos aparecerán aquí" />
                    @else
                        <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($breakdownExpense as $row)
                                <li class="px-6 py-4">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $row['name'] }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $row['tx_count'] }} {{ \Illuminate\Support\Str::plural('movimiento', $row['tx_count']) }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <x-ui.money :amount="$row['total']" type="expense" :signed="false" />
                                        </div>
                                    </div>
                                    <x-ui.progress :percent="($expense > 0 ? ($row['total'] / $expense) * 100 : 0)" tone="expense" />
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>

                <x-ui.card padding="p-0" class="overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-income-50 dark:bg-income-950/40 flex items-center justify-center text-income-600 dark:text-income-400">
                            <x-icon.income class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="section-title">Ingresos por categoría</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Distribución en el período</p>
                        </div>
                    </div>
                    @if ($breakdownIncome->isEmpty())
                        <x-ui.empty-state icon="empty-box" title="Sin ingresos en el período"
                            description="Cuando registres ingresos aparecerán aquí" />
                    @else
                        <ul class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($breakdownIncome as $row)
                                <li class="px-6 py-4">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $row['name'] }}</p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $row['tx_count'] }} {{ \Illuminate\Support\Str::plural('movimiento', $row['tx_count']) }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <x-ui.money :amount="$row['total']" type="income" :signed="false" />
                                        </div>
                                    </div>
                                    <x-ui.progress :percent="($income > 0 ? ($row['total'] / $income) * 100 : 0)" tone="income" />
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-ui.card>
            </div>

            {{-- Detail --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/40 flex items-center justify-center text-brand-600 dark:text-brand-400">
                            <x-icon.receipt class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="section-title">Detalle</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Movimientos del período</p>
                        </div>
                    </div>
                    <span class="badge-neutral">Mostrando {{ $transactions->count() }} de {{ $count }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th class="text-right">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transactions as $tx)
                                <tr>
                                    <td class="whitespace-nowrap font-semibold text-slate-900 dark:text-slate-100">{{ $tx->transaction_date->format('Y-m-d') }}</td>
                                    <td>
                                        <x-ui.badge :type="$tx->type">
                                            {{ $tx->type === 'income' ? 'Ingreso' : 'Gasto' }}
                                        </x-ui.badge>
                                    </td>
                                    <td>{{ $tx->category->name }}</td>
                                    <td class="text-slate-600 dark:text-slate-300 max-w-xs truncate">{{ $tx->description ?? '—' }}</td>
                                    <td class="text-right">
                                        <x-ui.money :amount="$tx->amount" :type="$tx->type" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-0">
                                        <x-ui.empty-state
                                            icon="empty-box"
                                            title="No hay transacciones con los filtros aplicados"
                                            description="Prueba a ampliar el rango de fechas" />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
