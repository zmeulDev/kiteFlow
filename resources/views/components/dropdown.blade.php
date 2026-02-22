@props(['align' => 'right', 'width' => '48'])

<div class="relative" x-data="{ open: false }" @click.away="open = false" @keydown.escape.window="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>
    
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute {{ $align === 'left' ? 'left-0' : 'right-0' }} z-50 mt-2 w-{{ $width }} rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
         style="display: none;">
        <div class="py-1">
            {{ $content }}
        </div>
    </div>
</div>