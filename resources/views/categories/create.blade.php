<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Nueva categoría" eyebrow="Crea una categoría para organizar tus transacciones." :back="route('categories.index')" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card padding="p-7 sm:p-8">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    @include('categories._form')

                    <div class="flex items-center justify-end mt-7 gap-3 pt-5 border-t border-slate-200 dark:border-slate-800">
                        <a href="{{ route('categories.index') }}" class="btn-secondary">Cancelar</a>
                        <x-ui.button type="submit" variant="primary" icon="check">Guardar</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
