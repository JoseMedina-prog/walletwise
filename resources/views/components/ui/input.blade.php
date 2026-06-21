@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'prefix' => null,
    'suffix' => null,
])

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if ($label)
        <label {{ $attributes->only('id')->merge(['class' => 'form-label'])->merge(['for' => $attributes->get('id')]) }}>
            {{ $label }}
            @if ($attributes->has('required'))
                <span class="text-expense-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($prefix)
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500 dark:text-slate-400 text-sm font-medium pointer-events-none">
                {{ $prefix }}
            </span>
        @endif

        <input {{ $attributes->except(['label', 'error', 'hint', 'prefix', 'suffix', 'class'])->merge([
            'class' => trim(($prefix ? 'pl-8 ' : '') . 'form-input'),
        ]) }}>

        @if ($suffix)
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-500 dark:text-slate-400 text-sm font-medium pointer-events-none">
                {{ $suffix }}
            </span>
        @endif

        {{ $append ?? '' }}
    </div>

    @if ($error)
        <p class="form-error">{{ $error }}</p>
    @elseif ($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif
</div>
