<!-- projects/visiflow/resources/views/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KiteFlow Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 antialiased min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full px-6">
        <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-2xl shadow-slate-200">
            <header class="mb-8 text-center">
                <span class="text-3xl mb-4 block">🪁</span>
                <h2 class="text-2xl font-bold">Welcome back</h2>
                <p class="text-slate-500 text-sm">Sign in to your KiteFlow account.</p>
            </header>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase text-slate-400">Email Address</label>
                    <input name="email" type="email" class="w-full h-12 px-4 rounded-xl bg-slate-50 border border-transparent transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-sm font-medium" placeholder="john@example.com" required>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase text-slate-400">Password</label>
                    <input name="password" type="password" class="w-full h-12 px-4 rounded-xl bg-slate-50 border border-transparent transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 text-sm font-medium" required>
                </div>

                @if($errors->any())
                    <p class="text-rose-500 text-xs font-bold">{{ $errors->first() }}</p>
                @endif

                <button type="submit" class="w-full h-14 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 hover:shadow-indigo-200 transition-all active:scale-95">
                    Sign In 🚀
                </button>
            </form>
            
            <p class="mt-8 text-center text-sm text-slate-400">
                Don't have an account? <a href="{{ route('register') }}" class="text-indigo-600 font-bold">Register</a>
            </p>
        </div>
    </div>
</body>
</html>
