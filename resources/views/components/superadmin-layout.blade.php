<!-- projects/visiflow/resources/views/components/layouts/superadmin.blade.php -->
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KiteFlow SuperAdmin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full overflow-hidden flex" x-data="{ sidebarOpen: false }">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-slate-900/50 lg:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar -->
    <aside 
        class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-white transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex items-center justify-between px-6 py-5 border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">🪁</span>
                <span class="text-xl font-bold tracking-tight">KiteFlow</span>
            </div>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">✕</button>
        </div>

        <nav class="mt-8 px-4 space-y-2">
            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <span class="mr-3">📊</span> Dashboard
            </a>
            <a href="{{ route('superadmin.tenants') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all {{ request()->routeIs('superadmin.tenants') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <span class="mr-3">🏢</span> Tenants (Offices)
            </a>
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium text-slate-400 rounded-xl hover:bg-slate-800 hover:text-white transition-all">
                <span class="mr-3">🏠</span> Return to App
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Header -->
        <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-slate-200">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 mr-4 text-slate-500 hover:bg-slate-100 rounded-lg">☰</button>
                <h1 class="text-lg font-semibold text-slate-900">@yield('title', 'Super Admin')</h1>
            </div>
            <div class="flex items-center space-x-4">
                <div class="hidden md:block text-right">
                    <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-indigo-600 font-bold uppercase tracking-wider">Super Admin</div>
                </div>
                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">Z</div>
            </div>
        </header>

        <!-- Main View Area -->
        <main class="flex-1 overflow-y-auto p-6 md:p-10">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
