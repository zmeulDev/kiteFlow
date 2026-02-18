<!-- projects/visiflow/resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KiteFlow | Modern Visitor Management for Offices</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">
    <!-- Hero Section -->
    <header class="relative overflow-hidden bg-white pt-16 pb-32">
        <nav class="max-w-7xl mx-auto px-6 flex items-center justify-between mb-24">
            <div class="flex items-center space-x-3">
                <span class="text-3xl">🪁</span>
                <span class="text-2xl font-extrabold tracking-tighter">KiteFlow</span>
            </div>
            <div class="flex items-center space-x-8">
                <a href="#features" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Features</a>
                <a href="#pricing" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Pricing</a>
                <a href="{{ route('login') }}" class="inline-flex h-10 items-center justify-center rounded-full bg-indigo-600 px-6 text-sm font-bold text-white shadow-lg hover:bg-indigo-700 transition-all">Sign In</a>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-6 text-center">
            <h1 class="text-6xl md:text-7xl font-extrabold tracking-tight mb-8">
                The <span class="text-indigo-600">lightweight</span> way to <br> manage your office flow.
            </h1>
            <p class="max-w-2xl mx-auto text-xl text-slate-500 mb-12">
                Replace your messy paper logs with a premium, digital kiosk. 
                Fast pass check-ins, instant host alerts, and powerful analytics.
            </p>
            <div class="flex flex-col md:flex-row items-center justify-center gap-4">
                <button class="w-full md:w-auto h-14 px-10 rounded-2xl bg-slate-900 text-white font-bold text-lg shadow-xl hover:bg-slate-800 transition-all active:scale-[0.98]">
                    Start Free Trial
                </button>
                <a href="{{ route('kiosk', ['tenant' => 'jucu-hub']) }}" target="_blank" class="w-full md:w-auto h-14 px-10 rounded-2xl border-2 border-slate-200 flex items-center justify-center font-bold text-lg hover:bg-slate-50 transition-all">
                    View Demo Kiosk
                </a>
            </div>
        </div>
    </header>

    <!-- Social Proof -->
    <section class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="space-y-4">
                    <div class="h-12 w-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-xl font-bold">⚡</div>
                    <h3 class="text-xl font-bold">Fast Pass Check-ins</h3>
                    <p class="text-slate-500 leading-relaxed">Let recurring visitors and invited guests check in with a single tap using their personal QR code.</p>
                </div>
                <div class="space-y-4">
                    <div class="h-12 w-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-xl font-bold">🔔</div>
                    <h3 class="text-xl font-bold">Instant Notifications</h3>
                    <p class="text-slate-500 leading-relaxed">No more "Someone is at the front desk for you." Hosts get instant Email and WhatsApp alerts the moment a guest arrives.</p>
                </div>
                <div class="space-y-4">
                    <div class="h-12 w-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-xl font-bold">📈</div>
                    <h3 class="text-xl font-bold">Deep Analytics</h3>
                    <p class="text-slate-500 leading-relaxed">Track peak office hours, visitor frequency, and stay times to optimize your reception staff and office resources.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section id="pricing" class="py-32">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold mb-4">Simple, transparent pricing.</h2>
            <p class="text-slate-500 mb-20 text-lg">No hidden fees. Scale with your business.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Free Plan -->
                <div class="p-10 rounded-[32px] border-2 border-slate-100 text-left hover:border-slate-200 transition-all">
                    <h3 class="text-xl font-bold mb-2">Starter</h3>
                    <div class="text-4xl font-extrabold mb-6">$0 <span class="text-sm font-normal text-slate-400">/mo</span></div>
                    <ul class="space-y-4 mb-10 text-slate-600">
                        <li class="flex items-center space-x-2"><span>✅</span> <span>Up to 50 visitors /mo</span></li>
                        <li class="flex items-center space-x-2"><span>✅</span> <span>Basic Email Alerts</span></li>
                        <li class="flex items-center space-x-2"><span>✅</span> <span>1 Office Location</span></li>
                    </ul>
                    <button class="w-full h-12 rounded-xl bg-slate-100 text-slate-900 font-bold hover:bg-slate-200 transition-all">Get Started</button>
                </div>
                <!-- Pro Plan -->
                <div class="p-10 rounded-[32px] border-2 border-indigo-600 text-left relative shadow-2xl shadow-indigo-200">
                    <span class="absolute top-0 right-10 -translate-y-1/2 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest px-4 py-1 rounded-full">Recommended</span>
                    <h3 class="text-xl font-bold mb-2">Pro</h3>
                    <div class="text-4xl font-extrabold mb-6">$49 <span class="text-sm font-normal text-slate-400">/mo</span></div>
                    <ul class="space-y-4 mb-10 text-slate-600">
                        <li class="flex items-center space-x-2"><span>✅</span> <span>Unlimited Visitors</span></li>
                        <li class="flex items-center space-x-2"><span>✅</span> <span>WhatsApp & Slack Alerts</span></li>
                        <li class="flex items-center space-x-2"><span>✅</span> <span>Custom Branding & Logos</span></li>
                        <li class="flex items-center space-x-2"><span>✅</span> <span>Visitor Analytics</span></li>
                    </ul>
                    <button class="w-full h-12 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">Start Free Trial</button>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-12 border-t border-slate-100 text-center text-slate-400 text-sm">
        &copy; 2026 KiteFlow. Built for the modern office. 🪁
    </footer>
</body>
</html>
