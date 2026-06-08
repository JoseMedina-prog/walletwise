@props(['name', 'class' => 'w-5 h-5'])

@php
    $icon = "icon.{$name}";
@endphp

<x-dynamic-component :component="$icon" :class="$class" />
