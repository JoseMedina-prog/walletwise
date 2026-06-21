<x-app-layout>
    <x-slot name="header">
        <x-ui.section-header title="Editar presupuesto" eyebrow="Ajusta el límite o el umbral de alerta.">
            <x-slot:action>
                <a href="{{ route('budgets.index') }}" class="btn-secondary">
                    <x-icon name="empty-box" class="w-4 h-4" /> Volver
                </a>
            </x-slot:action>
        </x-ui.section-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card padding="p-7 sm:p-8">
                <form method="POST" action="{{ route('budgets.update', $budget) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-ui.select id="category_id" name="category_id" label="Categoría" required>
                        <option value="">Selecciona una categoría…</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string) old('category_id', $budget->category_id) === (string) $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </x-ui.select>

                    <x-ui.input id="monthly_amount" name="monthly_amount" type="number" label="Importe mensual" required
                                step="0.01" min="0.01" placeholder="500.00" :value="old('monthly_amount', $budget->monthly_amount)"
                                :error="$errors->first('monthly_amount')" />

                    <div>
                        <label for="alert_threshold" class="form-label">
                            Umbral de alerta
                            <span class="text-expense-500">*</span>
                        </label>
                        <div class="flex items-center gap-3">
                            <input id="alert_threshold" name="alert_threshold" type="range"
                                   min="50" max="100" step="5"
                                   value="{{ old('alert_threshold', $budget->alert_threshold) }}"
                                   class="flex-1 accent-brand-600"
                                   oninput="document.getElementById('alert_value').textContent = this.value + '%'" />
                            <span id="alert_value" class="font-bold text-brand-600 dark:text-brand-400 num w-12 text-right">
                                {{ old('alert_threshold', $budget->alert_threshold) }}%
                            </span>
                        </div>
                        @error('alert_threshold')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <label for="is_active" class="inline-flex items-center cursor-pointer group">
                            <input id="is_active" type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $budget->is_active) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-brand-600 shadow-sm focus:ring-2 focus:ring-brand-500/30 cursor-pointer bg-white dark:bg-slate-900">
                            <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Presupuesto activo</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('budgets.index') }}" class="btn-secondary">Cancelar</a>
                        <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>