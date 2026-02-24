<x-slot name="title">Visits</x-slot>

<div>
    <!-- Header -->
    <header class="visits-header">
        <div class="visits-header-top">
            <div>
                <h1 class="visits-title">Visits</h1>
                <p class="visits-subtitle">Manage visitor check-ins and access</p>
            </div>
            <div class="visits-stats">
                <div class="visits-stat">
                    <div class="visits-stat-icon visits-stat-icon--active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="visits-stat-content">
                        <div class="visits-stat-value">{{ $visits->where('status', 'checked_in')->count() }}</div>
                        <div class="visits-stat-label">On Site</div>
                    </div>
                </div>
                <div class="visits-stat">
                    <div class="visits-stat-icon visits-stat-icon--pending">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="visits-stat-content">
                        <div class="visits-stat-value">{{ $visits->where('status', 'pending')->count() }}</div>
                        <div class="visits-stat-label">Expected</div>
                    </div>
                </div>
                <div class="visits-stat">
                    <div class="visits-stat-icon visits-stat-icon--complete">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="visits-stat-content">
                        <div class="visits-stat-value">{{ $visits->where('status', 'checked_out')->count() }}</div>
                        <div class="visits-stat-label">Completed</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @if(session()->has('message'))
    <div class="badge badge-success" style="margin-bottom: 1rem; padding: 0.75rem 1rem;">
        {{ session('message') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="visits-filters">
        <div class="visits-filters-row">
            <div class="visits-filters-search">
                <svg class="visits-filters-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search visitors...">
            </div>
            <div class="visits-filters-select">
                <select wire:model.live="status_filter">
                    <option value="">All Status</option>
                    <option value="checked_in">On Site</option>
                    <option value="pending">Expected</option>
                    <option value="checked_out">Completed</option>
                </select>
            </div>
            <div class="visits-filters-select">
                <select wire:model.live="building_filter">
                    <option value="">All Buildings</option>
                    @foreach($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                    @endforeach
                </select>
            </div>
            @if($building_filter)
            <div class="visits-filters-select">
                <select wire:model.live="entrance_filter">
                    <option value="">All Entrances</option>
                    @foreach($entrances as $entrance)
                    <option value="{{ $entrance->id }}">{{ $entrance->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="visits-filters-date">
                <input type="date" wire:model.live="date_from">
            </div>
        </div>
    </div>

    <!-- Visits List -->
    <div class="visits-list">
        @forelse($visits as $visit)
        @php
            $cardClass = $visit->status === 'checked_in' ? 'active' : ($visit->status === 'pending' ? 'pending' : 'complete');
            $badgeClass = $visit->status === 'checked_in' ? 'active' : ($visit->status === 'pending' ? 'pending' : 'complete');
            $statusLabel = $visit->status === 'checked_in' ? 'On Site' : ($visit->status === 'pending' ? 'Expected' : 'Completed');
        @endphp
        <div class="visit-card visit-card--{{ $cardClass }}" wire:click="editVisit({{ $visit->id }})">
            <div class="visit-visitor">
                <div class="visit-avatar visit-avatar--{{ $cardClass }}">
                    {{ strtoupper(substr($visit->visitor->first_name ?? 'V', 0, 1) . substr($visit->visitor->last_name ?? '', 0, 1)) }}
                </div>
                <div class="visit-visitor-info">
                    <div class="visit-visitor-name">{{ $visit->visitor->full_name ?? 'Unknown' }}</div>
                    <div class="visit-visitor-meta">
                        <span class="visit-visitor-company">{{ $visit->visitor->company->name ?? 'No Company' }}</span>
                        <span class="visit-visitor-email">{{ $visit->visitor->email ?? '' }}</span>
                    </div>
                </div>
            </div>

            <div class="visit-details">
                <div class="visit-detail">
                    <div class="visit-detail-label">Host</div>
                    <div class="visit-detail-value">{{ $visit->host?->name ?? $visit->host_name ?? '-' }}</div>
                </div>
                <div class="visit-detail">
                    <div class="visit-detail-label">Location</div>
                    <div class="visit-detail-value">{{ $visit->entrance->name }}</div>
                    <div class="visit-detail-sub">{{ $visit->entrance->building->name }}</div>
                </div>
                <div class="visit-detail">
                    <div class="visit-detail-label">Check In</div>
                    <div class="visit-detail-value">{{ $visit->check_in_at?->format('H:i') ?? '--:--' }}</div>
                    <div class="visit-detail-sub">{{ $visit->check_in_at?->format('M j') ?? '' }}</div>
                </div>
            </div>

            <div class="visit-status">
                <span class="visit-status-badge visit-status-badge--{{ $badgeClass }}">
                    <span class="visit-status-dot"></span>
                    {{ $statusLabel }}
                </span>
            </div>
        </div>
        @empty
        <div class="visits-empty">
            <div class="visits-empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <p class="visits-empty-text">No visitors found</p>
        </div>
        @endforelse

        <div class="visits-pagination">
            {{ $visits->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal && $editingVisit)
    @php
        $modalClass = $editingVisit->status === 'checked_in' ? 'active' : ($editingVisit->status === 'pending' ? 'pending' : 'complete');
        $statusLabel = $editingVisit->status === 'checked_in' ? 'Currently On Site' : ($editingVisit->status === 'pending' ? 'Awaiting Arrival' : 'Visit Completed');
        $statusTime = $editingVisit->status === 'checked_in'
            ? 'Since ' . $editingVisit->check_in_at?->format('g:i A')
            : ($editingVisit->status === 'checked_out'
                ? 'Departed at ' . $editingVisit->check_out_at?->format('g:i A')
                : 'Pre-registered');
    @endphp
    <div class="visits-modal-overlay" wire:click="closeModal">
        <div class="visits-modal" x-on:click.stop>
            <!-- Modal Header -->
            <div class="visits-modal-header visits-modal-header--{{ $modalClass }}">
                <div class="visits-modal-status-icon visits-modal-status-icon--{{ $modalClass }}">
                    @if($editingVisit->status === 'checked_in')
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    @elseif($editingVisit->status === 'pending')
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    @else
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @endif
                </div>
                <div class="visits-modal-status-text visits-modal-status-text--{{ $modalClass }}">
                    <h3>{{ $statusLabel }}</h3>
                    <p>{{ $statusTime }}</p>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="visits-modal-body">
                <!-- Visitor Card -->
                <div class="visits-modal-visitor">
                    <div class="visits-modal-visitor-avatar">
                        {{ strtoupper(substr($editingVisit->visitor->first_name ?? 'V', 0, 1) . substr($editingVisit->visitor->last_name ?? '', 0, 1)) }}
                    </div>
                    <div class="visits-modal-visitor-info">
                        <h4>{{ $editingVisit->visitor->full_name }}</h4>
                        <p>{{ $editingVisit->visitor->company->name ?? 'No Company' }}</p>
                        <span>{{ $editingVisit->visitor->email }}</span>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="visits-modal-grid">
                    <div class="visits-modal-field">
                        <div class="visits-modal-field-label">Host</div>
                        <div class="visits-modal-field-value">{{ $editingVisit->host?->name ?? $editingVisit->host_name ?? '-' }}</div>
                        @if($editingVisit->host?->email ?? $editingVisit->host_email)
                        <div class="visits-modal-field-sub">{{ $editingVisit->host?->email ?? $editingVisit->host_email }}</div>
                        @endif
                    </div>
                    <div class="visits-modal-field">
                        <div class="visits-modal-field-label">Location</div>
                        <div class="visits-modal-field-value">{{ $editingVisit->entrance->name }}</div>
                        <div class="visits-modal-field-sub">{{ $editingVisit->entrance->building->name }}</div>
                    </div>
                    <div class="visits-modal-field">
                        <div class="visits-modal-field-label">Purpose</div>
                        <div class="visits-modal-field-value">{{ $editingVisit->purpose ?? 'Not specified' }}</div>
                    </div>
                    <div class="visits-modal-field">
                        <div class="visits-modal-field-label">Check In</div>
                        <div class="visits-modal-field-value">{{ $editingVisit->check_in_at?->format('M j, Y') ?? '-' }}</div>
                        <div class="visits-modal-field-sub">{{ $editingVisit->check_in_at?->format('g:i A') ?? '' }}</div>
                    </div>
                    <div class="visits-modal-field">
                        <div class="visits-modal-field-label">Check Out</div>
                        <div class="visits-modal-field-value">{{ $editingVisit->check_out_at?->format('M j, Y') ?? '-' }}</div>
                        <div class="visits-modal-field-sub">{{ $editingVisit->check_out_at?->format('g:i A') ?? '' }}</div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="visits-modal-footer">
                <button wire:click="closeModal" class="btn btn-outline">Close</button>
                @if($editingVisit->status === 'pending')
                <button wire:click="showCheckInConfirm({{ $editingVisit->id }})" class="btn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Check In
                </button>
                @elseif($editingVisit->status === 'checked_in')
                <button wire:click="showCheckOutConfirm({{ $editingVisit->id }})" class="btn btn-danger">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Check Out
                </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
