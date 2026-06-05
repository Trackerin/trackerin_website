<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Trackerin - AI-powered Personal Learning Tracker</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Trackerin is a personalized AI-powered learning tracker that automatically generates roadmaps, quizzes, and helps you master any topic.">
    <meta name="keywords" content="learning tracker, study planner, AI roadmap, educational quiz, Trackerin">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white-bg text-dark-text min-h-screen font-sans selection:bg-main-blue selection:text-white-pure antialiased">
    <!-- Premium Header & Navbar (Creative Studio Style) -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-500 bg-transparent border-b border-dark-text/5" id="main-header">
        <div class="max-w-7xl mx-auto px-6 md:px-12 h-24 flex items-center justify-between transition-all duration-500" id="header-inner">
            <!-- Logo -->
            <a href="/" class="flex items-center focus:outline-none hover:opacity-80 transition-opacity duration-300">
                <img src="{{ Vite::asset('resources/images/trackerin_logo.svg') }}" alt="Trackerin Logo" class="h-8 w-auto">
            </a>

            <!-- Desktop Navigation Links (Minimalist Studio style) -->
            <nav class="hidden md:flex items-center space-x-10">
                <a href="#features" class="relative text-sm font-medium tracking-tight text-grey-text hover:text-dark-text transition-colors duration-300 group py-1">
                    <span>Features</span>
                    <span class="absolute bottom-0 left-0 w-full h-[1.5px] bg-dark-text transform scale-x-0 origin-right transition-transform duration-300 ease-out group-hover:scale-x-100 group-hover:origin-left"></span>
                </a>
                <a href="#how-it-works" class="relative text-sm font-medium tracking-tight text-grey-text hover:text-dark-text transition-colors duration-300 group py-1">
                    <span>How it Works</span>
                    <span class="absolute bottom-0 left-0 w-full h-[1.5px] bg-dark-text transform scale-x-0 origin-right transition-transform duration-300 ease-out group-hover:scale-x-100 group-hover:origin-left"></span>
                </a>
                <a href="#contact" class="relative text-sm font-medium tracking-tight text-grey-text hover:text-dark-text transition-colors duration-300 group py-1">
                    <span>Contact Us</span>
                    <span class="absolute bottom-0 left-0 w-full h-[1.5px] bg-dark-text transform scale-x-0 origin-right transition-transform duration-300 ease-out group-hover:scale-x-100 group-hover:origin-left"></span>
                </a>
            </nav>

            <!-- Desktop Action Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="relative inline-flex items-center justify-center px-6 py-2.5 overflow-hidden text-sm font-semibold tracking-tight text-white-pure bg-dark-text rounded-full transition-all duration-300 hover:bg-main-blue hover:scale-[1.02] active:scale-[0.98]">
                            Dashboard
                        </a>
                    @else
                        <!-- Login Button -->
                        <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full text-sm font-semibold tracking-tight text-dark-text border border-dark-text/15 hover:border-dark-text/80 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                            Sign In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="relative inline-flex items-center justify-center px-6 py-2.5 overflow-hidden text-sm font-semibold tracking-tight text-white-pure bg-dark-text rounded-full transition-all duration-300 group hover:bg-main-blue hover:scale-[1.02] active:scale-[0.98]">
                                <span class="relative z-10 flex items-center space-x-1.5">
                                    <span>Get Started</span>
                                    <svg class="w-3.5 h-3.5 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </span>
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <!-- Mobile Menu Toggle Button (Asymmetric Bars) -->
            <button class="md:hidden flex flex-col justify-center items-end w-8 h-8 rounded-full focus:outline-none z-50 group" id="menu-toggle" aria-label="Toggle Menu">
                <span class="w-6 h-[1.5px] bg-dark-text rounded-full transition-all duration-300 mb-1.5" id="burger-top"></span>
                <span class="w-4 h-[1.5px] bg-dark-text rounded-full transition-all duration-300 group-hover:w-6" id="burger-bottom"></span>
            </button>
        </div>

        <!-- Mobile Drawer Menu (Backdrop Blur & Smooth Slide) -->
        <div class="fixed inset-0 bg-white-bg/95 backdrop-blur-xl z-40 transform translate-x-full transition-transform duration-500 ease-in-out md:hidden flex flex-col justify-between p-8 pt-32" id="mobile-menu">
            <nav class="flex flex-col space-y-6">
                <a href="#features" class="mobile-nav-link text-3xl font-bold tracking-tight text-dark-text hover:text-main-blue transition-colors duration-200">Features</a>
                <a href="#how-it-works" class="mobile-nav-link text-3xl font-bold tracking-tight text-dark-text hover:text-main-blue transition-colors duration-200">How it Works</a>
                <a href="#contact" class="mobile-nav-link text-3xl font-bold tracking-tight text-dark-text hover:text-main-blue transition-colors duration-200">Contact Us</a>
            </nav>

            <div class="flex flex-col space-y-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full text-center py-4 rounded-full text-base font-semibold bg-dark-text text-white-pure hover:bg-main-blue transition-all duration-300">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full text-center py-4 rounded-full text-base font-semibold text-dark-text border border-dark-text/25 hover:border-dark-text transition-all duration-200 mb-2">
                            Sign In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full text-center py-4 rounded-full text-base font-semibold bg-dark-text text-white-pure hover:bg-main-blue transition-all duration-300">
                                Get Started
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="pt-24">
        <!-- Hero Section: Left Text, Right Image Layout -->
        <section class="max-w-7xl mx-auto px-6 md:px-12 py-16 md:py-24 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center min-h-[80vh]">
            <!-- Left Side: Typography & Content -->
            <div class="flex flex-col items-start text-left">
                <span class="text-xs font-bold tracking-widest text-main-blue uppercase mb-3">AI Learning Assistant</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-dark-text tracking-tight leading-tight">
                    Rencanakan materi belajar secara cerdas bersama <span class="text-main-blue">Trackerin</span>
                </h1>
                <p class="mt-6 text-base md:text-lg text-grey-text max-w-xl font-medium leading-relaxed">
                    Ubah metrik belajar Anda dari centang progres manual ke sistem penguasaan materi berbasis kuis evaluasi otomatis yang divalidasi oleh kecerdasan buatan.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center space-x-1.5 px-6 py-3 rounded-full text-sm font-semibold tracking-tight text-white-pure bg-dark-text hover:bg-main-blue transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] group">
                            <span>Mulai Belajar Sekarang</span>
                            <svg class="w-4 h-4 transform transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Side: Rounded Mockup Image -->
            <div class="w-full">
                <img src="{{ Vite::asset('resources/images/placeholder_image.jpg') }}" alt="Trackerin Dashboard Illustration" class="w-full h-auto object-cover rounded-3xl shadow-sm border border-dark-text/5">
            </div>
        </section>

        <!-- Features Placeholder Section (Provides scrollable area) -->
        <section class="bg-white-pure py-24 border-t border-dark-text/5 min-h-[60vh]" id="features">
            <div class="max-w-7xl mx-auto px-6 md:px-12">
                <span class="text-xs font-bold tracking-widest text-main-blue uppercase">Fitur Unggulan</span>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-dark-text mt-4">Belajar Lebih Efisien</h2>
                <p class="text-grey-text mt-4 max-w-2xl">
                    Temukan modul belajar terpersonalisasi, asisten pembuat kurikulum berbasis AI, kuis evaluasi otomatis, hingga sistem catatan personal dalam satu tempat.
                </p>
            </div>
        </section>
    </main>

    <!-- Inline Script for Header Blur & Mobile Menu -->
    <script>
        const header = document.getElementById('main-header');
        const headerInner = document.getElementById('header-inner');
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const burgerTop = document.getElementById('burger-top');
        const burgerBottom = document.getElementById('burger-bottom');
        const mobileLinks = document.querySelectorAll('.mobile-nav-link');

        // Scroll listener to toggle glassmorphism and height transition
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('bg-white-bg/90', 'backdrop-blur-lg', 'border-dark-text/10');
                header.classList.remove('border-dark-text/5');
                headerInner.classList.remove('h-24');
                headerInner.classList.add('h-16');
            } else {
                header.classList.remove('bg-white-bg/90', 'backdrop-blur-lg', 'border-dark-text/10');
                header.classList.add('border-dark-text/5');
                headerInner.classList.remove('h-16');
                headerInner.classList.add('h-24');
            }
        });

        // Toggle mobile menu drawer
        let isMenuOpen = false;
        function toggleMenu() {
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                mobileMenu.classList.remove('translate-x-full');
                burgerTop.classList.add('rotate-45', 'translate-y-[4px]');
                burgerBottom.classList.add('-rotate-45', '-translate-y-[4px]', 'w-6');
                document.body.classList.add('overflow-hidden');
            } else {
                mobileMenu.classList.add('translate-x-full');
                burgerTop.classList.remove('rotate-45', 'translate-y-[4px]');
                burgerBottom.classList.remove('-rotate-45', '-translate-y-[4px]', 'w-6');
                document.body.classList.remove('overflow-hidden');
            }
        }

        menuToggle.addEventListener('click', toggleMenu);
        mobileLinks.forEach(link => link.addEventListener('click', toggleMenu));
    </script>
</body>
</html>
