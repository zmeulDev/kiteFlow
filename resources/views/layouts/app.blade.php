<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} | KiteFlow</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Plus+Jakarta+Sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>[x-cloak] { display: none !important; }</style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 m-0 p-0">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen w-full">
        
        <!-- Sidebar -->
        <aside class="w-72 h-full bg-white border-r border-gray-200 flex flex-col flex-shrink-0">
            <!-- Logo -->
            <div class="flex items-center h-16 px-5 border-b border-gray-100 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900">KiteFlow</span>
                </a>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto" x-data="{ settingsOpen: false }">
                <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Main</div>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-grid-2 {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-gray-400' }}"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('visitors.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('visitors.*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-users {{ request()->routeIs('visitors.*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Visitors</span>
                </a>

                <a href="{{ route('meetings.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('meetings.*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-calendar-days {{ request()->routeIs('meetings.*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Meetings</span>
                </a>

                <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('reports.*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-chart-line {{ request()->routeIs('reports.*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Reports</span>
                </a>

                <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Facilities</div>

                <a href="/meeting-rooms" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('meeting-rooms.*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-door-open {{ request()->routeIs('meeting-rooms.*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Meeting Rooms</span>
                </a>

                <a href="/access-points" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('access-points.*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-id-card {{ request()->routeIs('access-points.*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Access Points</span>
                </a>

                <a href="/facilities" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('facilities.*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-building {{ request()->routeIs('facilities.*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Buildings</span>
                </a>

                @auth
                @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->hasRole('admin')))
                <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</div>

                @if(auth()->user()->isSuperAdmin())
                <a href="{{ route('settings.tenants') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.tenants*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-building {{ request()->routeIs('settings.tenants*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Tenants</span>
                </a>
                @endif

                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('settings.subtenants') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.subtenants') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-building {{ request()->routeIs('settings.subtenants') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Sub-Tenants</span>
                </a>
                @endif

                <a href="{{ route('settings.users') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.users') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="w-5 h-5 fa-solid fa-user-group {{ request()->routeIs('settings.users') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                    <span>Users</span>
                </a>

                <!-- Settings with Submenu -->
                <div>
                    <button @click="settingsOpen = !settingsOpen" class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <div class="flex items-center gap-3">
                            <i class="w-5 h-5 fa-solid fa-gear {{ request()->routeIs('settings.*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            <span>Settings</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="settingsOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="settingsOpen" x-transition class="ml-8 mt-1 space-y-1">
                        <!-- Available to both admin and super-admin -->
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->hasRole('admin'))
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.index') && !request()->routeIs('settings.*', '*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="w-4 h-4 fa-solid fa-sliders {{ request()->routeIs('settings.index') && !request()->routeIs('settings.*', '*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            <span>General</span>
                        </a>
                        <a href="{{ route('settings.kiosk') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.kiosk') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="w-4 h-4 fa-solid fa-desktop {{ request()->routeIs('settings.kiosk') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            <span>Kiosk</span>
                        </a>
                        @endif

                        <!-- Super admin only -->
                        @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('settings.billing') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.billing') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="w-4 h-4 fa-solid fa-credit-card {{ request()->routeIs('settings.billing') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            <span>Billing</span>
                        </a>
                        <a href="{{ route('settings.system') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.system*') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="w-4 h-4 fa-solid fa-shield-halved {{ request()->routeIs('settings.system*') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            <span>System</span>
                        </a>
                        <a href="{{ route('settings.integrations') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.integrations') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="w-4 h-4 fa-solid fa-plug {{ request()->routeIs('settings.integrations') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            <span>Integrations</span>
                        </a>
                        <a href="{{ route('settings.support') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.support') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="w-4 h-4 fa-solid fa-headset {{ request()->routeIs('settings.support') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            <span>Support</span>
                        </a>
                        @endif
                        @if(auth()->user()->hasRole('admin') || auth()->user()->isSuperAdmin())
                        <a href="{{ route('settings.system-logs') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all group {{ request()->routeIs('settings.system-logs') ? 'bg-brand-600/10 text-brand-600' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            <i class="w-4 h-4 fa-solid fa-clipboard-list {{ request()->routeIs('settings.system-logs') ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
                            <span>Audit Logs</span>
                        </a>
                        @endif
                    </div>
                </div>
                @endif
                @endauth
            </nav>
            
            <!-- User Section -->
            <div class="border-t border-gray-100 p-4 bg-gray-50 flex-shrink-0">
                @auth
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white transition-colors cursor-pointer">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 text-white font-semibold text-sm flex-shrink-0">
                        {{ strtoupper(auth()->user()->name[0]) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Logout">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
                @endauth
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-full min-w-0 overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white/80 backdrop-blur-lg border-b border-gray-100 flex-shrink-0">
                <div class="flex items-center justify-between h-16 px-6 gap-2">
                    <div class="flex-1">
                        <h1 class="text-lg font-semibold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <a href="{{ route('visitors.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-xl hover:bg-brand-700 hover:shadow-lg hover:shadow-brand-500/25 transition-all">
                            <i class="fa-solid fa-plus"></i>
                            <span>Check In</span>
                        </a>
                        
                        <button class="relative p-2.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-colors">
                            <i class="fa-solid fa-bell text-lg"></i>
                            <span class="absolute top-2 right-2 w-2 h-2 bg-brand-500 rounded-full ring-2 ring-white"></span>
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="flex-1 p-6 overflow-y-auto">
                {{ $slot }}
            </div>
        </main>
    </div>
    
    @if(session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
         class="fixed bottom-4 right-6 z-50 max-w-sm bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="flex items-center gap-3 p-4">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 flex-shrink-0">
                <i class="fa-solid fa-check text-emerald-600"></i>
            </div>
            <p class="text-sm font-medium text-gray-900 flex-1">{{ session('message') }}</p>
            <button @click="show = false" class="p-1 text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
    @endif
    
    @livewireScripts
    @stack('scripts')
</body>
</html>