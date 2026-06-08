<section>
    <header class="mb-6">
        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Información del perfil</h2>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Actualiza tu nombre y dirección de correo electrónico.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <x-ui.input id="name" name="name" type="text" label="Nombre"
                    :value="old('name', $user->name)" required autofocus autocomplete="name"
                    :error="$errors->first('name')" />

        <div>
            <x-ui.input id="email" name="email" type="email" label="Correo electrónico"
                        :value="old('email', $user->email)" required autocomplete="username"
                        :error="$errors->first('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3">
                    <p class="text-sm text-slate-700 dark:text-slate-300">
                        Tu dirección de correo no está verificada.
                        <button form="send-verification" class="underline font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40">
                            Haz clic aquí para reenviar el correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm font-medium text-income-600 dark:text-income-400">
                            Se ha enviado un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-ui.button type="submit" variant="primary">Guardar</x-ui.button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm font-medium text-income-600 dark:text-income-400">Guardado.</p>
            @endif
        </div>
    </form>
</section>
