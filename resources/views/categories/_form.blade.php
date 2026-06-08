<div class="space-y-5">
    <x-ui.input id="name" name="name" type="text" maxlength="100" label="Nombre"
                :value="old('name', $category->name ?? '')" required autofocus
                placeholder="Ej: Comida, Salario, Transporte…"
                :error="$errors->first('name')" />

    <div>
        <label class="form-label">Tipo <span class="text-expense-500">*</span></label>
        @php $currentType = old('type', $category->type ?? ''); @endphp
        <div class="grid grid-cols-2 gap-3">
            <label class="relative flex items-center justify-center gap-2 p-4 border-2 rounded-xl cursor-pointer transition focus-within:ring-2 focus-within:ring-brand-500/30
                {{ $currentType === 'income' ? 'border-income-500 bg-income-50/50 dark:bg-income-950/30' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 bg-white dark:bg-slate-900' }}">
                <input type="radio" name="type" value="income" {{ $currentType === 'income' ? 'checked' : '' }} class="sr-only peer" required>
                <x-icon.income class="w-5 h-5 text-income-600 dark:text-income-400" />
                <span class="font-semibold text-sm text-slate-900 dark:text-white">Ingreso</span>
            </label>
            <label class="relative flex items-center justify-center gap-2 p-4 border-2 rounded-xl cursor-pointer transition focus-within:ring-2 focus-within:ring-brand-500/30
                {{ $currentType === 'expense' ? 'border-expense-500 bg-expense-50/50 dark:bg-expense-950/30' : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 bg-white dark:bg-slate-900' }}">
                <input type="radio" name="type" value="expense" {{ $currentType === 'expense' ? 'checked' : '' }} class="sr-only peer" required>
                <x-icon.expense class="w-5 h-5 text-expense-600 dark:text-expense-400" />
                <span class="font-semibold text-sm text-slate-900 dark:text-white">Gasto</span>
            </label>
        </div>
        @error('type')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>
</div>

<script>
    document.querySelectorAll('input[name="type"]').forEach(r => {
        r.addEventListener('change', () => {
            document.querySelectorAll('input[name="type"]').forEach(other => {
                const label = other.closest('label');
                label.classList.remove('border-income-500', 'bg-income-50/50', 'dark:bg-income-950/30',
                                       'border-expense-500', 'bg-expense-50/50', 'dark:bg-expense-950/30');
                label.classList.add('border-slate-200', 'dark:border-slate-700');
            });
            const checked = document.querySelector('input[name="type"]:checked');
            const label = checked.closest('label');
            label.classList.remove('border-slate-200', 'dark:border-slate-700');
            if (checked.value === 'income') {
                label.classList.add('border-income-500', 'bg-income-50/50', 'dark:bg-income-950/30');
            } else {
                label.classList.add('border-expense-500', 'bg-expense-50/50', 'dark:bg-expense-950/30');
            }
        });
    });
</script>
