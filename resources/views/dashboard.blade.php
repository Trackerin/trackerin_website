<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Trackerin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/trackerin_logo.svg') }}">
    
    <!-- CSRF Token for Secure AJAX AJAX API calls -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ Auth::id() }}">

    <!-- Styles & Scripts compiled by Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/dashboard.js'])
    
    <style>
        .letter-tracking {
            letter-spacing: -0.05em !important;
        }
        
        .nav-link {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .nav-link:hover:not(.active-nav-link) {
            background-color: rgba(47, 108, 249, 0.05) !important;
            transform: translateX(4px);
        }
        
        .active-nav-link {
            background: linear-gradient(135deg, #2F6CF9 0%, #1A4FB8 100%) !important;
            color: #FFFFFF !important;
            border-left: none !important;
            font-weight: 800 !important;
            box-shadow: 0 8px 20px -6px rgba(47, 108, 249, 0.35);
            transform: translateY(-1px);
        }
        
        .active-nav-link svg {
            stroke: #FFFFFF !important;
        }
        
        @media (max-width: 768px) {
            .nav-link:hover:not(.active-nav-link) {
                background-color: transparent !important;
                transform: none;
            }
            .active-nav-link {
                background: transparent !important;
                color: #2F6CF9 !important;
                border-left: none !important;
                border-bottom: 3.5px solid #2F6CF9 !important;
                box-shadow: none !important;
                border-radius: 0px !important;
                transform: none !important;
            }
            .active-nav-link svg {
                stroke: #2F6CF9 !important;
            }
        }

        /* Chart tooltip styling */
        .chart-bar-container:hover .chart-tooltip {
            opacity: 1 !important;
            transform: translateY(-4px) !important;
        }
    </style>
</head>

<body class="bg-white-bg text-dark-text min-h-screen font-sans antialiased overflow-x-hidden relative flex flex-col md:flex-row">
    
    <!-- Aurora Background Glow -->
    <div class="aurora-hero-bg opacity-20 fixed inset-0 pointer-events-none"></div>

    <!-- Global Alert / Toast Notification -->
    <div id="dashboard-global-alert" class="fixed top-6 right-6 z-[100] px-6 py-3.5 rounded-full text-xs font-bold text-white-pure shadow-lg transition-all duration-300 transform scale-0 pointer-events-none">
        Notification message
    </div>

    <!-- ==========================================
         SIDEBAR NAVIGATION (Desktop Only)
         ========================================== -->
    <aside class="hidden md:flex flex-col justify-between w-64 h-screen sticky top-0 bg-white-pure/80 backdrop-blur-xl border-r border-dark-text/5 p-6 z-30 shrink-0">
        <div class="space-y-8">
            <!-- Brand Logo -->
            <a href="/" class="block focus:outline-none hover:opacity-85 transition-opacity duration-300">
                <img src="{{ asset('images/trackerin_logo.svg') }}" alt="Trackerin Logo" class="h-7 w-auto">
            </a>

            <!-- Quick Profile Overview -->
            <div class="flex items-center space-x-3.5 p-3 rounded-2xl bg-white-bg/40 border border-dark-text/5">
                <div class="w-10 h-10 rounded-full overflow-hidden border border-dark-text/5 shrink-0 flex items-center justify-center relative">
                    <img id="sidebar-avatar-img" class="w-full h-full object-cover hidden" src="" alt="Profile Image" referrerpolicy="no-referrer">
                    <div class="w-full h-full bg-main-blue/15 border border-main-blue/30 text-main-blue flex items-center justify-center font-bold text-sm uppercase" id="sidebar-avatar-initial">
                        U
                    </div>
                </div>
                <div class="min-w-0">
                    <h4 class="text-xs font-bold text-dark-text truncate" id="sidebar-user-name">{{ Auth::user()->name }}</h4>
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="flex flex-col space-y-1.5">
                <a href="#" data-view="view-dashboard" class="nav-link active-nav-link flex items-center space-x-3.5 px-4 py-3 rounded-xl text-xs font-bold text-grey-text hover:text-dark-text transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
                    <span>Dashboard</span>
                </a>
                <a href="#" data-view="view-explore" class="nav-link flex items-center space-x-3.5 px-4 py-3 rounded-xl text-xs font-bold text-grey-text hover:text-dark-text transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <span>Explore / AI</span>
                </a>
                <a href="#" data-view="view-roadmaps" class="nav-link flex items-center space-x-3.5 px-4 py-3 rounded-xl text-xs font-bold text-grey-text hover:text-dark-text transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                    <span>My Roadmaps</span>
                </a>
                <a href="#" data-view="view-todo" class="nav-link flex items-center space-x-3.5 px-4 py-3 rounded-xl text-xs font-bold text-grey-text hover:text-dark-text transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                    <span>Daftar Tugas</span>
                </a>
                <a href="#" data-view="view-notes" class="nav-link flex items-center space-x-3.5 px-4 py-3 rounded-xl text-xs font-bold text-grey-text hover:text-dark-text transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    <span>Catatan Belajar</span>
                </a>
                <a href="#" data-view="view-profile" class="nav-link flex items-center space-x-3.5 px-4 py-3 rounded-xl text-xs font-bold text-grey-text hover:text-dark-text transition-all duration-300">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    <span>Profil Saya</span>
                </a>
            </nav>
        </div>

        <!-- Logout Form -->
        <form action="{{ route('logout') }}" method="POST" class="w-full">
            @csrf
            <button type="submit" class="w-full py-3 px-4 rounded-xl text-xs font-bold text-red-500 bg-red-500/5 hover:bg-red-500 hover:text-white-pure active:scale-[0.98] transition-all duration-300 flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                <span>Logout</span>
            </button>
        </form>
    </aside>

    <!-- ==========================================
         MOBILE BOTTOM NAVIGATION
         ========================================== -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white-pure/90 backdrop-blur-xl border-t border-dark-text/5 h-16 flex items-center justify-around px-2 z-40">
        <a href="#" data-view="view-dashboard" class="nav-link active-nav-link flex flex-col items-center justify-center flex-1 h-full py-1.5 text-grey-text">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" /></svg>
            <span class="text-[9px] font-bold mt-1">Home</span>
        </a>
        <a href="#" data-view="view-explore" class="nav-link flex flex-col items-center justify-center flex-1 h-full py-1.5 text-grey-text">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <span class="text-[9px] font-bold mt-1">Explore</span>
        </a>
        <a href="#" data-view="view-roadmaps" class="nav-link flex flex-col items-center justify-center flex-1 h-full py-1.5 text-grey-text">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
            <span class="text-[9px] font-bold mt-1">Roadmaps</span>
        </a>
        <a href="#" data-view="view-todo" class="nav-link flex flex-col items-center justify-center flex-1 h-full py-1.5 text-grey-text">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
            <span class="text-[9px] font-bold mt-1">To Do</span>
        </a>
        <a href="#" data-view="view-notes" class="nav-link flex flex-col items-center justify-center flex-1 h-full py-1.5 text-grey-text">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            <span class="text-[9px] font-bold mt-1">Notes</span>
        </a>
        <a href="#" data-view="view-profile" class="nav-link flex flex-col items-center justify-center flex-1 h-full py-1.5 text-grey-text">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            <span class="text-[9px] font-bold mt-1">Profile</span>
        </a>
    </nav>

    <!-- ==========================================
         MAIN CONTENT AREA
         ========================================== -->
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 md:px-8 py-6 md:py-10 pb-24 md:pb-10 z-10">
        
        <!-- ==========================================
             TAB 1: VIEW DASHBOARD
             ========================================== -->
        <section id="view-dashboard" class="view-pane space-y-6 md:space-y-8">
            <!-- Header greeting -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-dark-text letter-tracking">
                        Halo, <span class="font-serif italic font-normal text-main-blue" id="dashboard-user-greeting">{{ Auth::user()->name }}</span>!
                    </h1>
                    <p class="text-xs text-grey-text font-semibold mt-1">Selamat datang kembali. Mari lanjutkan pencapaian belajar Anda hari ini!</p>
                </div>
            </div>

            <!-- Bento Stats Row -->
            <div class="grid grid-cols-3 gap-4 md:gap-6">
                <!-- Milestones Completed Stats Box -->
                <div class="gradient-border-card p-4 md:p-6 shadow-sm bg-white-pure border border-dark-text/5 flex items-center space-x-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-main-blue/10 text-main-blue rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-grey-text block">Selesai</span>
                        <h3 class="text-base md:text-xl font-bold text-dark-text mt-0.5 leading-none" id="stat-completed-count">0</h3>
                    </div>
                </div>

                <!-- Streak Stats Box -->
                <div class="gradient-border-card p-4 md:p-6 shadow-sm bg-white-pure border border-dark-text/5 flex items-center space-x-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-500/10 text-orange-500 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-grey-text block">Streak</span>
                        <h3 class="text-base md:text-xl font-bold text-dark-text mt-0.5 leading-none" id="stat-streak-count">1 Hari</h3>
                    </div>
                </div>

                <!-- Hours Stats Box -->
                <div class="gradient-border-card p-4 md:p-6 shadow-sm bg-white-pure border border-dark-text/5 flex items-center space-x-4">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-500/10 text-green-500 rounded-2xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[9px] md:text-[10px] font-bold uppercase tracking-wider text-grey-text block">Waktu Belajar</span>
                        <h3 class="text-base md:text-xl font-bold text-dark-text mt-0.5 leading-none" id="stat-hours-count">0.0 Jam</h3>
                    </div>
                </div>
            </div>

            <!-- Bento Dashboard Grid layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Widgets (8 Cols) -->
                <div class="lg:col-span-7 space-y-6">
                    <!-- Active Path Card -->
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text mb-4">Jalur Belajar Aktif</h3>
                        <div id="active-path-widget-container">
                            <div class="animate-pulse flex space-x-4">
                                <div class="flex-1 space-y-3 py-1">
                                    <div class="h-4 bg-dark-text/10 rounded w-3/4"></div>
                                    <div class="h-2 bg-dark-text/10 rounded"></div>
                                    <div class="h-6 bg-dark-text/10 rounded w-1/4 mt-4"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Weekly Chart Card -->
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text mb-6">Aktivitas Mingguan</h3>
                        
                        <div class="flex items-end justify-between h-40 px-2 md:px-6 relative">
                            <!-- Helper grid lines -->
                            <div class="absolute inset-x-0 bottom-[1px] border-b border-dark-text/5"></div>
                            <div class="absolute inset-x-0 bottom-[40px] border-b border-dark-text/5 border-dashed"></div>
                            <div class="absolute inset-x-0 bottom-[80px] border-b border-dark-text/5 border-dashed"></div>
                            <div class="absolute inset-x-0 bottom-[120px] border-b border-dark-text/5 border-dashed"></div>

                            <!-- Weekly Bars -->
                            @foreach(['MON' => 'S', 'TUE' => 'S', 'WED' => 'R', 'THU' => 'K', 'FRI' => 'J', 'SAT' => 'S', 'SUN' => 'M'] as $day => $label)
                                <div class="chart-bar-container flex flex-col items-center justify-end h-full w-8 relative z-10 group">
                                    <!-- Tooltip hover -->
                                    <span id="chart-val-{{ $day }}" class="chart-tooltip opacity-0 scale-90 translate-y-0 absolute bottom-[130px] bg-dark-text text-[9px] font-bold text-white-pure px-2 py-0.5 rounded-full transition-all duration-300 pointer-events-none shadow"></span>
                                    
                                    <!-- Bar Graphic -->
                                    <div id="chart-bar-{{ $day }}" class="w-4 rounded-t-full transition-all duration-500 ease-out cursor-pointer" style="height: 0px;"></div>
                                    
                                    <!-- Label -->
                                    <span id="chart-text-{{ $day }}" class="text-[10px] font-bold text-grey-text mt-3">{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Right Widgets (5 Cols) -->
                <div class="lg:col-span-5 space-y-6">
                    <!-- Daily To Dos Widget -->
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5">
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text">Tugas Belajar</h3>
                        </div>

                        <!-- Add Todo form inline -->
                        <form id="quick-add-todo-form" class="flex items-center space-x-2 mb-4">
                            <input type="text" id="todo-title-input" required placeholder="Tambah tugas baru..."
                                   class="flex-1 bg-white-bg/60 border border-dark-text/10 text-dark-text focus:border-main-blue rounded-full px-4 py-2 text-xs font-medium outline-none transition-all duration-300">
                            <button type="submit" class="w-8 h-8 rounded-full bg-dark-text hover:bg-main-blue text-white-pure flex items-center justify-center shrink-0 active:scale-[0.95] transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                            </button>
                        </form>

                        <!-- Scrollable list -->
                        <div id="todos-list-container" class="space-y-3 max-h-[350px] overflow-y-auto pr-1">
                            <!-- Loader -->
                            <div class="text-center py-6"><div class="animate-spin rounded-full h-5 w-5 border-b-2 border-main-blue mx-auto"></div></div>
                        </div>
                    </div>

                    <!-- Progress Stats Overview Panel -->
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text">Ringkasan Progres</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-3.5 bg-white-bg/40 border border-dark-text/5 rounded-2xl">
                                <span class="text-[9px] font-bold text-grey-text uppercase">Total Waktu</span>
                                <h4 class="text-lg font-bold text-dark-text mt-1"><span id="overview-total-hours">0.0</span> Jam</h4>
                            </div>
                            <div class="p-3.5 bg-white-bg/40 border border-dark-text/5 rounded-2xl">
                                <span class="text-[9px] font-bold text-grey-text uppercase">Milestones</span>
                                <h4 class="text-lg font-bold text-dark-text mt-1"><span id="overview-total-milestones">0</span> Topik</h4>
                            </div>
                            <div class="p-3.5 bg-white-bg/40 border border-dark-text/5 rounded-2xl">
                                <span class="text-[9px] font-bold text-grey-text uppercase">Keaktifan</span>
                                <h4 class="text-lg font-bold text-dark-text mt-1"><span id="overview-days-active">1</span> Hari</h4>
                            </div>
                            <div class="p-3.5 bg-white-bg/40 border border-dark-text/5 rounded-2xl">
                                <span class="text-[9px] font-bold text-grey-text uppercase">Rata-rata</span>
                                <h4 class="text-lg font-bold text-dark-text mt-1"><span id="overview-daily-average">0.0</span> Jam/Hari</h4>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==========================================
             TAB 2: VIEW EXPLORE / GENERATION
             ========================================== -->
        <section id="view-explore" class="view-pane hidden space-y-8">
            <div class="max-w-3xl">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-dark-text letter-tracking">
                    Temukan Jalur <span class="font-serif italic font-normal text-main-blue">Belajar</span> Baru
                </h1>
                <p class="text-xs text-grey-text font-semibold mt-1">Ketikkan topik atau minat apa saja yang ingin Anda pelajari, dan asisten AI kami akan menyusun kurikulum/roadmap belajar terstruktur secara instan.</p>
            </div>

            <!-- Generate Form Card -->
            <div class="gradient-border-card p-6 md:p-8 shadow-sm bg-white-pure border border-dark-text/5 max-w-4xl">
                <form id="ai-generate-form" class="space-y-4">
                    <div class="flex flex-col space-y-2">
                        <label for="explore-search-input" class="text-xs font-bold uppercase tracking-wider text-grey-text">Topik Pembelajaran</label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" id="explore-search-input" required placeholder="Masukkan topik, misal: Pemrograman Golang, Dasar-Dasar Akuntansi, Figma UI/UX..."
                                   class="flex-1 bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-3.5 placeholder:text-grey-text text-sm font-medium outline-none transition-all duration-300">
                            
                            <button type="submit" class="px-8 py-3.5 bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-sm font-bold text-white-pure rounded-xl flex items-center justify-center space-x-2 shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 9.172V5L8 4z" /></svg>
                                <span>Buat Roadmap AI</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Popular Suggestions Grid -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text mb-4">Saran Topik Populer</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-4xl" id="explore-suggestions-container">
                    <!-- Suggestion 1 -->
                    <div onclick="document.getElementById('explore-search-input').value = 'Dasar Pemrograman Python'; document.getElementById('explore-search-input').focus();"
                         class="explore-suggested-card p-5 bg-white-pure/60 border border-dark-text/5 rounded-2xl hover:bg-main-blue/5 hover:translate-y-[-2px] transition-all duration-300 cursor-pointer text-left">
                        <h4 class="suggested-title text-sm font-bold text-dark-text mb-1">Dasar Python</h4>
                        <p class="text-[10px] text-grey-text font-medium leading-relaxed">Fundamental pemrograman, tipe data, kontrol alur, dan fungsi.</p>
                    </div>

                    <!-- Suggestion 2 -->
                    <div onclick="document.getElementById('explore-search-input').value = 'Figma UI/UX Design'; document.getElementById('explore-search-input').focus();"
                         class="explore-suggested-card p-5 bg-white-pure/60 border border-dark-text/5 rounded-2xl hover:bg-main-blue/5 hover:translate-y-[-2px] transition-all duration-300 cursor-pointer text-left">
                        <h4 class="suggested-title text-sm font-bold text-dark-text mb-1">UI/UX Design</h4>
                        <p class="text-[10px] text-grey-text font-medium leading-relaxed">Pengenalan Figma, prototyping, wireframe, dan design system.</p>
                    </div>

                    <!-- Suggestion 3 -->
                    <div onclick="document.getElementById('explore-search-input').value = 'Belajar RESTful API Laravel'; document.getElementById('explore-search-input').focus();"
                         class="explore-suggested-card p-5 bg-white-pure/60 border border-dark-text/5 rounded-2xl hover:bg-main-blue/5 hover:translate-y-[-2px] transition-all duration-300 cursor-pointer text-left">
                        <h4 class="suggested-title text-sm font-bold text-dark-text mb-1">Laravel RESTful API</h4>
                        <p class="text-[10px] text-grey-text font-medium leading-relaxed">Routing, Controller, Eloquent ORM, dan otentikasi Sanctum.</p>
                    </div>

                    <!-- Suggestion 4 -->
                    <div onclick="document.getElementById('explore-search-input').value = 'Project Management Agile'; document.getElementById('explore-search-input').focus();"
                         class="explore-suggested-card p-5 bg-white-pure/60 border border-dark-text/5 rounded-2xl hover:bg-main-blue/5 hover:translate-y-[-2px] transition-all duration-300 cursor-pointer text-left">
                        <h4 class="suggested-title text-sm font-bold text-dark-text mb-1">Agile Scrum</h4>
                        <p class="text-[10px] text-grey-text font-medium leading-relaxed">Siklus sprint, backlog product, standup, dan scrum roles.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================
             TAB 3: VIEW MY ROADMAPS
             ========================================== -->
        <section id="view-roadmaps" class="view-pane hidden space-y-6">
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-dark-text letter-tracking mb-6">
                Roadmap <span class="font-serif italic font-normal text-main-blue">Belajar</span> Saya
            </h1>
            
            <div id="roadmaps-list-container" class="space-y-6">
                <!-- Loader -->
                <div class="text-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-main-blue mx-auto"></div></div>
            </div>
        </section>

        <!-- ==========================================
             TAB 4: VIEW TO DO LIST
             ========================================== -->
        <section id="view-todo" class="view-pane hidden space-y-6">
            <div class="max-w-3xl">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-dark-text letter-tracking">
                    Daftar Tugas <span class="font-serif italic font-normal text-main-blue">Belajar</span>
                </h1>
                <p class="text-xs text-grey-text font-semibold mt-1">Kelola tugas belajar harian Anda secara mandiri di sini. Tandai yang selesai untuk melacak waktu keaktifan Anda.</p>
            </div>

            <!-- Full Todo Manager Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Column: Add Task (5 Cols) -->
                <div class="lg:col-span-5">
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text" id="todo-manager-title">Tambah Tugas Baru</h3>
                        <form id="full-todo-form" class="space-y-4">
                            <input type="hidden" id="edit-todo-id" value="">
                            <div class="flex flex-col space-y-1.5">
                                <label for="todo-task-title" class="text-xs font-bold uppercase tracking-wider text-grey-text">Judul Tugas</label>
                                <input type="text" id="todo-task-title" required placeholder="Belajar syntax basic Python..."
                                       class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-semibold outline-none transition-all duration-300">
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <label for="todo-task-desc" class="text-xs font-bold uppercase tracking-wider text-grey-text">Deskripsi (Opsional)</label>
                                <textarea id="todo-task-desc" rows="3" placeholder="Selesaikan module 1 sampai 4..."
                                          class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-semibold outline-none transition-all duration-300 resize-none"></textarea>
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <label for="todo-task-due" class="text-xs font-bold uppercase tracking-wider text-grey-text">Batas Waktu (Due Date)</label>
                                <input type="date" id="todo-task-due"
                                       class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-semibold outline-none transition-all duration-300">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" id="todo-submit-btn" class="flex-1 py-3.5 bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                                    <span>Tambah Tugas</span>
                                </button>
                                <button type="button" id="todo-cancel-edit-btn" class="hidden px-5 py-3.5 border border-dark-text/10 text-dark-text hover:bg-white-bg text-xs font-bold rounded-full transition-all duration-200" onclick="window.trackerinDashboard.cancelEditTodo()">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Task List & Filters (7 Cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5 space-y-5">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text">Daftar Tugas Anda</h3>
                            <!-- Filters -->
                            <div class="flex items-center space-x-1.5 bg-white-bg border border-dark-text/5 p-1 rounded-xl">
                                <button type="button" onclick="window.trackerinDashboard.filterTodos('all')" id="todo-filter-all" class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-dark-text text-white-pure transition-all duration-200">Semua</button>
                                <button type="button" onclick="window.trackerinDashboard.filterTodos('active')" id="todo-filter-active" class="px-3 py-1.5 rounded-lg text-[10px] font-bold text-grey-text hover:text-dark-text transition-all duration-200">Aktif</button>
                                <button type="button" onclick="window.trackerinDashboard.filterTodos('completed')" id="todo-filter-completed" class="px-3 py-1.5 rounded-lg text-[10px] font-bold text-grey-text hover:text-dark-text transition-all duration-200">Selesai</button>
                            </div>
                        </div>

                        <!-- Complete Todo List Container -->
                        <div id="full-todos-list-container" class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                            <div class="text-center py-8 text-grey-text text-sm font-medium">Memuat tugas belajar...</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================
             TAB 5: VIEW NOTES
             ========================================== -->
        <section id="view-notes" class="view-pane hidden space-y-6">
            <div class="max-w-3xl">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-dark-text letter-tracking">
                    Catatan <span class="font-serif italic font-normal text-main-blue">Belajar</span>
                </h1>
                <p class="text-xs text-grey-text font-semibold mt-1">Simpan ringkasan materi, ide, dan catatan belajar Anda di sini agar mudah ditinjau kembali kapan saja.</p>
            </div>

            <!-- Full Notes Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <!-- Left Column: Add/Edit General Note (5 Cols) -->
                <div class="lg:col-span-5">
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text" id="notes-manager-title">Buat Catatan Baru</h3>
                        <form id="full-note-form" class="space-y-4">
                            <input type="hidden" id="edit-note-id" value="">
                            <div class="flex flex-col space-y-1.5">
                                <label for="note-title" class="text-xs font-bold uppercase tracking-wider text-grey-text">Judul Catatan</label>
                                <input type="text" id="note-title" required placeholder="Ringkasan OOP Javascript..."
                                       class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-semibold outline-none transition-all duration-300">
                            </div>
                            <div class="flex flex-col space-y-1.5">
                                <label for="note-content" class="text-xs font-bold uppercase tracking-wider text-grey-text">Isi Catatan</label>
                                <textarea id="note-content" rows="6" required placeholder="Tulis catatan lengkap belajar Anda..."
                                          class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-semibold outline-none transition-all duration-300 resize-none"></textarea>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 py-3.5 bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full" id="note-submit-btn">
                                    Simpan Catatan
                                </button>
                                <button type="button" id="note-cancel-edit-btn" class="hidden px-5 py-3.5 border border-dark-text/10 text-dark-text hover:bg-white-bg text-xs font-bold rounded-full transition-all duration-200" onclick="window.trackerinDashboard.cancelEditNote()">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Notes List & Search (7 Cols) -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5 space-y-5">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text">Daftar Catatan</h3>
                            <input type="text" id="notes-search-filter" placeholder="Cari catatan..."
                                   class="bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-full px-4 py-2 text-xs font-medium outline-none transition-all duration-300">
                        </div>

                        <!-- Complete Notes List Container -->
                        <div id="full-notes-list-container" class="space-y-4 max-h-[500px] overflow-y-auto pr-1">
                            <div class="text-center py-6"><div class="animate-spin rounded-full h-5 w-5 border-b-2 border-main-blue mx-auto"></div></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ==========================================
             TAB 6: VIEW PROFILE & SETTINGS
             ========================================== -->
        <section id="view-profile" class="view-pane hidden space-y-8">
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                <!-- Left: Edit Profile Credentials Form -->
                <div class="w-full lg:w-7/12 space-y-6">
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5 space-y-6">
                        <!-- Profile Card Header -->
                        <div class="flex items-center space-x-4 pb-6 border-b border-dark-text/5">
                            <div class="relative w-16 h-16 rounded-full overflow-hidden shrink-0 group cursor-pointer border border-dark-text/5" id="profile-avatar-clickable" title="Klik untuk ubah foto profil">
                                <img id="profile-avatar-img" class="w-full h-full object-cover hidden" src="" alt="Profile Image" referrerpolicy="no-referrer">
                                <div class="w-full h-full bg-main-blue text-white-pure text-2xl font-bold flex items-center justify-center uppercase" id="profile-avatar-initial">
                                    U
                                </div>
                                <!-- Dark overlay on hover -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white-pure" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-dark-text truncate" id="profile-user-name">{{ Auth::user()->name }}</h3>
                            </div>
                        </div>

                        <!-- Edit Form -->
                        <form id="profile-edit-form" class="space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col space-y-1.5">
                                    <label for="profile-name-input" class="text-xs font-bold uppercase tracking-wider text-grey-text">Nama Lengkap</label>
                                    <input type="text" id="profile-name-input" required
                                           class="bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-semibold outline-none transition-all duration-300">
                                </div>
                                <div class="flex flex-col space-y-1.5">
                                    <label for="profile-email-input" class="text-xs font-bold uppercase tracking-wider text-grey-text">Email</label>
                                    <input type="email" id="profile-email-input" required
                                           class="bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-semibold outline-none transition-all duration-300">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-dark-text/5 flex flex-col sm:flex-row gap-4 items-center justify-between">
                                <a href="{{ route('change-password') }}" class="px-5 py-3 rounded-full border border-dark-text/10 hover:border-dark-text/30 bg-white-pure hover:bg-white-bg text-dark-text font-bold text-xs transition-all duration-300 active:scale-[0.98] inline-flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-grey-text" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <span>Ubah Kata Sandi</span>
                                </a>
                                <button type="submit" class="px-8 py-3.5 bg-dark-text hover:bg-main-blue active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full shrink-0">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right: Contact Us Form -->
                <div class="w-full lg:w-5/12 space-y-6">
                    <div class="gradient-border-card p-6 shadow-sm bg-white-pure border border-dark-text/5 space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-grey-text">Hubungi Kami</h3>
                        <form id="dashboard-contact-form" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <input type="text" id="contact-name" required placeholder="Nama Anda"
                                       class="bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all duration-300">
                                <input type="email" id="contact-email" required placeholder="Email Anda"
                                       class="bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all duration-300">
                            </div>
                            <input type="text" id="contact-subject" required placeholder="Subjek"
                                   class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all duration-300">
                            <textarea id="contact-message" rows="4" required placeholder="Tulis pesan Anda di sini..."
                                      class="w-full bg-white-bg border border-dark-text/10 text-dark-text focus:border-main-blue rounded-xl px-4 py-2.5 text-xs font-medium outline-none transition-all duration-300 resize-none"></textarea>
                            <button type="submit" class="w-full py-3 bg-main-blue hover:bg-hover-blue active:scale-[0.98] transition-all duration-300 text-xs font-bold text-white-pure rounded-full">
                                Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </section>

        <!-- ==========================================
             TAB 7: VIEW QUIZ (SPA PANE)
             ========================================== -->
        <section id="view-quiz" class="view-pane hidden space-y-6">
            <div class="flex items-center gap-3 mb-6">
                <button onclick="window.trackerinDashboard.backToRoadmapDetailsFromQuiz()" class="p-2 text-grey-text hover:text-dark-text hover:bg-white-bg rounded-full transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-dark-text" id="quiz-pane-title">Evaluasi Kuis AI</h2>
                    <p class="text-xs text-grey-text font-semibold mt-0.5" id="quiz-pane-subtitle">Jawab kuis di bawah untuk menguji pemahaman Anda.</p>
                </div>
            </div>

            <div class="gradient-border-card p-6 md:p-8 shadow-sm bg-white-pure border border-dark-text/5 max-w-4xl" id="quiz-pane-content">
                <!-- Loaded dynamically -->
            </div>
        </section>
    </main>

    <!-- ==========================================
         FULL PAGE POPUPS / OVERLAYS
         ========================================== -->

    <!-- 2. AI GENERATE LOADER OVERLAY -->
    <div id="ai-generation-loader" class="hidden fixed inset-0 z-50 bg-white-pure flex flex-col items-center justify-center p-8">
        <div class="w-24 h-24 relative mb-8">
            <!-- Sleek pulse loader dots -->
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-main-blue/20 opacity-75"></span>
            <div class="w-20 h-20 rounded-full bg-main-blue/10 flex items-center justify-center text-main-blue relative top-2 left-2">
                <svg class="w-10 h-10 animate-spin" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <h2 class="text-xl font-bold text-dark-text text-center letter-tracking mb-3">AI sedang merancang roadmap Anda...</h2>
        <p id="loader-text-status" class="text-xs text-grey-text font-bold text-center animate-pulse">Menganalisis topik pelajaran Anda...</p>
    </div>

    <!-- 3. HIDDEN PROFILE AVATAR UPLOADER -->
    <input type="file" id="avatar-file-input" class="hidden" accept="image/*">

</body>

</html>
