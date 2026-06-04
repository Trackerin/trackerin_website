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
    <!-- Premium Header & Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 border-b border-transparent" id="main-header">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <!-- Typographic Logo -->
            <a href="/" class="flex items-center space-x-1.5 focus:outline-none">
                <span class="text-2xl font-black tracking-tight text-dark-text uppercase flex items-center">
                    Trackerin
                    <span class="w-2 h-2 rounded-full bg-main-blue ml-1 animate-pulse"></span>
                </span>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-sm font-semibold text-grey-text hover:text-dark-text transition-colors duration-200">Features</a>
                <a href="#how-it-works" class="text-sm font-semibold text-grey-text hover:text-dark-text transition-colors duration-200">How it Works</a>
                <a href="#contact" class="text-sm font-semibold text-grey-text hover:text-dark-text transition-colors duration-200">Contact Us</a>
            </nav>

            <!-- Desktop Action Buttons -->
            <div class="hidden md:flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-full text-sm font-bold bg-main-blue hover:bg-hover-blue text-white-pure transition-all duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-main-blue focus:ring-offset-2">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-dark-text hover:text-main-blue transition-colors duration-200">
                            Sign In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full text-sm font-bold bg-main-blue hover:bg-hover-blue text-white-pure transition-all duration-300 shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-main-blue focus:ring-offset-2">
                                Get Started
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <!-- Mobile Menu Toggle Button -->
            <button class="md:hidden flex flex-col justify-center items-center w-8 h-8 rounded-full focus:outline-none z-50" id="menu-toggle" aria-label="Toggle Menu">
                <span class="w-6 h-0.5 bg-dark-text rounded-full transition-all duration-300 origin-center mb-1.5" id="burger-top"></span>
                <span class="w-6 h-0.5 bg-dark-text rounded-full transition-all duration-300 origin-center" id="burger-bottom"></span>
            </button>
        </div>

        <!-- Mobile Drawer Menu -->
        <div class="fixed inset-0 bg-white-pure z-40 transform translate-x-full transition-transform duration-500 ease-in-out md:hidden flex flex-col justify-between p-8 pt-28" id="mobile-menu">
            <nav class="flex flex-col space-y-6">
                <a href="#features" class="mobile-nav-link text-2xl font-bold text-dark-text hover:text-main-blue transition-colors duration-200">Features</a>
                <a href="#how-it-works" class="mobile-nav-link text-2xl font-bold text-dark-text hover:text-main-blue transition-colors duration-200">How it Works</a>
                <a href="#contact" class="mobile-nav-link text-2xl font-bold text-dark-text hover:text-main-blue transition-colors duration-200">Contact Us</a>
            </nav>

            <div class="flex flex-col space-y-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="w-full text-center py-3.5 rounded-full text-base font-bold bg-main-blue hover:bg-hover-blue text-white-pure transition-all duration-300">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full text-center py-3.5 rounded-full text-base font-bold text-dark-text border border-dark-text hover:bg-white-bg transition-all duration-200">
                            Sign In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="w-full text-center py-3.5 rounded-full text-base font-bold bg-main-blue hover:bg-hover-blue text-white-pure transition-all duration-300">
                                Get Started
                            </a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="pt-20">
        <!-- Hero Section Placeholder for visual context and scrolling -->
        <section class="min-h-[140vh] flex flex-col items-center justify-center px-6 text-center max-w-5xl mx-auto">
            <h1 class="text-6xl md:text-8xl font-black text-dark-text uppercase leading-none tracking-tighter">
                Learn.<br>
                Track.<br>
                <span class="text-main-blue">Master.</span>
            </h1>
            <p class="mt-8 text-lg md:text-xl text-grey-text max-w-2xl font-medium">
                Sistem belajar terpersonalisasi dengan teknologi AI. Beralih dari centang progres manual ke penguasaan materi berbasis validasi kuis otomatis.
            </p>
        </section>
    </main>

    <!-- Inline Script for Header Blur & Mobile Menu -->
    <script>
        const header = document.getElementById('main-header');
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const burgerTop = document.getElementById('burger-top');
        const burgerBottom = document.getElementById('burger-bottom');
        const mobileLinks = document.querySelectorAll('.mobile-nav-link');

        // Scroll listener to toggle glassmorphism styling
        window.addEventListener('scroll', () => {
            if (window.scrollY > 20) {
                header.classList.add('bg-white-bg/85', 'backdrop-blur-md', 'border-dark-text/5');
            } else {
                header.classList.remove('bg-white-bg/85', 'backdrop-blur-md', 'border-dark-text/5');
            }
        });

        // Toggle mobile menu drawer
        let isMenuOpen = false;
        function toggleMenu() {
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                mobileMenu.classList.remove('translate-x-full');
                burgerTop.classList.add('rotate-45', 'translate-y-[3px]');
                burgerBottom.classList.add('-rotate-45', '-translate-y-[3px]');
                document.body.classList.add('overflow-hidden');
            } else {
                mobileMenu.classList.add('translate-x-full');
                burgerTop.classList.remove('rotate-45', 'translate-y-[3px]');
                burgerBottom.classList.remove('-rotate-45', '-translate-y-[3px]');
                document.body.classList.remove('overflow-hidden');
            }
        }

        menuToggle.addEventListener('click', toggleMenu);
        mobileLinks.forEach(link => link.addEventListener('click', toggleMenu));
    </script>
</body>
</html>
