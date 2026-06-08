<div class="space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-ui.select id="type" name="type" label="Tipo" required>
            @php $currentType = old('type', $transaction->type ?? ''); @endphp
            <option value="income"  {{ $currentType === 'income'  ? 'selected' : '' }}>Ingreso</option>
            <option value="expense" {{ $currentType === 'expense' ? 'selected' : '' }}>Gasto</option>
        </x-ui.select>

        <x-ui.select id="category_id" name="category_id" label="Categoría" required placeholder="— Selecciona una categoría —">
            @php
                $currentCat = old('category_id', $transaction->category_id ?? '');
                $grouped = $categories->groupBy('type');
            @endphp
            @foreach (['income' => 'Ingresos', 'expense' => 'Gastos'] as $typeKey => $typeLabel)
                @if ($grouped->has($typeKey))
                    <optgroup label="{{ $typeLabel }}">
                        @foreach ($grouped[$typeKey] as $cat)
                            <option value="{{ $cat->id }}" data-type="{{ $cat->type }}" {{ (string) $currentCat === (string) $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </optgroup>
                @endif
            @endforeach
        </x-ui.select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-ui.input id="amount" name="amount" type="number" step="0.01" min="0.01"
                    label="Importe" prefix="$"
                    :value="old('amount', $transaction->amount ?? '')" required placeholder="0.00"
                    :error="$errors->first('amount')" />

        <x-ui.input id="transaction_date" name="transaction_date" type="date"
                    label="Fecha" required
                    :value="old('transaction_date', isset($transaction) && $transaction->transaction_date ? $transaction->transaction_date->format('Y-m-d') : date('Y-m-d'))"
                    :max="date('Y-m-d')"
                    :error="$errors->first('transaction_date')" />
    </div>

    <x-ui.input id="description" name="description" type="text" maxlength="255"
                label="Descripción (opcional)"
                hint="Empieza a escribir y te sugeriré la categoría según tu historial."
                :value="old('description', $transaction->description ?? '')"
                placeholder="Ej: Mercadona, gasolina, Netflix…"
                :error="$errors->first('description')" />
</div>

<script>
    (function () {
        const typeSelect = document.getElementById('type');
        const catSelect  = document.getElementById('category_id');
        if (!typeSelect || !catSelect) return;

        function syncCategoryOptions() {
            const t = typeSelect.value;
            Array.from(catSelect.options).forEach(opt => {
                if (!opt.value) return;
                const optType = opt.getAttribute('data-type');
                opt.hidden = optType !== t;
            });
            if (catSelect.selectedOptions[0] && catSelect.selectedOptions[0].hidden) {
                catSelect.value = '';
            }
        }

        typeSelect.addEventListener('change', syncCategoryOptions);
        syncCategoryOptions();
    })();
</script>
