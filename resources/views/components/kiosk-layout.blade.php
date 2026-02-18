<!-- projects/visiflow/resources/views/layouts/kiosk.blade.php -->
<!DOCTYPE html>
<html lang="en" style="min-height: 100%; height: auto;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <title>KiteFlow Kiosk</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            height: -webkit-fill-available;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
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
<body class="dark:bg-slate-950">
    <div class="w-full max-w-4xl p-4 sm:p-6">
        <div class="bg-white dark:bg-slate-900 rounded-[40px] shadow-2xl overflow-hidden border border-transparent dark:border-slate-800">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <div class="hidden md:flex bg-indigo-600 p-12 flex-col justify-between text-white">
                    <h1 class="text-3xl font-black">KiteFlow 🪁</h1>
                    <div>
                        <h2 class="text-4xl font-bold">{{ $tenant->name }}</h2>
                        <p class="mt-4 text-indigo-100 opacity-80">Welcome to our secure facility.</p>
                    </div>
                    <div class="text-xs uppercase font-black tracking-widest opacity-50">Verified Access</div>
                </div>
                <div class="p-8 sm:p-12 min-h-[450px] flex flex-col justify-center bg-white dark:bg-slate-900">
                    {{ $slot }}
                </div>
            </div>
        </div>
        <div class="mt-8 text-center text-[10px] font-black uppercase tracking-widest text-slate-400">
            &copy; 2026 KiteFlow.
        </div>
    </div>
    <x-app-toast />
    <x-confirm-modal />
    @livewireScripts
</body>
</html>
