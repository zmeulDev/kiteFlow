<div class="p-4 bg-white rounded-lg border">
    <p class="mb-2">Count: {{ $count }}</p>
    <button wire:click="increment" class="px-4 py-2 bg-blue-600 text-white rounded">
        Click Me (wire:click)
    </button>
    <button onclick="alert('JS works!')" class="ml-2 px-4 py-2 bg-green-600 text-white rounded">
        Test JS (onclick)
    </button>
</div>