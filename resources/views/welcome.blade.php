<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#4F46E5">

        <title>{{ config('app.name', 'WalletWise') }} — Tus finanzas, bajo control</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <script>
            (function () {
                try {
                    var stored = localStorage.getItem('ww-theme');
                    var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (stored === 'dark' || (!stored && prefersDark)) {
                        document.documentElement.classList.add('dark');
                    }
                } catch (e) {}
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 dark:text-slate-100 bg-white dark:bg-slate-950 overflow-x-hidden">

        {{-- Top nav --}}
        <header class="absolute top-0 left-0 right-0 z-20">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-5 flex items-center justify-between">
                <x-wallet-logo />

                @if (Route::has('login'))
                    <nav class="flex items-center gap-2">
                        <x-theme-toggle size="sm" />
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary">
                                Ir al dashboard
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="hidden sm:inline-flex text-sm font-semibold text-slate-700 dark:text-slate-200 hover:text-brand-600 dark:hover:text-brand-400 px-4 py-2 transition">
                                Iniciar sesión
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary">
                                    Crear cuenta
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <main>
            {{-- Hero --}}
            <section class="relative overflow-hidden">
                <div class="absolute inset-0 -z-10">
                    <div class="absolute inset-0 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:24px_24px]"></div>
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[60rem] h-[40rem] bg-gradient-to-tr from-brand-200/40 via-brand-100/30 to-income-200/30 dark:from-brand-900/20 dark:via-brand-900/10 dark:to-income-900/15 rounded-full blur-3xl"></div>
                </div>

                <div class="max-w-7xl mx-auto px-6 lg:px-8 pt-32 pb-16 lg:pt-40 lg:pb-20">
                    <div class="max-w-3xl mx-auto text-center">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/80 dark:bg-slate-900/80 backdrop-blur border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold mb-7 shadow-xs">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-income-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-income-500"></span>
                            </span>
                            MVP · Laravel 12 · MySQL · Chart.js
                        </div>

                        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.05] text-slate-900 dark:text-white">
                            Tus finanzas,
                            <span class="block mt-2 text-brand-600 dark:text-brand-400">bajo control.</span>
                        </h1>

                        <p class="mt-6 text-lg sm:text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto leading-relaxed">
                            Registra ingresos y gastos, visualiza métricas con gráficos, genera reportes filtrados y exporta a CSV.
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Multi-usuario, simple y rápido.</span>
                        </p>

                        <div class="mt-10 flex flex-col sm:flex-row gap-3 justify-center">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary btn-lg">
                                    Ir al dashboard
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn-primary btn-lg">
                                    Empezar gratis
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                </a>
                                <a href="{{ route('login') }}" class="btn-secondary btn-lg">
                                    Ya tengo cuenta
                                </a>
                            @endauth
                        </div>

                        <div class="mt-8 inline-flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 bg-white/60 dark:bg-slate-900/60 backdrop-blur px-4 py-2 rounded-full border border-slate-200 dark:border-slate-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-accent-500"></span>
                            Demo:
                            <code class="font-mono text-brand-600 dark:text-brand-400 font-semibold">demo@walletwise.test</code>
                            ·
                            <code class="font-mono text-brand-600 dark:text-brand-400 font-semibold">password</code>
                        </div>
                    </div>

                    {{-- Dashboard mockup --}}
                    <div class="mt-16 lg:mt-20 max-w-5xl mx-auto">
                        <div class="relative">
                            <div class="absolute -inset-1 bg-gradient-to-r from-brand-500 via-brand-400 to-income-400 rounded-2xl blur-2xl opacity-20 dark:opacity-30"></div>

                            <div class="relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
                                {{-- Browser chrome --}}
                                <div class="flex items-center gap-2 px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700">
                                    <div class="flex gap-1.5">
                                        <div class="w-3 h-3 rounded-full bg-expense-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-accent-400"></div>
                                        <div class="w-3 h-3 rounded-full bg-income-400"></div>
                                    </div>
                                    <div class="flex-1 mx-4">
                                        <div class="bg-white dark:bg-slate-900 rounded-md px-3 py-1 text-xs text-slate-500 dark:text-slate-400 inline-flex items-center gap-1.5 max-w-xs">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            walletwise.test/dashboard
                                        </div>
                                    </div>
                                </div>

                                <div class="p-6 space-y-4">
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                                        <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Balance</span>
                                                <div class="w-6 h-6 rounded-md bg-brand-100 dark:bg-brand-900/50 flex items-center justify-center text-brand-600 dark:text-brand-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            </div>
                                            <div class="text-xl font-bold text-slate-900 dark:text-white num">$9,372</div>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Ingresos</span>
                                                <div class="w-6 h-6 rounded-md bg-income-100 dark:bg-income-900/50 flex items-center justify-center text-income-600 dark:text-income-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25"/></svg>
                                                </div>
                                            </div>
                                            <div class="text-xl font-bold text-income-600 dark:text-income-400 num">$11,615</div>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Gastos</span>
                                                <div class="w-6 h-6 rounded-md bg-expense-100 dark:bg-expense-900/50 flex items-center justify-center text-expense-600 dark:text-expense-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181"/></svg>
                                                </div>
                                            </div>
                                            <div class="text-xl font-bold text-expense-600 dark:text-expense-400 num">$2,243</div>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-[10px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">Ahorro</span>
                                                <div class="w-6 h-6 rounded-md bg-accent-100 dark:bg-accent-900/50 flex items-center justify-center text-accent-600 dark:text-accent-400">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75z"/></svg>
                                                </div>
                                            </div>
                                            <div class="text-xl font-bold text-accent-600 dark:text-accent-400 num">80.6%</div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
                                        <div class="lg:col-span-2 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                                            <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-3">Ingresos vs Gastos · últimos 6 meses</div>
                                            <div class="flex items-end gap-2 h-32">
                                                @foreach ([5,5,5,70,30,5] as $i => $incomeHeight)
                                                    @php $expenseHeight = 5 + ($i * 1.5); @endphp
                                                    <div class="flex-1 flex flex-col items-center gap-1">
                                                        <div class="w-full flex items-end gap-0.5 h-24">
                                                            <div class="flex-1 bg-income-300 dark:bg-income-700 rounded-t" style="height: {{ $incomeHeight }}%"></div>
                                                            <div class="flex-1 bg-expense-300 dark:bg-expense-700 rounded-t" style="height: {{ $expenseHeight }}%"></div>
                                                        </div>
                                                        <div class="text-[9px] text-slate-500 {{ $i === 3 ? 'font-bold text-slate-900 dark:text-white' : '' }}">
                                                            {{ ['Ene','Feb','Mar','Abr','May','Jun'][$i] }}
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex flex-col">
                                            <div class="text-xs font-semibold text-slate-700 dark:text-slate-300 mb-3">Por categoría</div>
                                            <div class="flex-1 flex items-center justify-center">
                                                <div class="relative w-24 h-24">
                                                    <div class="absolute inset-0 rounded-full" style="background: conic-gradient(#4F46E5 0% 35%, #10B981 35% 60%, #F59E0B 60% 80%, #F43F5E 80% 100%);"></div>
                                                    <div class="absolute inset-3 bg-white dark:bg-slate-900 rounded-full"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Features bento --}}
            <section class="py-24 bg-slate-50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-800">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold mb-4 border border-slate-200 dark:border-slate-700">
                            Funcionalidades
                        </div>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Todo lo que necesitas,
                            <span class="text-brand-600 dark:text-brand-400">nada que sobre.</span>
                        </h2>
                        <p class="mt-4 text-lg text-slate-600 dark:text-slate-400">
                            Un MVP limpio que cubre el ciclo completo de finanzas personales.
                        </p>
                    </div>

                    @php
                        $features = [
                            ['color' => 'brand',   'icon' => 'dashboard', 'title' => 'Dashboard con métricas',         'desc' => 'KPIs en vivo, gráficos de barras, dona por categoría y resumen mensual.'],
                            ['color' => 'income',  'icon' => 'income',    'title' => 'Categorías personalizadas',     'desc' => 'Crea tus propias categorías de ingresos y gastos según tu estilo.'],
                            ['color' => 'accent',  'icon' => 'filter',    'title' => 'Filtros potentes',              'desc' => 'Filtra transacciones por rango de fecha, tipo y categoría.'],
                            ['color' => 'expense', 'icon' => 'report',    'title' => 'Reportes detallados',           'desc' => 'Breakdowns por categoría con porcentajes y el detalle de tus movimientos.'],
                            ['color' => 'sky',     'icon' => 'download',  'title' => 'Exporta a CSV',                 'desc' => 'Descarga tus transacciones con un click. Compatible con Excel y Google Sheets.'],
                            ['color' => 'purple',  'icon' => 'user',      'title' => 'Multi-usuario seguro',          'desc' => 'Cada usuario solo ve y gestiona sus propios datos. Aislamiento estricto.'],
                        ];
                        $colorMap = [
                            'brand'   => ['bg' => 'bg-brand-50 dark:bg-brand-950/50',  'text' => 'text-brand-600 dark:text-brand-400',  'border' => 'border-brand-100 dark:border-brand-900/50'],
                            'income'  => ['bg' => 'bg-income-50 dark:bg-income-950/50', 'text' => 'text-income-600 dark:text-income-400', 'border' => 'border-income-100 dark:border-income-900/50'],
                            'accent'  => ['bg' => 'bg-accent-50 dark:bg-accent-950/50', 'text' => 'text-accent-600 dark:text-accent-400', 'border' => 'border-accent-100 dark:border-accent-900/50'],
                            'expense' => ['bg' => 'bg-expense-50 dark:bg-expense-950/50','text' => 'text-expense-600 dark:text-expense-400','border' => 'border-expense-100 dark:border-expense-900/50'],
                            'sky'     => ['bg' => 'bg-sky-50 dark:bg-sky-950/50',      'text' => 'text-sky-600 dark:text-sky-400',      'border' => 'border-sky-100 dark:border-sky-900/50'],
                            'purple'  => ['bg' => 'bg-purple-50 dark:bg-purple-950/50', 'text' => 'text-purple-600 dark:text-purple-400', 'border' => 'border-purple-100 dark:border-purple-900/50'],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($features as $f)
                            @php $c = $colorMap[$f['color']]; @endphp
                            <div class="group relative bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:shadow-md hover:-translate-y-0.5 transition duration-200">
                                <div class="w-12 h-12 rounded-xl {{ $c['bg'] }} {{ $c['border'] }} border flex items-center justify-center {{ $c['text'] }} mb-4 group-hover:scale-110 transition-transform duration-200">
                                    <x-icon :name="$f['icon']" class="w-6 h-6" />
                                </div>
                                <h3 class="text-lg font-semibold mb-1.5 text-slate-900 dark:text-white">{{ $f['title'] }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $f['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- How it works --}}
            <section class="py-24 bg-white dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-bold mb-4 border border-slate-200 dark:border-slate-700">
                            Cómo funciona
                        </div>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold tracking-tight text-slate-900 dark:text-white">
                            Empieza en
                            <span class="text-brand-600 dark:text-brand-400">3 pasos.</span>
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                        @php
                            $steps = [
                                ['num' => '01', 'title' => 'Crea tu cuenta',     'desc' => 'Registro gratuito en menos de 1 minuto. Sin tarjeta, sin compromiso.'],
                                ['num' => '02', 'title' => 'Registra tu actividad', 'desc' => 'Añade ingresos y gastos con categoría, importe y fecha.'],
                                ['num' => '03', 'title' => 'Visualiza y exporta', 'desc' => 'Tu dashboard se actualiza al instante. Exporta a CSV cuando quieras.'],
                            ];
                        @endphp
                        @foreach ($steps as $step)
                            <div class="relative card p-7 hover:border-slate-300 dark:hover:border-slate-700 transition">
                                <div class="text-4xl font-extrabold text-brand-600/20 dark:text-brand-400/20 mb-3 num">{{ $step['num'] }}</div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1.5">{{ $step['title'] }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $step['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Stats band --}}
            <section class="py-16 bg-slate-900 dark:bg-slate-900 relative overflow-hidden">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff0a_1px,transparent_1px),linear-gradient(to_bottom,#ffffff0a_1px,transparent_1px)] bg-[size:32px_32px]"></div>
                <div class="max-w-7xl mx-auto px-6 lg:px-8 relative">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center text-white">
                        <div>
                            <div class="text-3xl sm:text-4xl font-extrabold num">100%</div>
                            <div class="text-sm text-slate-400 mt-1">Open source</div>
                        </div>
                        <div>
                            <div class="text-3xl sm:text-4xl font-extrabold num">0€</div>
                            <div class="text-sm text-slate-400 mt-1">Costo de uso</div>
                        </div>
                        <div>
                            <div class="text-3xl sm:text-4xl font-extrabold">CSV</div>
                            <div class="text-sm text-slate-400 mt-1">Exporta tus datos</div>
                        </div>
                        <div>
                            <div class="text-3xl sm:text-4xl font-extrabold">∞</div>
                            <div class="text-sm text-slate-400 mt-1">Transacciones</div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Tech --}}
            <section class="py-16 border-t border-slate-100 dark:border-slate-800">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-[0.2em]">Construido con</p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-x-8 gap-y-4 text-slate-700 dark:text-slate-300">
                        <span class="text-base font-semibold">Laravel 12</span>
                        <span class="text-slate-300 dark:text-slate-700">·</span>
                        <span class="text-base font-semibold">PHP 8.2</span>
                        <span class="text-slate-300 dark:text-slate-700">·</span>
                        <span class="text-base font-semibold">MySQL</span>
                        <span class="text-slate-300 dark:text-slate-700">·</span>
                        <span class="text-base font-semibold">Tailwind CSS 4</span>
                        <span class="text-slate-300 dark:text-slate-700">·</span>
                        <span class="text-base font-semibold">Chart.js 4</span>
                        <span class="text-slate-300 dark:text-slate-700">·</span>
                        <span class="text-base font-semibold">Breeze</span>
                    </div>
                </div>
            </section>
        </main>

        <footer class="py-10 border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <x-wallet-mark class="w-6 h-6" />
                    <span>© {{ date('Y') }} WalletWise. Hecho con Laravel y ☕.</span>
                </div>
                <p>MVP construido para portafolio · <a href="https://github.com" class="font-semibold text-slate-700 dark:text-slate-300 hover:text-brand-600 dark:hover:text-brand-400">Ver en GitHub →</a></p>
            </div>
        </footer>

    </body>
</html>
