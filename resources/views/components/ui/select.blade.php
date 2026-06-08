@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'placeholder' => null,
])

<div>
    @if ($label)
        <label for="{{ $attributes->get('id') }}" class="form-label">
            {{ $label }}
            @if ($attributes->has('required'))
                <span class="text-expense-500">*</span>
            @endif
        </label>
    @endif

    <select {{ $attributes->except(['label', 'error', 'hint', 'placeholder'])->merge(['class' => 'form-select']) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        {{ $slot }}
    </select>

    @if ($error)
        <p class="form-error">{{ $error }}</p>
    @elseif ($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif
</div>
