@props(['active' => false])

<a {{ $attributes->merge(['class' => $active ? 'inline-flex items-center px-1 pt-1 border-b-2 border-primary-500 text-sm font-medium text-gray-900' : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300']) }}>
    {{ $slot }}
</a>