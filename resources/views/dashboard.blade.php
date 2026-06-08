<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header
            title="Dashboard"
            :eyebrow="'Hola, ' . auth()->user()->name . ' — aquí está tu resumen financiero.'">
            <x-slot:action>
                <a href="{{ route('transactions.create') }}" class="btn-primary">
                    <x-icon.plus class="w-4 h-4" /> Nueva transacción
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- KPI cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-ui.kpi-card label="Balance total" :value="'$' . number_format($totalBalance, 2)"
                               hint="Histórico completo" icon="balance" tone="brand" />

                <x-ui.kpi-card label="Ingresos del mes" :value="'$' . number_format($monthIncome, 2)"
                               :hint="now()->translatedFormat('F Y')" icon="income" tone="income" />

                <x-ui.kpi-card label="Gastos del mes" :value="'$' . number_format($monthExpense, 2)"
                               :hint="now()->translatedFormat('F Y')" icon="expense" tone="expense" />

                <x-ui.kpi-card label="Tasa de ahorro" :value="number_format($savingsRate, 1) . '%'"
                               hint="Del mes actual" icon="report" tone="accent" />
            </div>

            {{-- Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                <x-ui.card padding="p-6" class="lg:col-span-3">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="section-title">Ingresos vs Gastos</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Comparativa mensual</p>
                        </div>
                        <span class="badge-neutral">Últimos 6 meses</span>
                    </div>
                    <div class="relative h-72">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </x-ui.card>

                <x-ui.card padding="p-6" class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="section-title">Por categoría</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Distribución de gastos</p>
                        </div>
                        <span class="badge-neutral">Este mes</span>
                    </div>
                    @if ($expensesByCategory->isEmpty())
                        <x-ui.empty-state
                            icon="empty-box"
                            title="Sin gastos este mes"
                            description="Empieza registrando tu primera transacción">
                            <x-slot:action>
                                <a href="{{ route('transactions.create') }}" class="btn-soft btn-sm">
                                    <x-icon.plus class="w-3.5 h-3.5" /> Crear transacción
                                </a>
                            </x-slot:action>
                        </x-ui.empty-state>
                    @else
                        <div class="relative h-72">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    @endif
                </x-ui.card>
            </div>

            {{-- Monthly summary table --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/50 flex items-center justify-center text-brand-600 dark:text-brand-400">
                        <x-icon.calendar class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="section-title">Resumen mensual</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tendencia de los últimos meses</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mes</th>
                                <th class="text-right">Ingresos</th>
                                <th class="text-right">Gastos</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthlyData as $row)
                                <tr>
                                    <td class="font-semibold text-slate-900 dark:text-slate-100">{{ $row['label'] }}</td>
                                    <td class="text-right">
                                        <x-ui.money :amount="$row['income']" type="income" :signed="false" />
                                    </td>
                                    <td class="text-right">
                                        <x-ui.money :amount="$row['expense']" type="expense" :signed="false" />
                                    </td>
                                    <td class="text-right">
                                        <x-ui.money :amount="abs($row['balance'])" :type="$row['balance'] >= 0 ? 'positive' : 'negative'" :signed="false" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

            {{-- Recent transactions --}}
            <x-ui.card padding="p-0" class="overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-950/50 flex items-center justify-center text-brand-600 dark:text-brand-400">
                            <x-icon.transaction class="w-5 h-5" />
                        </div>
                        <div>
                            <h3 class="section-title">Movimientos recientes</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Últimas operaciones registradas</p>
                        </div>
                    </div>
                    <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition">
                        Ver todas
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th class="text-right">Importe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentTransactions as $tx)
                                <tr>
                                    <td class="whitespace-nowrap font-medium">{{ $tx->transaction_date->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $tx->type === 'income' ? 'bg-income-500' : 'bg-expense-500' }}"></span>
                                            {{ $tx->category->name }}
                                        </span>
                                    </td>
                                    <td class="text-slate-600 dark:text-slate-300 max-w-xs truncate">{{ $tx->description ?? '—' }}</td>
                                    <td class="text-right">
                                        <x-ui.money :amount="$tx->amount" :type="$tx->type" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-0">
                                        <x-ui.empty-state
                                            icon="empty-box"
                                            title="Sin transacciones todavía"
                                            description="Crea tu primera para empezar">
                                            <x-slot:action>
                                                <a href="{{ route('transactions.create') }}" class="btn-soft btn-sm">
                                                    <x-icon.plus class="w-3.5 h-3.5" /> Crear transacción
                                                </a>
                                            </x-slot:action>
                                        </x-ui.empty-state>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>

        </div>
    </div>

    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    @endonce

    <script>
        (function () {
            const monthlyLabels = @json(array_column($monthlyData, 'label'));
            const monthlyIncome = @json(array_column($monthlyData, 'income'));
            const monthlyExpense = @json(array_column($monthlyData, 'expense'));

            const monthlyCtx = document.getElementById('monthlyChart');
            const catEl = document.getElementById('categoryChart');
            const catLabels = @json($expensesByCategory->pluck('name'));
            const catValues = @json($expensesByCategory->pluck('total'));

            let monthlyChart, categoryChart;

            function draw() {
                if (monthlyChart) monthlyChart.destroy();
                if (categoryChart) categoryChart.destroy();
                if (!window.WWCharts) return;
                if (monthlyCtx) {
                    monthlyChart = window.WWCharts.renderMonthlyBar(monthlyCtx, monthlyLabels, monthlyIncome, monthlyExpense);
                }
                if (catEl && catLabels.length) {
                    categoryChart = window.WWCharts.renderCategoryDoughnut(catEl, catLabels, catValues);
                }
            }

            function boot() {
                if (window.WWCharts) { draw(); return; }
                // charts.js loaded via Vite/app.js may not be ready immediately
                window.addEventListener('ww:charts-ready', draw, { once: true });
                setTimeout(() => window.WWCharts && draw(), 250);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot);
            } else {
                boot();
            }

            document.addEventListener('ww:theme-changed', draw);
        })();
    </script>
</x-app-layout>
