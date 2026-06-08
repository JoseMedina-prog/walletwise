<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'WalletWise') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 dark:text-slate-100 antialiased">
        <div class="min-h-screen flex">

            {{-- Brand panel (hidden on mobile) --}}
            <aside class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-800 to-slate-900 text-white">
                {{-- Grid pattern --}}
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff0a_1px,transparent_1px),linear-gradient(to_bottom,#ffffff0a_1px,transparent_1px)] bg-[size:32px_32px]"></div>

                {{-- Decorative blobs --}}
                <div class="absolute -top-32 -left-32 w-[28rem] h-[28rem] bg-brand-500/30 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -right-20 w-[36rem] h-[36rem] bg-income-500/20 rounded-full blur-3xl"></div>
                <div class="absolute top-1/3 right-1/4 w-72 h-72 bg-accent-400/15 rounded-full blur-2xl"></div>

                {{-- Floating wallet illustration --}}
                <div class="absolute top-1/4 right-12 opacity-25 hidden xl:block">
                    <svg viewBox="0 0 200 200" class="w-96 h-96" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="40" y="60" width="140" height="100" rx="12" fill="white" fill-opacity="0.05"/>
                        <path d="M40 75a12 12 0 0112-12h100l28 25H40V75z" fill="white" fill-opacity="0.1"/>
                        <circle cx="155" cy="115" r="14" fill="white" fill-opacity="0.1"/>
                        <path d="M155 110v10M150 115h10" stroke="white" stroke-opacity="0.5" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col justify-between p-12 xl:p-16 w-full">
                    <x-wallet-logo color="white" />

                    <div class="space-y-7 max-w-lg">
                        <div>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 ring-1 ring-white/20 text-xs font-semibold mb-5">
                                <span class="w-1.5 h-1.5 rounded-full bg-income-400"></span>
                                MVP · Laravel 12
                            </span>
                            <h1 class="text-4xl xl:text-5xl font-bold leading-[1.1] tracking-tight">
                                Tus finanzas,<br>
                                <span class="bg-gradient-to-r from-income-300 to-accent-300 bg-clip-text text-transparent">bajo control.</span>
                            </h1>
                        </div>
                        <p class="text-lg text-slate-300/90 leading-relaxed">
                            Registra ingresos y gastos, visualiza métricas, genera reportes y exporta a CSV.
                            Todo en un solo lugar, sin complicaciones.
                        </p>

                        <ul class="space-y-3.5 pt-2">
                            @foreach ([
                                'Dashboard con KPIs y gráficos en tiempo real',
                                'Filtros por fecha, tipo y categoría',
                                'Exporta tus datos a CSV cuando quieras',
                                'Multi-usuario con aislamiento estricto',
                            ] as $feature)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 flex-shrink-0 w-6 h-6 rounded-full bg-income-500/20 ring-1 ring-income-400/30 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5 text-income-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="text-slate-200">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="text-sm text-slate-400">
                        © {{ date('Y') }} WalletWise · MVP construido con Laravel 12
                    </div>
                </div>
            </aside>

            {{-- Form panel --}}
            <main class="w-full lg:w-1/2 xl:w-[45%] flex flex-col bg-slate-50 dark:bg-slate-950">
                {{-- Top bar (mobile) --}}
                <div class="lg:hidden flex items-center justify-between p-6 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur">
                    <x-wallet-logo />
                    <button type="button" @click="window.WWTheme.toggle()" class="btn-icon" aria-label="Cambiar tema">
                        <svg x-show="window.WWTheme?.current() === 'dark'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                        </svg>
                        <svg x-show="window.WWTheme?.current() !== 'dark'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
                    <div class="w-full max-w-md">
                        {{-- Header --}}
                        <div class="mb-8">
                            @isset($eyebrow)
                                <p class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-600 dark:text-brand-400 uppercase tracking-[0.12em] mb-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-600 dark:bg-brand-400"></span>
                                    {{ $eyebrow }}
                                </p>
                            @endisset
                            <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white tracking-tight">
                                {{ $heading ?? 'Bienvenido' }}
                            </h2>
                            @isset($subheading)
                                <p class="mt-2.5 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                                    {{ $subheading }}
                                </p>
                            @endisset
                        </div>

                        {{-- Form slot --}}
                        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-7 sm:p-8">
                            {{ $slot }}
                        </div>

                        {{-- Footer link --}}
                        @isset($footer)
                            <div class="mt-6 text-center text-sm text-slate-600 dark:text-slate-400">
                                {{ $footer }}
                            </div>
                        @endisset
                    </div>
                </div>
            </main>

        </div>
    </body>
</html>
