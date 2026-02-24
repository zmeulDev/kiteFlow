@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-bold leading-5 focus:outline-none transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';
$activeStyles = 'border-color: var(--primary); color: var(--text-primary);';
$inactiveStyles = 'color: var(--text-secondary); border-color: transparent;';
$activeHoverStyles = 'hover:color: var(--text-primary); hover:border-color: var(--border-light);';
$styles = ($active ?? false) ? $activeStyles : $inactiveStyles . $activeHoverStyles;
@endphp

<a {{ $attributes->merge(['class' => $classes, 'style' => $styles]) }}>
    {{ $slot }}
</a>
