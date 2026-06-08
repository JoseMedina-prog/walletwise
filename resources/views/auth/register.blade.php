<x-auth-layout>
    <x-slot name="eyebrow">Empieza gratis</x-slot>
    <x-slot name="heading">Crea tu cuenta</x-slot>
    <x-slot name="subheading">Empieza a controlar tus finanzas en menos de un minuto.</x-slot>
    <x-slot name="footer">
        ¿Ya tienes cuenta?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300">
            Inicia sesión
        </a>
    </x-slot>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-ui.input id="name" name="name" type="text" label="Nombre"
                    :value="old('name')" required autofocus autocomplete="name"
                    placeholder="Tu nombre"
                    :error="$errors->first('name')" />

        <x-ui.input id="email" name="email" type="email" label="Correo electrónico"
                    :value="old('email')" required autocomplete="username"
                    placeholder="tu@correo.com"
                    :error="$errors->first('email')" />

        <x-ui.input id="password" name="password" type="password" label="Contraseña"
                    required autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                    :error="$errors->first('password')" />

        <x-ui.input id="password_confirmation" name="password_confirmation" type="password"
                    label="Confirmar contraseña" required autocomplete="new-password"
                    placeholder="Repite tu contraseña"
                    :error="$errors->first('password_confirmation')" />

        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
            Crear cuenta
        </x-ui.button>
    </form>
</x-auth-layout>
