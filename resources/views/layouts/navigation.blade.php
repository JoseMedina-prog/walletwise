<nav x-data="{ open: false }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 sticky top-0 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center" aria-label="WalletWise — Inicio">
                        <x-wallet-logo />
                    </a>
                </div>

                <div class="hidden space-x-1 sm:flex sm:items-center sm:ms-6">
                    <a href="{{ route('dashboard') }}"
                       @class([
                           'nav-link',
                           'nav-link-active' => request()->routeIs('dashboard'),
                       ])>
                        <x-icon.dashboard class="w-4 h-4" />
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('transactions.index') }}"
                       @class([
                           'nav-link',
                           'nav-link-active' => request()->routeIs('transactions.*'),
                       ])>
                        <x-icon.transaction class="w-4 h-4" />
                        {{ __('Transacciones') }}
                    </a>
                    <a href="{{ route('reports.index') }}"
                       @class([
                           'nav-link',
                           'nav-link-active' => request()->routeIs('reports.*'),
                       ])>
                        <x-icon.report class="w-4 h-4" />
                        {{ __('Reportes') }}
                    </a>
                    <a href="{{ route('categories.index') }}"
                       @class([
                           'nav-link',
                           'nav-link-active' => request()->routeIs('categories.*'),
                       ])>
                        <x-icon.category class="w-4 h-4" />
                        {{ __('Categorías') }}
                    </a>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="nav-link ms-2 ps-3 border-s border-slate-200 dark:border-slate-700"
                               :class="{ 'nav-link-active': request()->routeIs('admin.*') }">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                </svg>
                                Admin
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-2">
                {{-- Theme toggle --}}
                <button type="button"
                        @click="window.WWTheme.toggle()"
                        class="btn-icon"
                        :aria-label="window.WWTheme?.current() === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                        aria-label="Cambiar tema">
                    <svg x-show="window.WWTheme?.current() === 'dark'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                    <svg x-show="window.WWTheme?.current() !== 'dark'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                </button>

                {{-- User menu --}}
                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 pl-1.5 pr-3 py-1.5 rounded-full
                                       border border-slate-200 dark:border-slate-700
                                       bg-white dark:bg-slate-800
                                       hover:border-slate-300 dark:hover:border-slate-600
                                       hover:bg-slate-50 dark:hover:bg-slate-700/50
                                       transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40">
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="hidden md:inline text-sm font-semibold text-slate-700 dark:text-slate-200 max-w-[10rem] truncate">
                                {{ Auth::user()->name }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2.5">
                            <x-icon.user class="w-4 h-4 text-slate-400" />
                            {{ __('Mi perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="flex items-center gap-2.5 text-expense-600 dark:text-expense-400">
                                <x-icon class="w-4 h-4 text-current" name="empty-box" />
                                {{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center gap-1 sm:hidden">
                <button type="button" @click="window.WWTheme.toggle()" class="btn-icon" aria-label="Cambiar tema">
                    <svg x-show="window.WWTheme?.current() === 'dark'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                    </svg>
                    <svg x-show="window.WWTheme?.current() !== 'dark'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"/>
                    </svg>
                </button>
                <button @click="open = ! open" class="btn-icon" :aria-label="open ? 'Cerrar menú' : 'Abrir menú'">
                    <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="sm:hidden border-t border-slate-200 dark:border-slate-800" style="display: none;">
        <div class="pt-2 pb-3 px-2 space-y-1">
            <a href="{{ route('dashboard') }}" @class(['nav-link w-full', 'nav-link-active' => request()->routeIs('dashboard')])>
                <x-icon.dashboard class="w-4 h-4" /> Dashboard
            </a>
            <a href="{{ route('transactions.index') }}" @class(['nav-link w-full', 'nav-link-active' => request()->routeIs('transactions.*')])>
                <x-icon.transaction class="w-4 h-4" /> Transacciones
            </a>
            <a href="{{ route('reports.index') }}" @class(['nav-link w-full', 'nav-link-active' => request()->routeIs('reports.*')])>
                <x-icon.report class="w-4 h-4" /> Reportes
            </a>
            <a href="{{ route('categories.index') }}" @class(['nav-link w-full', 'nav-link-active' => request()->routeIs('categories.*')])>
                <x-icon.category class="w-4 h-4" /> Categorías
            </a>
        </div>

        <div class="pt-3 pb-3 border-t border-slate-200 dark:border-slate-800">
            <div class="px-4 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-500 to-brand-600 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="font-semibold text-sm text-slate-800 dark:text-slate-100 truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="mt-3 px-2 space-y-1">
                <a href="{{ route('profile.edit') }}" class="nav-link w-full">
                    <x-icon.user class="w-4 h-4" /> Mi perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link w-full text-expense-600 dark:text-expense-400">
                        <x-icon class="w-4 h-4" name="empty-box" /> Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
