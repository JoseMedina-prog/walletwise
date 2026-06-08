<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Editar transacción" eyebrow="Modifica los datos de la transacción." :back="route('transactions.index')" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card padding="p-7 sm:p-8">
                <form method="POST" action="{{ route('transactions.update', $transaction) }}">
                    @csrf
                    @method('PUT')
                    @include('transactions._form')

                    <div class="flex items-center justify-end mt-7 gap-3 pt-5 border-t border-slate-200 dark:border-slate-800">
                        <a href="{{ route('transactions.index') }}" class="btn-secondary">Cancelar</a>
                        <x-ui.button type="submit" variant="primary" icon="check">Actualizar transacción</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
