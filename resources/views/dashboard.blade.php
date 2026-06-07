<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Trackerin</title>
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
    <div class="aurora-hero-bg opacity-30"></div>

    <!-- Main Container -->
    <div class="relative z-10 w-full max-w-2xl px-4 py-12 text-center">
        <!-- Premium Card -->
        <div class="gradient-border-card p-6 sm:p-8 md:p-12 shadow-lg bg-white-pure/80 backdrop-blur-xl border border-dark-text/5 flex flex-col items-center">
            
            <!-- Icon -->
            <div class="w-16 h-16 bg-main-blue/10 text-main-blue rounded-full flex items-center justify-center mb-6">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <!-- Header -->
            <h1 class="text-3xl font-bold tracking-tight text-dark-text letter-tracking mb-4">
                Autentikasi <span class="font-serif italic font-normal text-main-blue">Berhasil!</span>
            </h1>
            
            <p class="text-grey-text text-sm font-medium max-w-md mb-8">
                Selamat datang kembali di Trackerin! Autentikasi berhasil dan akunmu sudah siap digunakan. Yuk, langsung masuk ke dashboard dan lanjutkan target belajarmu hari ini!
            </p>

            <!-- User details list -->
            <div class="w-full bg-white-bg/60 border border-dark-text/5 rounded-2xl p-4 sm:p-6 text-left mb-8 space-y-3">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-bold uppercase tracking-wider text-grey-text">Nama Lengkap</span>
                    <span class="font-bold text-dark-text">{{ Auth::user()->name }}</span>
                </div>
                <div class="flex justify-between items-center text-xs border-t border-dark-text/5 pt-3">
                    <span class="font-bold uppercase tracking-wider text-grey-text">Email</span>
                    <span class="font-bold text-dark-text">{{ Auth::user()->email }}</span>
                </div>
                <div class="flex justify-between items-center text-xs border-t border-dark-text/5 pt-3">
                    <span class="font-bold uppercase tracking-wider text-grey-text">Status Akun</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-800">
                        Aktif
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                <a href="/" class="px-8 py-3.5 rounded-full border border-dark-text/10 bg-white-pure hover:bg-white-bg text-dark-text font-bold text-sm transition-all duration-300 active:scale-[0.98]">
                    Kembali ke Dashboard
                </a>
                
                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-dark-text hover:bg-red-600 active:scale-[0.98] transition-all duration-300 text-sm font-bold text-white-pure">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
