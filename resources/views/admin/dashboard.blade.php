@extends('admin.layouts.admin')

@section('title', 'Panel de administración')

@section('content')
    <div class="mb-6">
        <x-ui.section-header
            title="Panel de administración"
            eyebrow="Métricas agregadas de la plataforma. No se muestran datos individuales." />
    </div>

    {{-- Privacy notice --}}
    <x-ui.alert type="info" class="mb-6">
        <span class="font-semibold">Modo privacidad:</span> por diseño, el administrador no tiene acceso a las categorías, transacciones ni importes de los usuarios. Solo se muestran métricas agregadas.
    </x-ui.alert>

    {{-- KPI grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.kpi-card label="Usuarios totales" :value="number_format($totalUsers)" hint="Registrados en la plataforma" icon="user" tone="brand" />
        <x-ui.kpi-card label="Usuarios activos" :value="number_format($activeUsers)"
                       :hint="$inactiveUsers . ' inactivo' . ($inactiveUsers === 1 ? '' : 's')"
                       icon="check" tone="income" />
        <x-ui.kpi-card label="Categorías" :value="number_format($totalCategories)" hint="Totales en la plataforma" icon="category" tone="accent" />
        <x-ui.kpi-card label="Transacciones" :value="number_format($totalTransactions)" hint="Totales en la plataforma" icon="transaction" tone="expense" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-ui.card padding="p-6" class="lg:col-span-2">
            <h3 class="section-title mb-1">Actividad reciente</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Últimos 30 días · sin contenido, solo conteos</p>

            <ul class="space-y-4">
                <li class="flex items-center justify-between gap-3 py-2">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-brand-500"></span>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Nuevos usuarios (30d)</span>
                    </div>
                    <span class="num text-lg font-bold text-slate-900 dark:text-white">{{ number_format($newUsersLast30) }}</span>
                </li>
                <li class="flex items-center justify-between gap-3 py-2">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-income-500"></span>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Usuarios activos (último login en 7d)</span>
                    </div>
                    <span class="num text-lg font-bold text-slate-900 dark:text-white">{{ number_format($activeLast7) }}</span>
                </li>
                <li class="flex items-center justify-between gap-3 py-2">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Transacciones creadas (30d)</span>
                    </div>
                    <span class="num text-lg font-bold text-slate-900 dark:text-white">{{ number_format($newTxLast30) }}</span>
                </li>
            </ul>
        </x-ui.card>

        <x-ui.card padding="p-6">
            <h3 class="section-title mb-1">Distribución de roles</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">Cantidad por tipo de cuenta</p>

            <ul class="space-y-4">
                <li>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Usuarios</span>
                        <span class="num text-sm font-bold text-slate-900 dark:text-white">{{ number_format($regularUsers) }}</span>
                    </div>
                    <x-ui.progress :percent="$totalUsers > 0 ? ($regularUsers / $totalUsers) * 100 : 0" tone="income" />
                </li>
                <li>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-slate-700 dark:text-slate-200">Administradores</span>
                        <span class="num text-sm font-bold text-slate-900 dark:text-white">{{ number_format($adminUsers) }}</span>
                    </div>
                    <x-ui.progress :percent="$totalUsers > 0 ? ($adminUsers / $totalUsers) * 100 : 0" tone="expense" />
                </li>
            </ul>

            <div class="mt-6 pt-5 border-t border-slate-200 dark:border-slate-800">
                <a href="{{ route('admin.users.index') }}" class="btn-primary w-full">
                    <x-icon.user class="w-4 h-4" /> Gestionar usuarios
                </a>
            </div>
        </x-ui.card>
    </div>
@endsection
