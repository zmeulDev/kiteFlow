<div class="checkout-container kiosk-fade-in">
    <div class="card kiosk-card">
        <!-- Page Header -->
        <div class="page-header">
            <div class="page-header__icon">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <h1 class="page-header__title">Check Out</h1>
            <p class="page-header__subtitle">Find your visit and tap to check out</p>
        </div>

        <!-- Success Message -->
        @if(session()->has('message'))
        <div class="checkout-success">
            <p class="checkout-success__text">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('message') }}
            </p>
        </div>
        @endif

        <!-- Search -->
        <div class="checkout-search">
            <svg class="checkout-search__icon w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by name or host..."
                class="checkout-search__input">
        </div>

        @if($activeVisits->count() > 0)
        <!-- Visitor Count -->
        <div class="flex justify-center">
            <div class="visitor-count">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ $activeVisits->total() }} {{ Str::plural('visitor', $activeVisits->total()) }} checked in
            </div>
        </div>

        <!-- Visitor List -->
        <div class="space-y-4">
            @foreach($activeVisits as $visit)
            <div class="visitor-card flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 kiosk-fade-in" style="animation-delay: {{ $loop->index * 0.05 }}s">
                <div class="flex-1">
                    <h3 class="visitor-card__name">{{ $visit->visitor->full_name }}</h3>
                    <div class="visitor-card__meta">
                        <span>Visiting: <strong class="text-primary">{{ $visit->host?->name ?? $visit->host_name }}</strong></span>
                        <div class="visitor-card__time">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Checked in at {{ $visit->check_in_at->format('g:i A') }}
                        </div>
                    </div>
                </div>
                <button wire:click="showConfirmModal({{ $visit->id }}, '{{ $visit->visitor->full_name }}')"
                    class="checkout-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Check Out
                </button>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="checkout-pagination">
            {{ $activeVisits->links() }}
        </div>
        @else
        <!-- Empty State -->
        <div class="checkout-empty">
            <div class="checkout-empty__icon">
                <svg class="w-16 h-16 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="checkout-empty__title">No Active Visitors</h3>
            <p class="checkout-empty__subtitle">All visitors have checked out for the day.</p>
        </div>
        @endif

        <!-- Back Link -->
        <div class="mt-8 text-center pt-6 border-t border-light">
            <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" class="back-link">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Check In
            </a>
        </div>
    </div>
</div>
