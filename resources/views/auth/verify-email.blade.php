<x-auth-layout>
    <x-slot name="eyebrow">Verificación</x-slot>
    <x-slot name="heading">Confirma tu correo</x-slot>
    <x-slot name="subheading">Gracias por registrarte. Antes de empezar, confirma tu correo haciendo clic en el enlace que te acabamos de enviar. Si no lo recibiste, te lo enviaremos de nuevo.</x-slot>

    @if (session('status') == 'verification-link-sent')
        <x-ui.alert type="success" class="mb-4">
            Se ha enviado un nuevo enlace de verificación a tu correo.
        </x-ui.alert>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                Reenviar correo de verificación
            </x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold underline py-2 transition">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-auth-layout>
