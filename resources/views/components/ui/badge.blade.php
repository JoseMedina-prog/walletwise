@props([
    'type' => 'neutral',  // income | expense | neutral | brand
    'icon' => null,
])

@php
    $classes = match($type) {
        'income'  => 'badge-income',
        'expense' => 'badge-expense',
        'brand'   => 'badge-brand',
        default   => 'badge-neutral',
    };
    $defaultIcon = match($type) {
        'income'  => 'income',
        'expense' => 'expense',
        default   => null,
    };
    $iconName = $icon ?? $defaultIcon;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($iconName)
        <x-icon :name="$iconName" class="w-3 h-3" />
    @endif
    {{ $slot }}
</span>
