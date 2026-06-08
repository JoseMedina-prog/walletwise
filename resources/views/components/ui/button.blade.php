@props([
    'variant' => 'primary',  // primary | secondary | ghost | danger | soft
    'size' => 'md',          // sm | md | lg
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $variantClass = match($variant) {
        'secondary' => 'btn-secondary',
        'ghost'     => 'btn-ghost',
        'danger'    => 'btn-danger',
        'soft'      => 'btn-soft',
        default     => 'btn-primary',
    };
    $sizeClass = match($size) {
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => '',
    };
    $class = trim("$variantClass $sizeClass " . ($attributes->get('class') ?? ''));
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
        @if ($icon)<x-icon :name="$icon" class="w-4 h-4" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>
        @if ($icon)<x-icon :name="$icon" class="w-4 h-4" />@endif
        {{ $slot }}
    </button>
@endif
