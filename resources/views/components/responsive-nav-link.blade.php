@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium focus:outline-none transition duration-150 ease-in-out';
$activeStyles = 'border-color: var(--primary); color: var(--primary); background-color: rgba(255, 75, 75, 0.1);';
$inactiveStyles = 'color: var(--text-secondary);';
$activeHoverStyles = 'hover:color: var(--text-primary); hover:background-color: var(--bg-main); hover:border-color: var(--border-light);';
$styles = ($active ?? false) ? $activeStyles : $inactiveStyles . $activeHoverStyles;
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => $styles]) }}>
    {{ $slot }}
</a>
