@props(['padding' => 'p-6', 'interactive' => false])

<div {{ $attributes->merge(['class' => ($interactive ? 'card-interactive' : 'card') . ' ' . $padding]) }}>
    {{ $slot }}
</div>
