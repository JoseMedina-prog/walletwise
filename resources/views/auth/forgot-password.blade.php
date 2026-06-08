<x-auth-layout>
    <x-slot name="eyebrow">Recuperar acceso</x-slot>
    <x-slot name="heading">¿Olvidaste tu contraseña?</x-slot>
    <x-slot name="subheading">Sin problema. Ingresa tu correo y te enviaremos un enlace para crear una nueva.</x-slot>
    <x-slot name="footer">
        ¿Te acordaste?
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300">
            Volver a iniciar sesión
        </a>
    </x-slot>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <x-ui.input id="email" name="email" type="email" label="Correo electrónico"
                    :value="old('email')" required autofocus
                    placeholder="tu@correo.com"
                    :error="$errors->first('email')" />

        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
            Enviar enlace de recuperación
        </x-ui.button>
    </form>
</x-auth-layout>
