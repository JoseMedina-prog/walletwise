<section class="space-y-5">
    <header>
        <h2 class="text-lg font-bold text-expense-700 dark:text-expense-400">Eliminar cuenta</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Una vez que tu cuenta sea eliminada, todos sus recursos y datos se borrarán permanentemente. Antes de eliminarla, descarga cualquier dato o información que desees conservar.
        </p>
    </header>

    <x-ui.button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        Eliminar cuenta
    </x-ui.button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                ¿Estás seguro de que quieres eliminar tu cuenta?
            </h2>

            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                Esta acción es permanente. Todos tus recursos y datos se eliminarán de forma definitiva. Ingresa tu contraseña para confirmar.
            </p>

            <div class="mt-5">
                <x-ui.input id="password" name="password" type="password"
                            label="Contraseña" placeholder="Tu contraseña"
                            :error="$errors->userDeletion->first('password')" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close')">Cancelar</x-ui.button>
                <x-ui.button type="submit" variant="danger">Eliminar cuenta</x-ui.button>
            </div>
        </form>
    </x-modal>
</section>
