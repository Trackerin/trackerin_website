<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Trackerin</title>
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .letter-tracking {
            letter-spacing: -0.06em !important;
        }
    </style>
</head>

<body class="bg-white-bg text-dark-text min-h-screen font-sans selection:bg-main-blue selection:text-white-pure antialiased overflow-x-hidden flex items-center justify-center relative">
    
    <!-- Aurora Background Glow -->
    <div class="aurora-hero-bg opacity-40"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-md px-4 py-12">
        <!-- Back to Home -->
        <a href="/" class="inline-flex items-center space-x-2 text-sm font-semibold text-grey-text hover:text-dark-text transition-colors duration-300 mb-8 group">
            <svg class="w-4 h-4 transform transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>

        <!-- Premium Auth Card -->
        <div class="gradient-border-card p-6 sm:p-8 md:p-10 shadow-lg bg-white-pure/80 backdrop-blur-xl border border-dark-text/5">
            <!-- Brand Logo & Header -->
            <div class="text-left mb-8">
                <img src="{{ asset('images/trackerin_logo.svg') }}" alt="Trackerin Logo" class="h-7 w-auto mb-6">
                <h1 class="text-2xl font-bold tracking-tight text-dark-text letter-tracking">
                    Selamat datang <span class="font-serif italic font-normal text-main-blue">kembali</span>
                </h1>
                <p class="text-grey-text text-sm font-medium mt-2">
                    Yuk, masuk ke akunmu untuk melanjutkan materi dan pantau progres belajarmu hari ini!
                </p>
            </div>

            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm font-semibold">
                    <ul class="list-disc pl-5 space-y-1 text-left">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div class="flex flex-col space-y-1.5">
                    <label for="email" class="text-xs font-bold uppercase tracking-wider text-grey-text">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com"
                        class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                    @error('email')
                        <span class="text-xs font-semibold text-red-500 mt-1 text-left">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="flex flex-col space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-xs font-bold uppercase tracking-wider text-grey-text">Kata Sandi</label>
                    </div>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                        class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                    @error('password')
                        <span class="text-xs font-semibold text-red-500 mt-1 text-left">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-xs font-semibold">
                    <label class="flex items-center space-x-2 text-grey-text cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-dark-text/10 text-main-blue focus:ring-main-blue/50 bg-white-bg cursor-pointer">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 px-6 rounded-full bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-sm font-bold text-white-pure">
                    Masuk
                </button>

                <!-- Separator -->
                <div class="relative flex items-center justify-center my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-dark-text/5"></div>
                    </div>
                    <span class="relative z-10 px-4 bg-white-pure/80 text-[10px] font-bold uppercase tracking-widest text-grey-text">atau</span>
                </div>

                <!-- Google OAuth Login -->
                <a href="{{ route('google.login') }}" class="w-full py-3.5 px-6 rounded-full border border-dark-text/10 hover:border-dark-text/30 bg-white-pure hover:bg-white-bg text-dark-text font-bold text-sm transition-all duration-300 flex items-center justify-center space-x-3 active:scale-[0.98]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" fill="#EA4335"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                </a>
            </form>

            <!-- Sign Up Link -->
            <div class="mt-8 pt-6 border-t border-dark-text/5 text-center text-xs font-medium text-grey-text">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-main-blue font-bold hover:underline transition-all">Daftar sekarang</a>
            </div>
        </div>
    </div>
</body>

</html>
