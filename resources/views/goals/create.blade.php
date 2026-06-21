<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Nueva meta" eyebrow="Define un objetivo de ahorro y su fecha límite.">
            <x-slot:action>
                <a href="{{ route('goals.index') }}" class="btn-secondary">
                    <x-icon name="empty-box" class="w-4 h-4" /> Volver
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card padding="p-7 sm:p-8">
                <form method="POST" action="{{ route('goals.store') }}" class="space-y-5">
                    @csrf

                    <x-ui.input id="name" name="name" type="text" label="Nombre" required
                                placeholder="Ej. Vacaciones de verano" :value="old('name')" />

                    <x-ui.input id="description" name="description" type="text" label="Descripción (opcional)"
                                placeholder="¿Para qué es esta meta?" :value="old('description')" />

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.input id="target_amount" name="target_amount" type="number" label="Objetivo" required
                                    step="0.01" min="0.01" placeholder="5000.00" :value="old('target_amount')" />
                        <x-ui.input id="current_amount" name="current_amount" type="number" label="Ya ahorrado (opcional)"
                                    step="0.01" min="0" placeholder="0.00" :value="old('current_amount', 0)" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.input id="start_date" name="start_date" type="date" label="Fecha de inicio (opcional)"
                                    :value="old('start_date', now()->toDateString())" />
                        <x-ui.input id="target_date" name="target_date" type="date" label="Fecha objetivo (opcional)"
                                    :value="old('target_date')" />
                    </div>

                    <x-ui.input id="color" name="color" type="color" label="Color"
                                :value="old('color', '#10b981')" />

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('goals.index') }}" class="btn-secondary">Cancelar</a>
                        <x-ui.button type="submit" variant="primary">Crear meta</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>