<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Kata Sandi - Trackerin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/trackerin_logo.svg') }}">
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
        <!-- Back to Dashboard -->
        <a href="/dashboard" class="inline-flex items-center space-x-2 text-sm font-semibold text-grey-text hover:text-dark-text transition-colors duration-300 mb-8 group">
            <svg class="w-4 h-4 transform transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Dashboard</span>
        </a>

        <!-- Premium Auth Card -->
        <div class="gradient-border-card p-6 sm:p-8 md:p-10 shadow-lg bg-white-pure/80 backdrop-blur-xl border border-dark-text/5">
            <!-- Brand Logo & Header -->
            <!-- Brand Logo & Header -->
            <div class="text-left mb-8">
                <img src="{{ asset('images/trackerin_logo.svg') }}" alt="Trackerin Logo" class="h-7 w-auto mb-6">
                <h1 class="text-2xl font-bold tracking-tight text-dark-text letter-tracking">
                    @if(is_null(Auth::user()->password))
                        Atur <span class="font-serif italic font-normal text-main-blue">Kata Sandi</span>
                    @else
                        Ubah <span class="font-serif italic font-normal text-main-blue">Kata Sandi</span>
                    @endif
                </h1>
                <p class="text-grey-text text-sm font-medium mt-2">
                    @if(is_null(Auth::user()->password))
                        Akun Anda terhubung melalui Google. Silakan atur kata sandi baru untuk mengaktifkan login manual di masa mendatang.
                    @else
                        Untuk keamanan akun Anda, silakan masukkan kata sandi saat ini dan tentukan kata sandi baru.
                    @endif
                </p>
            </div>

            <!-- Success Alert -->
            @if(session('status'))
                <div class="mb-5 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-500 text-sm font-semibold">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Error Alerts -->
            @if(session('error'))
                <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 text-sm font-semibold space-y-1">
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <form action="{{ route('change-password') }}" method="POST" class="space-y-5">
                @csrf

                @if(!is_null(Auth::user()->password))
                <!-- Current Password -->
                <div class="flex flex-col space-y-1.5">
                    <label for="current_password" class="text-xs font-bold uppercase tracking-wider text-grey-text">Kata Sandi Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required placeholder="••••••••"
                        class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                </div>
                @endif

                <!-- New Password -->
                <div class="flex flex-col space-y-1.5">
                    <label for="password" class="text-xs font-bold uppercase tracking-wider text-grey-text">Kata Sandi Baru</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••"
                        class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                </div>

                <!-- Confirm New Password -->
                <div class="flex flex-col space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold uppercase tracking-wider text-grey-text">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••"
                        class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 px-6 rounded-full bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-sm font-bold text-white-pure">
                    @if(is_null(Auth::user()->password))
                        Atur Kata Sandi
                    @else
                        Simpan Kata Sandi
                    @endif
                </button>
            </form>
        </div>
    </div>
</body>

</html>
