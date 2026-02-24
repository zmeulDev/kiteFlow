<div class="w-full max-w-2xl mx-auto">
    <div class="card kiosk-card">
        <h2 class="text-2xl font-bold mb-4 text-center">Check Out</h2>
        <p class="text-secondary mb-6 text-center">Find your visit below and tap Check Out when ready to leave.</p>

        @if(session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-medium text-center">
            {{ session('message') }}
        </div>
        @endif

        <div class="mb-6">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or host..."
                class="input kiosk-input">
        </div>

        @if($activeVisits->count() > 0)
        <div class="space-y-4">
            @foreach($activeVisits as $visit)
            <div class="bg-main rounded-lg p-5 flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-lg">{{ $visit->visitor->full_name }}</h3>
                    <p class="text-sm text-secondary">Visiting: {{ $visit->host?->name ?? $visit->host_name }}</p>
                    <p class="text-xs text-muted mt-1">Checked in: {{ $visit->check_in_at->format('g:i A') }}</p>
                </div>
                <button wire:click="showConfirmModal({{ $visit->id }}, '{{ $visit->visitor->full_name }}')"
                    class="btn btn-danger kiosk-btn">Check Out</button>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $activeVisits->links() }}
        </div>
        @else
        <div class="text-center py-10 text-muted">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <p>No active visitors found</p>
        </div>
        @endif

        <div class="mt-8 text-center">
            <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" class="link">Back to Check In</a>
        </div>
    </div>
</div>