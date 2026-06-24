@props([
    'href' => '#',
    'active' => false
])

@php
$classes = $active
    ? 'bg-primary text-primary-content font-bold'
    : 'hover:bg-base-100 hover:text-base-content';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "flex block py-2 px-2 transition-colors is-drawer-close:tooltip is-drawer-close:tooltip-right rounded-xl $classes"]) }}>
    {{ $slot }}
</a>
