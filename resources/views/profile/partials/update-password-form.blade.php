<section>
    <header class="mb-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Actualizar contraseña</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Asegúrate de que tu cuenta utilice una contraseña larga y aleatoria para mantenerse segura.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <x-ui.input id="update_password_current_password" name="current_password" type="password"
                    label="Contraseña actual" autocomplete="current-password"
                    :error="$errors->updatePassword->first('current_password')" />

        <x-ui.input id="update_password_password" name="password" type="password"
                    label="Nueva contraseña" autocomplete="new-password"
                    :error="$errors->updatePassword->first('password')" />

        <x-ui.input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    label="Confirmar contraseña" autocomplete="new-password"
                    :error="$errors->updatePassword->first('password_confirmation')" />

        <div class="flex items-center gap-4 pt-2">
            <x-ui.button type="submit" variant="primary">Guardar</x-ui.button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm font-medium text-income-600 dark:text-income-400">Guardado.</p>
            @endif
        </div>
    </form>
</section>
