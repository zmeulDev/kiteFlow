<!-- resources/views/components/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en" style="min-height: 100vh;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0">
    <title>{{ $title ?? 'KiteFlow' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            min-height: 100vh;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            display: flex;
            flex-direction: column;
            background-color: #f8fafc; /* slate-50 foundation */
        }
        
        .dark body {
            background-color: #020617; /* slate-950 foundation */
        }

        @keyframes slideInFromLeft {
            from { opacity: 0; transform: translateX(-15px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in {
            animation: slideInFromLeft 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes pulse-soft {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        .animate-pulse-soft {
            animation: pulse-soft 2s infinite ease-in-out;
        }
        
        [x-cloak] { display: none !important; }
    </style>
    @livewireStyles
</head>
<body class="dark:bg-slate-950 selection:bg-indigo-500/30" x-data="{ sidebarOpen: false }">
    @if(session()->has('impersonator_id'))
        <div class="bg-indigo-600 text-white px-6 py-3 flex items-center justify-between text-[10px] font-black uppercase tracking-[0.2em] z-[100] flex-shrink-0 sticky top-0 shadow-lg border-b border-indigo-800">
            <div class="flex items-center">
                <span class="mr-3 animate-pulse text-lg">⚡</span> 
                GOD MODE ACTIVE: Impersonating {{ \App\Models\Tenant::find(session('tenant_id'))?->name }}
            </div>
            <a href="{{ route('superadmin.dashboard') }}" class="px-4 py-1.5 bg-white text-indigo-600 rounded-lg hover:bg-slate-100 transition-all font-black shadow-sm">Return to Admin</a>
        </div>
    @endif

    <div class="flex flex-1 relative bg-slate-50 dark:bg-slate-950 overflow-hidden">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-[60] bg-slate-950 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-[70] w-72 bg-slate-900 dark:bg-slate-900 text-white border-r border-slate-800 transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex-shrink-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center justify-between px-8 py-7 border-b border-slate-800 bg-slate-950">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-600/20">
                        <span class="text-xl">🪁</span>
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase italic">Kite<span class="text-indigo-500">Flow</span></span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">✕</button>
            </div>

            <nav class="mt-10 px-6 space-y-2 flex-1 overflow-y-auto pb-10">
                <div class="text-[10px] font-black uppercase text-slate-500 tracking-widest mb-4 ml-4">Workspace</div>
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-4 text-sm font-bold rounded-2xl transition-all group {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <span class="mr-4 text-xl {{ request()->routeIs('dashboard') ? 'scale-110' : 'opacity-70 group-hover:opacity-100 group-hover:scale-110' }} transition-all">📊</span> Dashboard
                </a>
                <a href="{{ route('calendar') }}" class="flex items-center px-4 py-4 text-sm font-bold rounded-2xl transition-all group {{ request()->routeIs('calendar') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <span class="mr-4 text-xl {{ request()->routeIs('calendar') ? 'scale-110' : 'opacity-70 group-hover:opacity-100 group-hover:scale-110' }} transition-all">📅</span> Calendar
                </a>
                <a href="{{ route('notifications') }}" class="flex items-center px-4 py-4 text-sm font-bold rounded-2xl transition-all group {{ request()->routeIs('notifications') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <div class="relative mr-4 {{ request()->routeIs('notifications') ? 'scale-110' : 'opacity-70 group-hover:opacity-100 group-hover:scale-110' }} transition-all">
                        <span class="text-xl">🔔</span>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-0.5 -right-0.5 h-2.5 w-2.5 bg-rose-500 rounded-full border-2 border-slate-900 animate-pulse"></span>
                        @endif
                    </div>
                    Notifications
                </a>
                <a href="{{ route('settings') }}" class="flex items-center px-4 py-4 text-sm font-bold rounded-2xl transition-all group {{ request()->routeIs('settings') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <span class="mr-4 text-xl {{ request()->routeIs('settings') ? 'scale-110' : 'opacity-70 group-hover:opacity-100 group-hover:scale-110' }} transition-all">⚙️</span> Settings
                </a>

                @if(auth()->user()->is_super_admin)
                    <div class="pt-6">
                        <div class="text-[10px] font-black uppercase text-slate-500 tracking-widest mb-4 ml-4">Super Powers</div>
                        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-4 text-sm font-bold rounded-2xl transition-all group text-amber-500 hover:bg-slate-800 border border-transparent hover:border-amber-500/20">
                            <span class="mr-4 text-xl group-hover:rotate-12 transition-transform">⚡</span> God Mode
                        </a>
                    </div>
                @endif
            </nav>

            <div class="p-6 border-t border-slate-800 space-y-4">
                <a href="{{ route('profile') }}" class="flex items-center space-x-4 p-4 rounded-3xl hover:bg-slate-800 transition-all text-white group border border-transparent hover:border-slate-700">
                    <div class="h-12 w-12 rounded-2xl bg-indigo-600 flex items-center justify-center font-black shadow-lg shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-black truncate">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mt-0.5">Administrator</div>
                    </div>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-bold text-slate-400 rounded-2xl hover:bg-slate-800 hover:text-white transition-all group">
                        <span class="mr-4 text-xl opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">🚪</span> Log Out
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 relative">
            <header class="flex items-center justify-between px-6 sm:px-10 py-6 bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 z-50">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 mr-4 text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight truncate uppercase italic">{{ $header ?? 'Dashboard' }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex items-center space-x-2 px-4 py-2 bg-slate-100 dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 transition-all hover:ring-2 hover:ring-indigo-500/20">
                        <span class="h-2 w-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase text-slate-600 dark:text-slate-400 tracking-widest">{{ \App\Models\Tenant::find(session('tenant_id'))?->name ?? auth()->user()->tenant->name }}</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-8 md:p-12 pb-32 bg-slate-50 dark:bg-slate-950">
                {{ $slot }}
            </main>
        </div>
    </div>
    <x-app-toast />
    <x-confirm-modal />
    @livewireScripts
</body>
</html>
