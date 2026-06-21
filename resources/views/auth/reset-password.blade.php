<x-auth-layout>
    <x-slot name="eyebrow">Nueva contraseña</x-slot>
    <x-slot name="heading">Restablece tu contraseña</x-slot>
    <x-slot name="subheading">Elige una contraseña nueva y segura para tu cuenta.</x-slot>
    <x-slot name="footer">
        <a href="{{ route('login') }}" class="font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300">
            Volver a iniciar sesión
        </a>
    </x-slot>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-ui.input id="email" name="email" type="email" label="Correo electrónico"
                    :value="old('email', $request->email)" required autofocus
                    autocomplete="username"
                    :error="$errors->first('email')" />

        <x-ui.input id="password" name="password" type="password" label="Nueva contraseña"
                    required autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                    :error="$errors->first('password')" class="[&>div>input]:!pr-10">
            <x-slot:append>
                <x-password-toggle target="password" />
            </x-slot:append>
        </x-ui.input>

        <x-ui.input id="password_confirmation" name="password_confirmation" type="password"
                    label="Confirmar contraseña" required autocomplete="new-password"
                    placeholder="Repite la contraseña"
                    :error="$errors->first('password_confirmation')" class="[&>div>input]:!pr-10">
            <x-slot:append>
                <x-password-toggle target="password_confirmation" />
            </x-slot:append>
        </x-ui.input>

        <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
            Restablecer contraseña
        </x-ui.button>
    </form>
</x-auth-layout>
