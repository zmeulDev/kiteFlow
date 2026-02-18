<!-- resources/views/components/layouts/superadmin.blade.php -->
<!DOCTYPE html>
<html lang="en" style="min-height: 100vh;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, user-scalable=0">
    <title>KiteFlow - GOD MODE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js" defer></script>
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
            background-color: #020617; /* slate-950 foundation */
        }
        [x-cloak] { display: none !important; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
    @livewireStyles
</head>
<body class="dark:bg-slate-950 selection:bg-amber-500/30 text-slate-200" x-data="{ sidebarOpen: false }">
    <div class="flex flex-1 relative overflow-hidden bg-slate-950">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-[60] bg-slate-950/80 lg:hidden" @click="sidebarOpen = false"></div>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 z-[70] w-72 bg-slate-900 border-r border-slate-800 transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0 flex-shrink-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center justify-between px-8 py-7 border-b border-slate-800 bg-slate-950">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-amber-500 rounded-xl shadow-lg shadow-amber-500/20">
                        <span class="text-xl">⚡</span>
                    </div>
                    <span class="text-2xl font-black tracking-tighter uppercase italic text-white">GOD <span class="text-amber-500">MODE</span></span>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">✕</button>
            </div>

            <nav class="mt-10 px-6 space-y-2 flex-1 overflow-y-auto pb-10">
                <div class="text-[10px] font-black uppercase text-slate-500 tracking-widest mb-4 ml-4">Empire Control</div>
                <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-4 text-sm font-bold rounded-2xl transition-all group {{ request()->routeIs('superadmin.dashboard') ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <span class="mr-4 text-xl">📊</span> Dashboard
                </a>
                <a href="{{ route('superadmin.tenants') }}" class="flex items-center px-4 py-4 text-sm font-bold rounded-2xl transition-all group {{ request()->routeIs('superadmin.tenants') ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/20' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <span class="mr-4 text-xl">🏢</span> Tenants
                </a>
                
                <div class="pt-10 border-t border-slate-800 mt-10">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-4 text-sm font-bold rounded-2xl text-slate-400 hover:bg-indigo-600 hover:text-white transition-all group">
                        <span class="mr-4 text-xl group-hover:scale-110 transition-transform">🪁</span> Back to App
                    </a>
                </div>
            </nav>

            <div class="p-6 border-t border-slate-800">
                <div class="flex items-center space-x-4 p-4 rounded-3xl bg-slate-950 border border-slate-800">
                    <div class="h-10 w-10 rounded-xl bg-amber-500 flex items-center justify-center font-black text-white">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-black truncate text-white">{{ auth()->user()->name }}</div>
                        <div class="text-[10px] text-amber-500 uppercase font-black tracking-widest">Architect</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 relative">
            <header class="flex items-center justify-between px-6 sm:px-10 py-6 bg-slate-950 border-b border-slate-800 z-50">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 mr-4 text-slate-500 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-xl font-black text-white tracking-tight truncate uppercase italic">@yield('title', 'Global Overview')</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 px-4 py-2 bg-slate-900 rounded-2xl border border-slate-800 transition-all hover:ring-2 hover:ring-amber-500/20">
                        <span class="h-2 w-2 bg-amber-500 rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-black uppercase text-amber-500 tracking-widest">SUPER ADMIN</span>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-8 md:p-12 pb-32">
                {{ $slot }}
            </main>
        </div>
    </div>
    <x-app-toast />
    <x-confirm-modal />
    @livewireScripts
</body>
</html>
