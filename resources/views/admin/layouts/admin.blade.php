<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>Admin · {{ config('app.name', 'WalletWise') }} — @yield('title', 'Panel')</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

        <div class="min-h-screen bg-slate-50 dark:bg-slate-950">
            {{-- Admin topbar --}}
            <header class="bg-slate-900 dark:bg-slate-950 text-white border-b border-slate-800 sticky top-0 z-30">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center gap-6">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5" aria-label="Panel de administración">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-brand-500/20 ring-1 ring-brand-400/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4 text-brand-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                    </svg>
                                </span>
                                <span class="font-bold text-base tracking-tight">Admin · WalletWise</span>
                            </a>
                            <nav class="hidden md:flex items-center gap-1 ms-4">
                                <a href="{{ route('admin.dashboard') }}" @class([
                                    'px-3 h-9 inline-flex items-center gap-2 rounded-lg text-sm font-medium transition',
                                    'bg-white/10 text-white' => request()->routeIs('admin.dashboard*'),
                                    'text-slate-300 hover:text-white hover:bg-white/5' => ! request()->routeIs('admin.dashboard*'),
                                ])>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                                    Métricas
                                </a>
                                <a href="{{ route('admin.users.index') }}" @class([
                                    'px-3 h-9 inline-flex items-center gap-2 rounded-lg text-sm font-medium transition',
                                    'bg-white/10 text-white' => request()->routeIs('admin.users.*'),
                                    'text-slate-300 hover:text-white hover:bg-white/5' => ! request()->routeIs('admin.users.*'),
                                ])>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                    Usuarios
                                </a>
                            </nav>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 h-9 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V12m-6-6l6 6m0 0v-5.25M18 12V6.75"/></svg>
                                Ir a la app
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 h-9 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-white/5 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                                    Salir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main id="main-content" class="py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @isset($header)
                        <div class="mb-6">{{ $header }}</div>
                    @endisset
                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>
