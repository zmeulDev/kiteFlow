<div x-data x-show="$wire.show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-on:keydown.escape.window="$wire.hideConfirmModal"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" x-on:click="$wire.hideConfirmModal()"></div>

    <!-- Modal -->
    <div class="relative mb-6 card overflow-hidden transform transition-all sm:w-full sm:max-w-md sm:mx-auto"
        x-show="$wire.show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

        <!-- Header -->
        <div class="px-6 py-4 border-b-light">
            <h3 class="text-lg font-semibold">{{ $title }}</h3>
        </div>

        <!-- Body -->
        <div class="px-6 py-4">
            <p class="text-secondary">{{ $message }}</p>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t-light flex justify-end gap-3">
            <button wire:click="hideConfirmModal" class="btn btn-outline">
                {{ $cancelText }}
            </button>
            @if($confirmColor === 'danger')
            <button wire:click="confirm" class="btn" style="background-color: #DC2626; color: white;">
                {{ $confirmText }}
            </button>
            @elseif($confirmColor === 'warning')
            <button wire:click="confirm" class="btn" style="background-color: #F59E0B; color: white;">
                {{ $confirmText }}
            </button>
            @else
            <button wire:click="confirm" class="btn btn-primary">
                {{ $confirmText }}
            </button>
            @endif
        </div>
    </div>
</div>
