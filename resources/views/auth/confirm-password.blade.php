<x-auth-layout>
    <x-slot name="eyebrow">Área segura</x-slot>
    <x-slot name="heading">Confirma tu contraseña</x-slot>
    <x-slot name="subheading">Esta es un área segura de la aplicación. Por favor, confirma tu contraseña para continuar.</x-slot>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <x-ui.input id="password" name="password" type="password" label="Contraseña"
                    required autocomplete="current-password" placeholder="••••••••"
                    :error="$errors->first('password')" />

        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
            Confirmar
        </x-ui.button>
    </form>
</x-auth-layout>
