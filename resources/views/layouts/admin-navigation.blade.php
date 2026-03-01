<nav x-data="{ open: false, profileOpen: false, settingsOpen: false }" class="admin-nav">
    <div class="admin-nav-container">
        <div class="admin-nav-inner">
            {{-- Logo & Brand --}}
            <div class="admin-nav-brand">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-logo">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span class="admin-nav-logo-text">Visitor Tracker</span>
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <div class="admin-nav-links">
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'admin-nav-link--active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.reports') }}" class="admin-nav-link {{ request()->routeIs('admin.reports') ? 'admin-nav-link--active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <span>Reports</span>
                </a>

                @can('viewVisits', App\Models\User::class)
                <a href="{{ route('admin.visits') }}" class="admin-nav-link {{ request()->routeIs('admin.visits') ? 'admin-nav-link--active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span>Visits</span>
                </a>
                @endcan

                @if(auth()->user()->can('viewCompanies', App\Models\User::class) || auth()->user()->can('viewBuildings', App\Models\User::class) || auth()->user()->can('viewUsers', App\Models\User::class))
                <div class="admin-nav-dropdown" x-data="{ open: false }">
                    <button @click="open = !open" class="admin-nav-link admin-nav-link--dropdown {{ request()->routeIs('admin.companies') || request()->routeIs('admin.buildings') || request()->routeIs('admin.users') ? 'admin-nav-link--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                        <span>Management</span>
                        <svg class="admin-nav-dropdown-arrow" :class="{ 'admin-nav-dropdown-arrow--open': open }" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="admin-nav-dropdown-menu" x-show="open" @click.away="open = false" x-transition>
                        @can('viewCompanies', App\Models\User::class)
                        <a href="{{ route('admin.companies') }}" class="admin-nav-dropdown-link {{ request()->routeIs('admin.companies') ? 'admin-nav-dropdown-link--active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Companies
                        </a>
                        @endcan

                        @can('viewBuildings', App\Models\User::class)
                        <a href="{{ route('admin.buildings') }}" class="admin-nav-dropdown-link {{ request()->routeIs('admin.buildings') ? 'admin-nav-dropdown-link--active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="3" x2="9" y2="21"></line>
                            </svg>
                            Buildings
                        </a>
                        @endcan

                        @can('viewUsers', App\Models\User::class)
                        <a href="{{ route('admin.users') }}" class="admin-nav-dropdown-link {{ request()->routeIs('admin.users') ? 'admin-nav-dropdown-link--active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Users
                        </a>
                        @endcan
                    </div>
                </div>
                @endif

                @can('manageSettings', App\Models\User::class)
                <div class="admin-nav-dropdown" x-data="{ open: false }">
                    <button @click="open = !open" class="admin-nav-link admin-nav-link--dropdown {{ request()->routeIs('admin.settings.*') ? 'admin-nav-link--active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        <span>Settings</span>
                        <svg class="admin-nav-dropdown-arrow" :class="{ 'admin-nav-dropdown-arrow--open': open }" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="admin-nav-dropdown-menu" x-show="open" @click.away="open = false" x-transition>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.settings.business') }}" class="admin-nav-dropdown-link {{ request()->routeIs('admin.settings.business') ? 'admin-nav-dropdown-link--active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            Business Details
                        </a>
                        <a href="{{ route('admin.settings.gdpr') }}" class="admin-nav-dropdown-link {{ request()->routeIs('admin.settings.gdpr') ? 'admin-nav-dropdown-link--active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            GDPR Settings
                        </a>
                        <a href="{{ route('admin.settings.nda') }}" class="admin-nav-dropdown-link {{ request()->routeIs('admin.settings.nda') ? 'admin-nav-dropdown-link--active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                            NDA Settings
                        </a>
                        </a>
                        @endif
                        <a href="{{ route('admin.settings.rbac') }}" class="admin-nav-dropdown-link {{ request()->routeIs('admin.settings.rbac') ? 'admin-nav-dropdown-link--active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            RBAC
                        </a>
                    </div>
                </div>
                @endcan
            </div>

            {{-- User Menu --}}
            <div class="admin-nav-user">
                <div class="admin-nav-dropdown" x-data="{ open: false }">
                    <button @click="open = !open" class="admin-nav-user-trigger">
                        <div class="admin-nav-user-avatar">
                            {{ collect(explode(' ', Auth::user()->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                        </div>
                        <div class="admin-nav-user-info">
                            <span class="admin-nav-user-name">{{ Auth::user()->name }}</span>
                            <span class="admin-nav-user-role">{{ \App\Models\User::getRoles()[Auth::user()->role] ?? ucfirst(Auth::user()->role) }}</span>
                        </div>
                        <svg class="admin-nav-dropdown-arrow" :class="{ 'admin-nav-dropdown-arrow--open': open }" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="admin-nav-dropdown-menu admin-nav-dropdown-menu--right" x-show="open" @click.away="open = false" x-transition>
                        <a href="{{ route('profile.edit') }}" class="admin-nav-dropdown-link">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="admin-nav-dropdown-link admin-nav-dropdown-link--logout">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="open = !open" class="admin-nav-mobile-toggle">
                <svg x-show="!open" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
                <svg x-show="open" x-cloak width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-cloak class="admin-nav-mobile" x-transition>
        <div class="admin-nav-mobile-links">
            <a href="{{ route('admin.dashboard') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.dashboard') ? 'admin-nav-mobile-link--active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.reports') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.reports') ? 'admin-nav-mobile-link--active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                Reports
            </a>

            @can('viewVisits', App\Models\User::class)
            <a href="{{ route('admin.visits') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.visits') ? 'admin-nav-mobile-link--active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Visits
            </a>
            @endcan

            @if(auth()->user()->can('manageCompanies', App\Models\User::class) || auth()->user()->can('manageBuildings', App\Models\User::class) || auth()->user()->can('manageUsers', App\Models\User::class))
            <div class="admin-nav-mobile-section">Management</div>
            
            @can('manageCompanies', App\Models\User::class)
            <a href="{{ route('admin.companies') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.companies') ? 'admin-nav-mobile-link--active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Companies
            </a>
            @endcan

            @can('manageBuildings', App\Models\User::class)
            <a href="{{ route('admin.buildings') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.buildings') ? 'admin-nav-mobile-link--active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                </svg>
                Buildings
            </a>
            @endcan

            @can('manageUsers', App\Models\User::class)
            <a href="{{ route('admin.users') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.users') ? 'admin-nav-mobile-link--active' : '' }}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Users
            </a>
            @endcan
            @endif

            @can('manageSettings', App\Models\User::class)
            <div class="admin-nav-mobile-section">Settings</div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.settings.business') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.settings.business') ? 'admin-nav-mobile-link--active' : '' }}">
                Business Details
            </a>
            <a href="{{ route('admin.settings.gdpr') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.settings.gdpr') ? 'admin-nav-mobile-link--active' : '' }}">
                GDPR Settings
            </a>
            <a href="{{ route('admin.settings.nda') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.settings.nda') ? 'admin-nav-mobile-link--active' : '' }}">
                NDA Settings
            </a>
            </a>
            @endif
            <a href="{{ route('admin.settings.rbac') }}" class="admin-nav-mobile-link {{ request()->routeIs('admin.settings.rbac') ? 'admin-nav-mobile-link--active' : '' }}">
                RBAC
            </a>
            @endcan
        </div>

        <div class="admin-nav-mobile-footer">
            <div class="admin-nav-mobile-user">
                <div class="admin-nav-user-avatar">
                    {{ collect(explode(' ', Auth::user()->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                </div>
                <div class="admin-nav-user-info">
                    <span class="admin-nav-user-name">{{ Auth::user()->name }}</span>
                    <span class="admin-nav-user-email">{{ Auth::user()->email }}</span>
                </div>
            </div>
            <div class="admin-nav-mobile-actions">
                <a href="{{ route('profile.edit') }}" class="admin-nav-mobile-action">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-nav-mobile-action admin-nav-mobile-action--logout">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
[x-cloak] { display: none !important; }
</style>
