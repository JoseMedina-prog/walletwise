<x-auth-layout>
    <x-slot name="eyebrow">Cuenta</x-slot>
    <x-slot name="heading">Inicia sesión</x-slot>
    <x-slot name="subheading">Ingresa tus credenciales para acceder a tu panel financiero.</x-slot>
    <x-slot name="footer">
        ¿No tienes cuenta?
        <a href="{{ route('register') }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300">
            Regístrate gratis
        </a>
    </x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-ui.input id="email" name="email" type="email" label="Correo electrónico"
                    :value="old('email')" required autofocus autocomplete="username"
                    placeholder="tu@correo.com"
                    :error="$errors->first('email')" />

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="form-label">Contraseña</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 font-semibold">
                        ¿La olvidaste?
                    </a>
                @endif
            </div>
            <x-ui.input id="password" name="password" type="password" required
                        autocomplete="current-password" placeholder="••••••••"
                        :error="$errors->first('password')" />
        </div>

        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox"
                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-brand-600 shadow-sm focus:ring-2 focus:ring-brand-500/30 cursor-pointer bg-white dark:bg-slate-900"
                       name="remember">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Recordarme</span>
            </label>
        </div>

        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
            Iniciar sesión
        </x-ui.button>
    </form>
</x-auth-layout>
