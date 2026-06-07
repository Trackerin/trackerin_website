<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Trackerin - AI-powered Personal Learning Tracker</title>

    <!-- SEO Meta Tags -->
    <meta name="description"
        content="Trackerin is a personalized AI-powered learning tracker that automatically generates roadmaps, quizzes, and helps you master any topic.">
    <meta name="keywords" content="learning tracker, study planner, AI roadmap, educational quiz, Trackerin">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-white-pure text-dark-text min-h-screen font-sans selection:bg-main-blue selection:text-white-pure antialiased overflow-x-hidden">
    <!-- Premium Header & Navbar (Creative Studio Style - Floating) -->
    <header
        class="fixed top-4 left-1/2 -translate-x-1/2 w-[92%] max-w-[90rem] z-50 transition-all duration-300 bg-white-pure/75 backdrop-blur-md border border-dark-text/8 rounded-full"
        id="main-header">
        <div class="w-full px-6 md:px-10 h-14 md:h-16 flex items-center justify-between transition-all duration-300"
            id="header-inner">
            <!-- Logo -->
            <div class="flex-1 flex justify-start">
                <a href="/"
                    class="flex items-center focus:outline-none hover:opacity-80 transition-opacity duration-300">
                    <img src="{{ asset('images/trackerin_logo.svg') }}" alt="Trackerin Logo"
                        class="h-8 w-auto">
                </a>
            </div>

            <!-- Desktop Navigation Links (Minimalist Studio style) -->
            <nav class="hidden md:flex items-center space-x-10">
                <a href="#features"
                    class="relative text-sm font-semibold tracking-tight text-grey-text hover:text-dark-text transition-colors duration-300 group py-1">
                    <span>Features</span>
                    <span
                        class="absolute bottom-0 left-0 w-full h-[1.5px] bg-dark-text transform scale-x-0 origin-right transition-transform duration-300 ease-out group-hover:scale-x-100 group-hover:origin-left"></span>
                </a>
                <a href="#why-us"
                    class="relative text-sm font-semibold tracking-tight text-grey-text hover:text-dark-text transition-colors duration-300 group py-1">
                    <span>Why Us</span>
                    <span
                        class="absolute bottom-0 left-0 w-full h-[1.5px] bg-dark-text transform scale-x-0 origin-right transition-transform duration-300 ease-out group-hover:scale-x-100 group-hover:origin-left"></span>
                </a>
                <a href="#testimonials"
                    class="relative text-sm font-semibold tracking-tight text-grey-text hover:text-dark-text transition-colors duration-300 group py-1">
                    <span>Testimonials</span>
                    <span
                        class="absolute bottom-0 left-0 w-full h-[1.5px] bg-dark-text transform scale-x-0 origin-right transition-transform duration-300 ease-out group-hover:scale-x-100 group-hover:origin-left"></span>
                </a>
                <a href="#download"
                    class="relative text-sm font-semibold tracking-tight text-grey-text hover:text-dark-text transition-colors duration-300 group py-1">
                    <span>Get App</span>
                    <span
                        class="absolute bottom-0 left-0 w-full h-[1.5px] bg-dark-text transform scale-x-0 origin-right transition-transform duration-300 ease-out group-hover:scale-x-100 group-hover:origin-left"></span>
                </a>
            </nav>

            <!-- Desktop Action Buttons -->
            <div class="hidden md:flex items-center justify-end space-x-4 flex-1">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="relative inline-flex items-center justify-center px-6 py-2.5 overflow-hidden text-sm font-semibold tracking-tight text-white-pure bg-dark-text rounded-full transition-all duration-300 hover:bg-main-blue hover:scale-[1.02] active:scale-[0.98]">
                            Dashboard
                        </a>
                    @else
                        <!-- Login Button -->
                        <a href="{{ route('login') }}"
                            class="px-5 py-2.5 rounded-full text-sm font-semibold tracking-tight text-dark-text border border-dark-text/15 hover:border-dark-text/80 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                            Sign In
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="relative inline-flex items-center justify-center px-6 py-2.5 overflow-hidden text-sm font-semibold tracking-tight text-white-pure bg-dark-text rounded-full transition-all duration-300 group hover:bg-main-blue hover:scale-[1.02] active:scale-[0.98]">
                                <span class="relative z-10 flex items-center space-x-1.5">
                                    <span>Get Started</span>
                                    <svg class="w-3.5 h-3.5 transform transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                    </svg>
                                </span>
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <!-- Mobile Menu Toggle Button (Asymmetric Bars) -->
            <button
                class="md:hidden flex flex-col justify-center items-end w-8 h-8 rounded-full focus:outline-none z-50 group"
                id="menu-toggle" aria-label="Toggle Menu">
                <span class="w-6 h-[1.5px] bg-dark-text rounded-full transition-all duration-300 mb-1.5"
                    id="burger-top"></span>
                <span class="w-4 h-[1.5px] bg-dark-text rounded-full transition-all duration-300 group-hover:w-6"
                    id="burger-bottom"></span>
            </button>
    </header>

    <!-- Mobile Drawer Menu (Backdrop Blur & Smooth Slide) -->
    <div class="fixed inset-0 bg-white-pure/95 backdrop-blur-xl z-40 transform translate-x-full transition-transform duration-500 ease-in-out md:hidden flex flex-col justify-between p-8 pt-32"
        id="mobile-menu">
        <nav class="flex flex-col space-y-6">
            <a href="#features"
                class="mobile-nav-link text-3xl font-bold tracking-tight text-dark-text hover:text-main-blue transition-colors duration-200">Features</a>
            <a href="#why-us"
                class="mobile-nav-link text-3xl font-bold tracking-tight text-dark-text hover:text-main-blue transition-colors duration-200">Why
                Us</a>
            <a href="#testimonials"
                class="mobile-nav-link text-3xl font-bold tracking-tight text-dark-text hover:text-main-blue transition-colors duration-200">Testimonials</a>
            <a href="#download"
                class="mobile-nav-link text-3xl font-bold tracking-tight text-dark-text hover:text-main-blue transition-colors duration-200">Get
                App</a>
        </nav>

        <div class="flex flex-col space-y-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="w-full text-center py-4 rounded-full text-base font-semibold bg-dark-text text-white-pure hover:bg-main-blue transition-all duration-300">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="w-full text-center py-4 rounded-full text-base font-semibold text-dark-text border border-dark-text/25 hover:border-dark-text transition-all duration-200 mb-2">
                        Sign In
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="w-full text-center py-4 rounded-full text-base font-semibold bg-dark-text text-white-pure hover:bg-main-blue transition-all duration-300">
                            Get Started
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>

    <!-- Main Container -->
    <main class="pt-0">
        <!-- Hero Section Wrapper with Aurora Background -->
        <div class="relative w-full overflow-hidden bg-white-pure">
            <!-- Aurora Premium Glow Background -->
            <div class="aurora-hero-bg"></div>

            <!-- Hero Section: Left Text, Right Mockup Layout -->
            <section
                class="relative z-10 max-w-[98rem] mx-auto px-4 md:px-6 py-20 sm:py-28 md:py-36 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center min-h-screen">
            <!-- Left Side: Custom Font Pairing Content -->
            <div class="flex flex-col items-start text-left">
                <span class="text-xs font-bold tracking-widest text-main-blue uppercase mb-4">AI STUDY COMPANION</span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-dark-text tracking-tight leading-tight reveal">
                    Susun target dan belajar lebih <span
                        class="font-serif italic font-normal text-main-blue">cerdas</span> bersama <span
                        class="font-serif italic font-normal text-main-blue">Trackerin</span>
                </h1>
                <p class="mt-6 text-base md:text-lg text-grey-text max-w-xl font-medium leading-relaxed reveal delay-100">
                    Rencanakan materi, pantau progres, dan uji pemahamanmu di satu tempat! Trackerin hadir untuk memastikan kamu benar-benar paham dan bikin waktu belajarmu jadi lebih efisien.
                </p>
                <div class="mt-8 flex flex-wrap gap-4 reveal delay-200">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center space-x-2 px-6 py-3.5 rounded-full text-sm font-semibold tracking-tight text-white-pure bg-dark-text hover:bg-main-blue transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] group">
                            <span>Mulai Belajar Sekarang</span>
                            <svg class="w-4 h-4 transform transition-transform duration-300 group-hover:translate-x-1"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right Side: Clean Mobile Mockup Image (Enlarged) -->
            <div class="w-full reveal-right delay-200 flex justify-center">
                <img src="{{ asset('images/mockup-mobile.png') }}"
                    alt="Trackerin Mobile Mockup"
                    class="w-full max-w-[380px] md:max-w-[440px] lg:max-w-[480px] h-auto object-contain">
            </div>
        </section>
        </div>

        <!-- Features Bento Grid Section -->
        <section class="bg-white-pure py-24 border-t border-dark-text/5" id="features">
            <div class="max-w-[98rem] mx-auto px-4 md:px-6">
                <div class="text-left mb-16 reveal">
                    <span class="text-xs font-bold tracking-widest text-main-blue uppercase">Fitur Unggulan</span>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-dark-text mt-4">
                        Belajar lebih <span class="font-serif italic font-normal text-main-blue">terarah</span> dengan integrasi
                        <span class="font-serif italic font-normal text-main-blue">AI</span>
                    </h2>
                    <p class="text-grey-text mt-3 text-sm md:text-base font-medium max-w-xl leading-relaxed">
                        Satu aplikasi untuk semua kebutuhan belajarmu. Mulai dari merancang materi belajar, memantau progres, mengetes pemahaman dengan kuis, semuanya bisa dilakukan disini. Trackerin siap memandu proses belajarmu agar lebih efektif!
                    </p>
                </div>

                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Bento Card 1: AI Curriculum (Span 2 Columns on Desktop) -->
                    <div
                        class="gradient-border-card p-6 sm:p-8 lg:col-span-2 flex flex-col justify-between reveal hover-glow-card">
                        <div class="flex flex-col md:flex-row justify-between gap-8">
                            <div class="max-w-md">
                                <div
                                    class="w-12 h-12 bg-white-bg rounded-xl flex items-center justify-center mb-6 border border-dark-text/5 text-main-blue">
                                    <svg class="w-6 h-6" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13.5 6L12.5625 3.9375L10.5 3L12.5625 2.0625L13.5 0L14.4375 2.0625L16.5 3L14.4375 3.9375L13.5 6ZM13.5 16.5L12.5625 14.4375L10.5 13.5L12.5625 12.5625L13.5 10.5L14.4375 12.5625L16.5 13.5L14.4375 14.4375L13.5 16.5ZM6 14.25L4.125 10.125L0 8.25L4.125 6.375L6 2.25L7.875 6.375L12 8.25L7.875 10.125L6 14.25ZM6 10.6125L6.75 9L8.3625 8.25L6.75 7.5L6 5.8875L5.25 7.5L3.6375 8.25L5.25 9L6 10.6125Z" fill="currentColor"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-dark-text mb-3">AI Curriculum Generator</h3>
                                <p class="text-grey-text text-sm font-medium leading-relaxed">
                                    Mulai belajar topik apa saja tanpa pusing menyusun materi. Cukup ketik subjek yang pengen kamu kuasai, dan AI bakal otomatis buat roadmap terstruktur lengkap dengan tahapan belajarnya!
                                </p>
                            </div>

                            <!-- Visual Element: Roadmap Mockup -->
                            <div
                                class="w-full md:w-64 bg-white-bg rounded-2xl p-5 border border-dark-text/5 flex flex-col space-y-3 shrink-0">
                                <span class="text-[10px] font-bold tracking-wider text-main-blue uppercase">Generated
                                    Roadmap</span>
                                <div
                                    class="bg-white-pure p-3 rounded-lg border border-dark-text/5 flex items-center justify-between">
                                    <span class="text-xs font-bold text-dark-text">1. Fundamental PHP</span>
                                    <span
                                        class="w-4 h-4 rounded-full bg-main-blue/20 flex items-center justify-center"><span
                                            class="w-1.5 h-1.5 rounded-full bg-main-blue"></span></span>
                                </div>
                                <div
                                    class="bg-white-pure p-3 rounded-lg border border-dark-text/5 opacity-70 flex items-center justify-between">
                                    <span class="text-xs font-bold text-dark-text">2. Object-Oriented PHP</span>
                                    <span class="w-4 h-4 rounded-full bg-dark-text/10"></span>
                                </div>
                                <div
                                    class="bg-white-pure p-3 rounded-lg border border-dark-text/5 opacity-50 flex items-center justify-between">
                                    <span class="text-xs font-bold text-dark-text">3. MVC Architecture</span>
                                    <span class="w-4 h-4 rounded-full bg-dark-text/10"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bento Card 2: Knowledge Quiz (Span 1 Column) -->
                    <div
                        class="gradient-border-card p-6 sm:p-8 flex flex-col justify-between reveal delay-100 hover-glow-card">
                        <div>
                            <div
                                class="w-12 h-12 bg-white-bg rounded-xl flex items-center justify-center mb-6 border border-dark-text/5 text-main-blue">
                                <svg class="w-6 h-6" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18.3334 13.95V3.89168C18.3334 2.89168 17.5167 2.15001 16.525 2.23335H16.475C14.725 2.38335 12.0667 3.27502 10.5 4.20835L10.4417 4.30001C10.2 4.45001 9.80002 4.45001 9.55835 4.30001L9.35002 4.17502C7.86669 3.25002 5.21669 2.36668 3.46669 2.22501C2.47502 2.14168 1.66669 2.89168 1.66669 3.88335V13.95C1.66669 14.75 2.31669 15.5 3.11669 15.6L3.35835 15.6333C5.16669 15.875 7.95835 16.7917 9.55835 17.6667L9.59169 17.6833C9.81669 17.8083 10.175 17.8083 10.3917 17.6833C11.9917 16.8 14.7917 15.875 16.6084 15.6333L16.8834 15.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 4.57495V17.075" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M6.45831 7.07495H4.58331" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M7.08331 9.57495H4.58331" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-dark-text mb-3">Knowledge Quiz</h3>
                            <p class="text-grey-text text-sm font-medium leading-relaxed">
                                Uji sejauh mana pemahamanmu lewat kuis! Materi akan ditandai selesai kalau kamu berhasil lulus kuis di setiap akhir tahapan belajar.
                            </p>
                        </div>
                        <div
                            class="mt-8 pt-4 border-t border-dark-text/5 flex justify-between items-center text-xs font-bold text-main-blue">
                            <span>Verification-based system</span>
                            <img src="{{ asset('images/arrow_right_icon.svg') }}" alt="Arrow"
                                class="w-4 h-4">
                        </div>
                    </div>

                    <!-- Bento Card 3: Smart Search (Span 1 Column) -->
                    <div
                        class="gradient-border-card p-6 sm:p-8 flex flex-col justify-between reveal delay-200 hover-glow-card">
                        <div>
                            <div
                                class="w-12 h-12 bg-white-bg rounded-xl flex items-center justify-center mb-6 border border-dark-text/5 text-main-blue">
                                <svg class="w-6 h-6" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="8.25012" cy="8.25" r="7.5" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M13.75 13.75L19.0533 19.0533" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-dark-text mb-3">Smart Search</h3>
                            <p class="text-grey-text text-sm font-medium leading-relaxed">
                                Cari apa pun jadi lebih gampang. Ketik topik belajarmu pakai kalimat biasa, dan sistem bakal langsung paham konteks yang kamu maksud!
                            </p>
                        </div>
                        <div
                            class="mt-8 w-full bg-white-bg rounded-xl p-2.5 border border-dark-text/5 flex items-center justify-between">
                            <span class="text-[10px] text-grey-text">"Belajar framework MVC..."</span>
                            <span class="w-6 h-6 bg-main-blue rounded-lg flex items-center justify-center"><img
                                    src="{{ asset('images/search_icon.svg') }}" alt="Search"
                                    class="w-3.5 h-3.5 filter invert"></span>
                        </div>
                    </div>

                    <!-- Bento Card 4: Progress Tracker (Span 2 Columns on Desktop) -->
                    <div
                        class="gradient-border-card p-6 sm:p-8 lg:col-span-2 flex flex-col justify-between reveal delay-300 hover-glow-card">
                        <div class="flex flex-col md:flex-row justify-between gap-8">
                            <div class="max-w-md">
                                <div
                                    class="w-12 h-12 bg-white-bg rounded-xl flex items-center justify-center mb-6 border border-dark-text/5 text-main-blue">
                                    <svg class="w-6 h-6" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 11C0 9.25 0.416667 7.69167 1.25 6.325C2.08333 4.95833 3 3.80833 4 2.875C5 1.94167 5.91667 1.22917 6.75 0.7375C7.58333 0.245833 8 0 8 0V3.3C8 3.91667 8.20833 4.40417 8.625 4.7625C9.04167 5.12083 9.50833 5.3 10.025 5.3C10.3083 5.3 10.5792 5.24167 10.8375 5.125C11.0958 5.00833 11.3333 4.81667 11.55 4.55L12 4C13.2 4.7 14.1667 5.67083 14.9 6.9125C15.6333 8.15417 16 9.51667 16 11C16 12.4667 15.6417 13.8042 14.925 15.0125C14.2083 16.2208 13.2667 17.175 12.1 17.875C12.3833 17.475 12.6042 17.0375 12.7625 16.5625C12.9208 16.0875 13 15.5833 13 15.05C13 14.3833 12.875 13.7542 12.625 13.1625C12.375 12.5708 12.0167 12.0417 11.55 11.575L8 8.1L4.475 11.575C3.99167 12.0583 3.625 12.5917 3.375 13.175C3.125 13.7583 3 14.3833 3 15.05C3 15.5833 3.07917 16.0875 3.2375 16.5625C3.39583 17.0375 3.61667 17.475 3.9 17.875C2.73333 17.175 1.79167 16.2208 1.075 15.0125C0.358333 13.8042 0 12.4667 0 11ZM8 10.9L10.125 12.975C10.4083 13.2583 10.625 13.575 10.775 13.925C10.925 14.275 11 14.65 11 15.05C11 15.8667 10.7083 16.5625 10.125 17.1375C9.54167 17.7125 8.83333 18 8 18C7.16667 18 6.45833 17.7125 5.875 17.1375C5.29167 16.5625 5 15.8667 5 15.05C5 14.6667 5.075 14.2958 5.225 13.9375C5.375 13.5792 5.59167 13.2583 5.875 12.975L8 10.9Z" fill="currentColor"/>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-dark-text mb-3">Progress Tracker</h3>
                                <p class="text-grey-text text-sm font-medium leading-relaxed">
                                    Pantau semua perkembangan belajarmu lewat grafik! Cek persentase progresmu tiap minggu biar kamu tahu sejauh mana target belajarmu tercapai!
                                </p>
                            </div>

                            <!-- Visual Element: Stats Mockup -->
                            <div
                                class="w-full md:w-64 bg-white-bg rounded-2xl p-5 border border-dark-text/5 flex flex-col justify-between shrink-0">
                                <div class="flex justify-between items-center mb-3">
                                    <span class="text-[10px] font-bold tracking-wider text-main-blue uppercase">Progres
                                        Mingguan</span>
                                    <span class="text-xs font-bold text-dark-text">78%</span>
                                </div>
                                <div class="w-full bg-white-pure rounded-full h-2.5 mb-4 border border-dark-text/5">
                                    <div class="bg-main-blue h-2 rounded-full" style="width: 78%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-bold text-grey-text">
                                    <span>Sen</span>
                                    <span>Sel</span>
                                    <span>Rab</span>
                                    <span>Kam</span>
                                    <span class="text-main-blue">Jum</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Us Section -->
        <section class="py-24 border-t border-dark-text/5" id="why-us">
            <div class="max-w-[98rem] mx-auto px-4 md:px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left: Big Typography Info -->
                <div class="lg:col-span-5 flex flex-col items-start reveal">
                    <span class="text-xs font-bold tracking-widest text-main-blue uppercase mb-3">
                        Mengapa Trackerin
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-dark-text leading-tight">
                        Belajar mandiri <span class="font-serif italic font-normal text-main-blue">tanpa</span>
                        ribet susun <span class="font-serif italic font-normal text-main-blue">materi</span>
                    </h2>
                    <p class="text-grey-text mt-6 font-medium leading-relaxed">
                        Kami percaya belajar hal baru itu gak harus bikin pusing. Trackerin bakal siapin target dan susunan materi biar kamu langsung fokus belajar!
                    </p>
                </div>

                <!-- Right: Comparison list -->
                <div class="lg:col-span-7 flex flex-col space-y-6">
                    <div class="gradient-border-card p-5 sm:p-6 flex space-x-4 sm:space-x-6 items-start reveal delay-100 hover-lift">
                        <div
                            class="w-10 h-10 bg-[#ECF0F1] text-main-blue rounded-full flex items-center justify-center font-bold shrink-0">
                            1</div>
                        <div>
                            <h3 class="text-lg font-bold text-dark-text">Susun Materi Otomatis</h3>
                            <p class="text-grey-text text-sm mt-2 font-medium leading-relaxed">
                                Tidak perlu buang waktu berjam-jam cari bahan belajar di internet. Cukup ketik topiknya dan Trackerin bakal langsung buatin susunan materi buat kamu!
                            </p>
                        </div>
                    </div>

                    <div class="gradient-border-card p-5 sm:p-6 flex space-x-4 sm:space-x-6 items-start reveal delay-200 hover-lift">
                        <div
                            class="w-10 h-10 bg-[#ECF0F1] text-main-blue rounded-full flex items-center justify-center font-bold shrink-0">
                            2</div>
                        <div>
                            <h3 class="text-lg font-bold text-dark-text">Progres yang Valid</h3>
                            <p class="text-grey-text text-sm mt-2 font-medium leading-relaxed">
                                Say goodbye sama asal centang selesai padahal belum paham! Di Trackerin, materi akan ditandai selesai setelah kamu berhasil mengerjakan kuis evaluasi!
                            </p>
                        </div>
                    </div>

                    <div class="gradient-border-card p-5 sm:p-6 flex space-x-4 sm:space-x-6 items-start reveal delay-300 hover-lift">
                        <div
                            class="w-10 h-10 bg-[#ECF0F1] text-main-blue rounded-full flex items-center justify-center font-bold shrink-0">
                            3</div>
                        <div>
                            <h3 class="text-lg font-bold text-dark-text">Paham Kebutuhanmu</h3>
                            <p class="text-grey-text text-sm mt-2 font-medium leading-relaxed">
                                Trackerin bakal kasih rekomendasi topik belajar lanjutan berdasarkan hasil evaluasimu, biar kamu tahu bagian mana yang perlu ditingkatkan!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="bg-white-pure py-24 border-t border-dark-text/5" id="testimonials">
            <div class="max-w-[98rem] mx-auto px-4 md:px-6">
                <div class="text-center max-w-3xl mx-auto mb-16 reveal">
                    <span class="text-xs font-bold tracking-widest text-main-blue uppercase">Testimoni</span>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-dark-text mt-4">
                        Apa kata <span class="font-serif italic font-normal text-main-blue">mereka</span> tentang <span
                            class="font-serif italic font-normal text-main-blue">kami</span>
                    </h2>
                    <p class="text-grey-text mt-4 font-medium leading-relaxed">
                        Simak bagaimana Trackerin membantu ribuan pembelajar dan intip pengalaman seru mereka yang sudah menggunakan platform ini untuk mengatur waktu serta materi belajarnya!"
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Testimonial 1 -->
                    <div class="gradient-border-card p-6 sm:p-8 flex flex-col justify-between reveal hover-lift">
                        <p class="text-dark-text text-sm font-medium italic leading-relaxed">
                            Dulu pas awal mau belajar coding bingung banget mau mulai dari mana karena materinya banyak dan berantakan di internet. Pas nyobain Trackerin, tinggal ketik satu topik langsung dibuatin roadmap belajar yang terstruktur banget!"
                        </p>
                        <div class="mt-6 flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-full bg-main-blue flex items-center justify-center text-white-pure font-bold">
                                R</div>
                            <div>
                                <h4 class="text-sm font-bold text-dark-text">Ned Leeds</h4>
                                <span class="text-xs text-grey-text">Computer Science Student</span>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="gradient-border-card p-6 sm:p-8 flex flex-col justify-between reveal delay-100 hover-lift">
                        <p class="text-dark-text text-sm font-medium italic leading-relaxed">
                            Jujurrr suka bgt sm sistem kuisnya trackerin!! bener2 gabisa curang asal centang kl blm paham materinya wkwk. jd berasa tertantang tp seru, apalagi ui/ux websitenya clean parah bikin betah scroll n belajar lama2 disitu huaa 😭👍
                        </p>
                        <div class="mt-6 flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-full bg-main-blue flex items-center justify-center text-white-pure font-bold">
                                A</div>
                            <div>
                                <h4 class="text-sm font-bold text-dark-text">Cher Horowitz</h4>
                                <span class="text-xs text-grey-text">Self-Taught Designer</span>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="gradient-border-card p-6 sm:p-8 flex flex-col justify-between reveal delay-200 hover-lift">
                        <p class="text-dark-text text-sm font-medium italic leading-relaxed">
                            Nyari materi dan konsisten belajar mandiri itu gak mudah, tapi fitur notes sama to-dos Trackerin ngebantu banget buat ngejaga itu. Materi ujian yang banyak jadi rapi dan mudah dipahami. 
                            Gua bisa nge-track progres harian sampai akhirnya lolos di ujian scholarship kemarin. Big appreciation buat tim Trackerin.
                        </p>
                        <div class="mt-6 flex items-center space-x-3">
                            <div
                                class="w-10 h-10 rounded-full bg-main-blue flex items-center justify-center text-white-pure font-bold">
                                D</div>
                            <div>
                                <h4 class="text-sm font-bold text-dark-text">Bruce Wayne</h4>
                                <span class="text-xs text-grey-text">Independent Learner</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Download & Continue Section -->
        <section class="py-24 border-t border-dark-text/5" id="download">
            <div class="max-w-[98rem] mx-auto px-4 md:px-6">
                <div
                    class="bg-dark-text text-white-pure rounded-3xl p-6 sm:p-8 md:p-12 lg:p-16 flex flex-col lg:flex-row items-center justify-between gap-12 reveal-scale">
                    <div class="max-w-xl text-left">
                        <span class="text-xs font-bold tracking-widest text-main-blue uppercase">MULTI PLATFORM</span>
                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight mt-4">Belajar jadi lebih fleksibel lewat smartphone atau laptop</h2>
                        <p class="text-gray-400 mt-6 text-sm md:text-base leading-relaxed">
                            Data dan progres belajarmu bakal otomatis tersinkronisasi antara browser laptop dan aplikasi android di smartphone kamu. Kamu bisa lanjut belajar kapan pun dan di mana pun!
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 shrink-0 w-full lg:w-auto">
                        <!-- Continue on Web button -->
                        <a href="{{ url('/dashboard') }}"
                            class="w-full sm:w-auto text-center px-8 py-4 bg-main-blue hover:bg-hover-blue text-white-pure font-semibold rounded-full transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]">
                            Continue on Web
                        </a>
                        <!-- Download Android Button -->
                        <a href="#"
                            class="w-full sm:w-auto text-center px-8 py-4 bg-white-pure text-dark-text font-semibold rounded-full border border-white-pure transition-all duration-300 hover:bg-[#ECF0F1] hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center space-x-2">
                            <span>Get Mobile App</span>
                            <img src="{{ asset('images/arrow_right_icon.svg') }}" alt="Arrow"
                                class="w-4 h-4">
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Us Section -->
        <section class="bg-white-pure py-24 border-t border-dark-text/5" id="contact">
            <div class="max-w-[98rem] mx-auto px-4 md:px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-start">
                <!-- Left Column: Details -->
                <div class="lg:col-span-5 flex flex-col items-start text-left reveal">
                    <span class="text-xs font-bold tracking-widest text-main-blue uppercase mb-3 inline-block">Hubungi Kami</span>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-dark-text leading-tight">
                        Ada pertanyaan? <br>Yuk, <span class="font-serif italic font-normal text-main-blue"> diskusi</span> bersama kami!
                    </h2>
                    <p class="text-grey-text mt-6 text-sm md:text-base font-medium leading-relaxed max-w-md">
                        Punya kendala teknis, ingin bertanya, beri saran atau tertarik untuk kolaborasi? Tulis pesanmu melalui form di samping, kami akan balas secepatnya!
                    </p>
                    
                    <!-- Contact details -->
                    <div class="mt-10 space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full bg-white-bg flex items-center justify-center text-main-blue border border-dark-text/5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-grey-text">Email Resmi</h4>
                                <a href="mailto:trackerinofficial@gmail.com" class="text-sm font-semibold text-dark-text hover:text-main-blue transition-colors duration-200">trackerinofficial@gmail.com</a>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-full bg-white-bg flex items-center justify-center text-main-blue border border-dark-text/5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-grey-text">Media Sosial</h4>
                                <a href="#" class="text-sm font-semibold text-dark-text hover:text-main-blue transition-colors duration-200">Instagram @trackerin</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Premium Form Card -->
                <div class="lg:col-span-7 w-full">
                    <div class="gradient-border-card p-6 sm:p-8 md:p-10 shadow-sm border border-dark-text/5 reveal delay-100">
                        <form id="contact-form" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="flex flex-col space-y-1.5">
                                    <label for="contact-name" class="text-xs font-bold uppercase tracking-wider text-grey-text">Nama Lengkap</label>
                                    <input type="text" id="contact-name" name="name" required placeholder="Masukkan nama Anda"
                                        class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <label for="contact-email" class="text-xs font-bold uppercase tracking-wider text-grey-text">Email</label>
                                    <input type="email" id="contact-email" name="email" required placeholder="name@example.com"
                                        class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                                </div>
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <label for="contact-subject" class="text-xs font-bold uppercase tracking-wider text-grey-text">Subjek</label>
                                <input type="text" id="contact-subject" name="subject" required placeholder="Topik pesan Anda"
                                        class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium">
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <label for="contact-message" class="text-xs font-bold uppercase tracking-wider text-grey-text">Pesan</label>
                                <textarea id="contact-message" name="message" rows="5" required placeholder="Tulis pesan Anda di sini..."
                                    class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue/80 focus:ring-1 focus:ring-main-blue/80 rounded-xl px-4 py-3 placeholder:text-grey-text transition-all duration-300 outline-none text-sm font-medium resize-none"></textarea>
                            </div>

                            <!-- Alert Box -->
                            <div id="contact-alert" class="hidden rounded-xl p-4 text-sm font-semibold transition-all duration-300"></div>

                            <button type="submit" id="contact-submit-btn"
                                class="w-full py-4 px-6 rounded-full bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-sm font-bold text-white-pure flex items-center justify-center space-x-2">
                                <span id="contact-btn-text">Kirim Pesan</span>
                                <svg id="contact-btn-spinner" class="hidden animate-spin h-4 w-4 text-white-pure" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer (Creative Studio Style) -->
    <footer class="bg-dark-text pt-28 pb-12 overflow-hidden border-t border-white-pure/5 text-white-pure">
        <div class="max-w-[98rem] mx-auto px-4 md:px-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-8 pb-20">
            <!-- Column 1: Brand details -->
            <div class="flex flex-col space-y-6">
                <a href="/" class="flex items-center focus:outline-none hover:opacity-80 transition-opacity duration-300">
                    <img src="{{ asset('images/trackerin_logo.svg') }}" alt="Trackerin Logo" class="h-8 w-auto">
                </a>
                <p class="text-sm text-gray-400 max-w-xs leading-relaxed font-medium">
                    Platform learning tracker berbasis AI yang membantu menyusun rencana belajar dan menguasai materi secara lebih terstruktur
                </p>
            </div>

            <!-- Column 2: Trackerin Official -->
            <div class="flex flex-col space-y-4">
                <h4 class="text-sm font-bold tracking-wider text-white-pure uppercase">Trackerin Official</h4>
                <div class="flex flex-col space-y-2 text-sm text-gray-400 font-medium">
                    <a href="#" class="hover:text-white-pure transition-colors duration-200">trackerin.app</a>
                    <a href="#" class="hover:text-white-pure transition-colors duration-200">Our Developers</a>
                </div>
            </div>

            <!-- Column 3: Navigation Quick Links -->
            <div class="flex flex-col space-y-4">
                <h4 class="text-sm font-bold tracking-wider text-white-pure uppercase">Navigation</h4>
                <div class="flex flex-col space-y-2 text-sm text-gray-400 font-medium">
                    <a href="#features" class="hover:text-white-pure transition-colors duration-200">Home</a>
                    <a href="#features" class="hover:text-white-pure transition-colors duration-200">Features</a>
                    <a href="#why-us" class="hover:text-white-pure transition-colors duration-200">Why Us</a>
                    <a href="#testimonials" class="hover:text-white-pure transition-colors duration-200">Testimonials</a>
                </div>
            </div>

            <!-- Column 4: Contact Detail -->
            <div class="flex flex-col space-y-4">
                <h4 class="text-sm font-bold tracking-wider text-white-pure uppercase">Contact Detail</h4>
                <div class="flex flex-col space-y-2.5 text-sm text-gray-400 font-medium">
                    <a href="mailto:trackerinofficial@gmail.com" class="hover:text-white-pure transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-main-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>trackerinofficial@gmail.com</span>
                    </a>
                    <a href="#" class="hover:text-white-pure transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-4 h-4 text-main-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <span>Instagram @trackerin</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Giant Text Flush to screen bottom (Reference style - absolute clipping prevents scroll height overflow) -->
        <div class="w-full select-none pointer-events-none mt-12 overflow-hidden h-[9vw] min-h-[60px] relative">
            <h2
                class="text-[14vw] font-black lowercase text-white-pure/[0.03] tracking-tighter leading-none text-center absolute left-0 right-0 -bottom-[1vw]">
                trackerin
            </h2>
        </div>
    </footer>

    <!-- Inline Script for Header Blur & Mobile Menu -->
    <script>
        const header = document.getElementById('main-header');
        const headerInner = document.getElementById('header-inner');
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const burgerTop = document.getElementById('burger-top');
        const burgerBottom = document.getElementById('burger-bottom');
        const mobileLinks = document.querySelectorAll('.mobile-nav-link');

        // Scroll listener to toggle floating stroke outline contrast
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('bg-white-pure/90', 'border-dark-text/15');
                header.classList.remove('bg-white-pure/75', 'border-dark-text/8');
            } else {
                header.classList.add('bg-white-pure/75', 'border-dark-text/8');
                header.classList.remove('bg-white-pure/90', 'border-dark-text/15');
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

        // Contact Form Submission handling
        const contactForm = document.getElementById('contact-form');
        const contactAlert = document.getElementById('contact-alert');
        const contactSubmitBtn = document.getElementById('contact-submit-btn');
        const contactBtnText = document.getElementById('contact-btn-text');
        const contactBtnSpinner = document.getElementById('contact-btn-spinner');

        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Set loading state
            contactSubmitBtn.disabled = true;
            contactBtnSpinner.classList.remove('hidden');
            contactBtnText.textContent = 'Mengirim...';
            contactAlert.classList.add('hidden');
            contactAlert.className = 'hidden rounded-xl p-4 text-sm font-semibold transition-all duration-300';

            const formData = new FormData(contactForm);
            const data = {
                name: formData.get('name'),
                email: formData.get('email'),
                subject: formData.get('subject'),
                message: formData.get('message'),
            };

            try {
                const response = await fetch('/api/v1/contact', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': formData.get('_token')
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    contactAlert.textContent = result.message;
                    contactAlert.classList.remove('hidden');
                    contactAlert.classList.add('bg-emerald-500/10', 'text-emerald-400', 'border', 'border-emerald-500/20');
                    contactForm.reset();
                } else {
                    contactAlert.textContent = result.message || 'Gagal mengirim pesan. Silakan coba lagi.';
                    contactAlert.classList.remove('hidden');
                    contactAlert.classList.add('bg-rose-500/10', 'text-rose-400', 'border', 'border-rose-500/20');
                }
            } catch (error) {
                console.error('Submission error:', error);
                contactAlert.textContent = 'Terjadi kesalahan koneksi. Silakan periksa jaringan Anda.';
                contactAlert.classList.remove('hidden');
                contactAlert.classList.add('bg-rose-500/10', 'text-rose-400', 'border', 'border-rose-500/20');
            } finally {
                // Reset button state
                contactSubmitBtn.disabled = false;
                contactBtnSpinner.classList.add('hidden');
                contactBtnText.textContent = 'Kirim Pesan';
            }
        });
    </script>
</body>

</html>
