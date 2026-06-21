<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Nuevo recurrente" eyebrow="Define una transacción que se repite automáticamente.">
            <x-slot:action>
                <a href="{{ route('recurring.index') }}" class="btn-secondary">
                    <x-icon name="empty-box" class="w-4 h-4" /> Volver
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card padding="p-7 sm:p-8">
                <form method="POST" action="{{ route('recurring.store') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.select id="type" name="type" label="Tipo" required>
                            <option value="expense" {{ old('type', 'expense') === 'expense' ? 'selected' : '' }}>Gasto</option>
                            <option value="income"  {{ old('type') === 'income' ? 'selected' : '' }}>Ingreso</option>
                        </x-ui.select>

                        <x-ui.input id="amount" name="amount" type="number" label="Importe" required
                                    step="0.01" min="0.01" placeholder="500.00" :value="old('amount')"
                                    :error="$errors->first('amount')" />
                    </div>

                    <x-ui.select id="category_id" name="category_id" label="Categoría" required>
                        <option value="">Selecciona…</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string) old('category_id') === (string) $cat->id ? 'selected' : '' }}>
                                [{{ $cat->type === 'income' ? 'I' : 'G' }}] {{ $cat->name }}
                            </option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.input id="description" name="description" type="text" label="Descripción (opcional)"
                                placeholder="Ej. Alquiler piso centro" :value="old('description')" />

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.select id="frequency" name="frequency" label="Frecuencia" required>
                            @foreach (\App\Models\RecurringTransaction::FREQUENCIES as $slug => $label)
                                <option value="{{ $slug }}" {{ old('frequency', 'monthly') === $slug ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </x-ui.select>
                        <x-ui.input id="interval" name="interval" type="number" label="Cada (N)" required
                                    min="1" max="60" :value="old('interval', 1)" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-ui.input id="start_date" name="start_date" type="date" label="Fecha de inicio" required
                                    :value="old('start_date', now()->toDateString())" />
                        <x-ui.input id="end_date" name="end_date" type="date" label="Fecha de fin (opcional)"
                                    :value="old('end_date')" />
                    </div>

                    <div class="flex items-center">
                        <label for="is_active" class="inline-flex items-center cursor-pointer group">
                            <input id="is_active" type="checkbox" name="is_active" value="1" checked
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-brand-600 shadow-sm focus:ring-2 focus:ring-brand-500/30 cursor-pointer bg-white dark:bg-slate-900">
                            <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Recurrente activo</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('recurring.index') }}" class="btn-secondary">Cancelar</a>
                        <x-ui.button type="submit" variant="primary">Crear recurrente</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>