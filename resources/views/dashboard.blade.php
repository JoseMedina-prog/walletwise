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
                               :hint="now()->translatedFormat('F Y')"
                               :trend="$incomeTrend['label'] ?? null"
                               :trendTone="$incomeTrend['tone'] ?? null"
                               icon="income" tone="income" />

                <x-ui.kpi-card label="Gastos del mes" :value="'$' . number_format($monthExpense, 2)"
                               :hint="now()->translatedFormat('F Y')"
                               :trend="$expenseTrend['label'] ?? null"
                               :trendTone="$expenseTrend['tone'] ?? null"
                               icon="expense" tone="expense" />

                <x-ui.kpi-card label="Tasa de ahorro" :value="number_format($savingsRate, 1) . '%'"
                               :hint="now()->translatedFormat('F Y') . ' · ' . ($savingsTrend['label'] ?? '0%')"
                               icon="report" tone="accent" />
            </div>

            {{-- Goals widget --}}
            @if (!empty($activeGoals) && $activeGoals->isNotEmpty())
                <x-ui.card padding="p-0" class="overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-income-50 dark:bg-income-950/40 flex items-center justify-center text-income-600 dark:text-income-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="section-title">Metas activas</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Tu progreso hacia los objetivos</p>
                            </div>
                        </div>
                        <a href="{{ route('goals.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition">
                            Ver todas
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 dark:divide-slate-800">
                        @foreach ($activeGoals as $g)
                            @php $pct = max(0, min(100, (float) $g->percent)); @endphp
                            <div class="px-5 py-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $g->color }}"></span>
                                    <h4 class="font-semibold text-slate-900 dark:text-slate-100 truncate flex-1 min-w-0">{{ $g->name }}</h4>
                                </div>
                                <div class="flex items-baseline gap-1.5 mb-2">
                                    <span class="text-base font-bold num text-slate-900 dark:text-white">${{ number_format($g->current_amount, 0) }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 num">/ ${{ number_format($g->target_amount, 0) }}</span>
                                </div>
                                <div class="relative h-1.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                                    <div class="absolute inset-y-0 left-0 {{ $g->is_completed ? 'bg-income-500' : 'bg-brand-500' }} transition-all"
                                         style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                                    <span class="font-semibold num">{{ number_format($pct, 0) }}%</span>
                                    @if ($g->monthly_suggestion)
                                        <span class="num">~${{ number_format($g->monthly_suggestion, 0) }}/mes</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            {{-- Recurring widget --}}
            @if (!empty($dueRecurrings) && $dueRecurrings->isNotEmpty())
                <x-ui.card padding="p-0" class="overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-accent-50 dark:bg-accent-950/40 flex items-center justify-center text-accent-600 dark:text-accent-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="section-title">Recurrentes pendientes</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Listos para registrar</p>
                            </div>
                        </div>
                        <a href="{{ route('recurring.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition">
                            Ver todos
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($dueRecurrings as $r)
                            <div class="px-6 py-4 flex items-center gap-4">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $r->category->name }}</h4>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        <x-ui.money :amount="$r->amount" :type="$r->type" :signed="false" />
                                        <span class="mx-1">·</span>
                                        <span>{{ $r->frequencyLabel() }}</span>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('recurring.post', $r) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary !py-1.5 !px-3 text-xs">
                                        Registrar
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

            {{-- Budgets widget --}}
            @if (!empty($budgets))
                <x-ui.card padding="p-0" class="overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-accent-50 dark:bg-accent-950/40 flex items-center justify-center text-accent-600 dark:text-accent-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M3 16.061V18a2.25 2.25 0 002.25 2.25h13.5A2.25 2.25 0 0021 18v-1.939M3 16.061c0-1.18.91-2.165 2.087-2.317l9.193-1.456a2.25 2.25 0 012.236 1.272M21 16.061V18a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-1.939"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="section-title">Presupuestos del mes</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ now()->translatedFormat('F Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('budgets.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 transition">
                            Gestionar
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach ($budgets as $budget)
                            <div class="px-6 py-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <h4 class="font-semibold text-slate-900 dark:text-slate-100 truncate flex-1 min-w-0">
                                        {{ $budget->category->name }}
                                    </h4>
                                    @if ($budget->level === 'over')
                                        <span class="badge-expense">Excedido</span>
                                    @elseif ($budget->level === 'warn')
                                        <span class="badge-accent">Alerta</span>
                                    @endif
                                </div>
                                <x-ui.budget-progress
                                    :percent="$budget->percent"
                                    :level="$budget->level"
                                    :spent="$budget->spent"
                                    :monthly="$budget->monthly_amount" />
                            </div>
                        @endforeach
                    </div>
                </x-ui.card>
            @endif

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
