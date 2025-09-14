<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Speedboat Ticketing System') - {{ config('app.name', 'Speedboat Ticketing') }}</title>
    
    <!-- Meta Description -->
    <meta name="description" content="@yield('description', 'Sistem Manajemen Tiket Speedboat - Fast, Safe, Reliable. Kelola penjualan tiket, jadwal keberangkatan, dan data penumpang speedboat dengan mudah.')">
    <meta name="keywords" content="speedboat, tiket, ticketing system, transportasi laut, booking, penjualan tiket">
    <meta name="author" content="Speedboat Ticketing System">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('image/icon.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/icon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('image/icon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#3b82f6">
    
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Speedboat Ticketing System')">
    <meta property="og:description" content="@yield('description', 'Sistem Manajemen Tiket Speedboat - Fast, Safe, Reliable')">
    <meta property="og:image" content="{{ asset('image/logo.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Speedboat Ticketing System')">
    <meta name="twitter:description" content="@yield('description', 'Sistem Manajemen Tiket Speedboat - Fast, Safe, Reliable')">
    <meta name="twitter:image" content="{{ asset('image/logo.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Flowbite Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/20/solid/index.css">
    
    <!-- Compiled CSS -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Dark mode initialization script -->
    <script>
        // Initialize dark mode
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-200">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 sidebar-gradient transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" id="sidebar">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-800">
                    <div class="text-center">
                        <h4 class="text-xl font-bold text-gray-800 dark:text-white flex items-center justify-center">
                            <img src="{{ asset('image/logo.png') }}" alt="Speedboat Logo" class="w-8 h-8 mr-2 object-contain">
                            Speedboat
                        </h4>
                        <small class="text-gray-600 dark:text-gray-300">Ticketing System</small>
                    </div>
                </div>
                
                <!-- Navigation -->
                <nav class="flex-1 px-4 py-6 space-y-2">
                    <!-- Dashboard - accessible by all -->
                    <a href="{{ route('dashboard') }}" 
                       class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v3H8V5z"></path>
                        </svg>
                        Dashboard
                    </a>

                    @auth
                        @if(Auth::user()->isAdmin() || Auth::user()->isKasir())
                            <!-- Destinations - accessible by Admin and Kasir -->
                            <a href="{{ route('destinations.index') }}" 
                               class="sidebar-nav-item {{ request()->routeIs('destinations.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Destinasi
                            </a>
                        @endif

                        @if(Auth::user()->isAdmin() || Auth::user()->isKasir() || Auth::user()->isBoarding())
                            <!-- Schedules - accessible by all roles -->
                            <a href="{{ route('schedules.index') }}" 
                               class="sidebar-nav-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Jadwal
                            </a>
                        @endif

                        @if(Auth::user()->isAdmin() || Auth::user()->isKasir())
                            <!-- Tiket Menu with Dropdown - accessible by Admin and Kasir -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="sidebar-nav-item w-full flex items-center justify-between {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                        Tiket
                                    </div>
                                    <svg class="w-4 h-4 transition-transform duration-200" 
                                         :class="open ? 'rotate-180' : ''" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                                     x-transition:enter-end="opacity-100 transform translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform translate-y-0"
                                     x-transition:leave-end="opacity-0 transform -translate-y-2"
                                     class="ml-8 mt-2 space-y-1">
                                    <a href="{{ route('transactions.create') }}" 
                                       class="sidebar-submenu-item {{ request()->routeIs('transactions.create') ? 'active' : '' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Penjualan Tiket
                                    </a>
                                    <a href="{{ route('transactions.index') }}" 
                                       class="sidebar-submenu-item {{ request()->routeIs('transactions.index') ? 'active' : '' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Riwayat Transaksi
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if(Auth::user()->isAdmin() || Auth::user()->isBoarding())
                            <!-- QR Validation - accessible by Admin and Boarding -->
                            <a href="#" onclick="alert('Fitur validasi QR akan segera hadir!')" class="sidebar-nav-item">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h4M4 8h4m0 0V4m0 4h4m0 0V4m0 4v4"></path>
                                </svg>
                                Validasi QR
                            </a>
                        @endif

                        @if(Auth::user()->isAdmin())
                            <!-- User Management - accessible by Admin only -->
                            <a href="{{ route('users.index') }}" 
                               class="sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                                Kelola User
                            </a>

                            <!-- Reports - accessible by Admin only -->
                            <a href="#" onclick="alert('Fitur laporan akan segera hadir!')" class="sidebar-nav-item">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Laporan
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </aside>

        <!-- Mobile menu button -->
        <div class="lg:hidden">
            <button type="button" class="fixed top-4 left-4 z-50 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" id="mobile-menu-button">
                <span class="sr-only">Open main menu</span>
                <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Main content -->
        <main class="flex-1 lg:ml-0">
            <div class="px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex items-center justify-between py-6 border-b border-gray-200 dark:border-gray-700">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">@yield('title', 'Dashboard')</h1>
                    <div class="flex items-center space-x-4">
                        @yield('header-actions')
                        
                        <!-- Dark Mode Toggle -->
                        <button 
                            type="button" 
                            id="theme-toggle" 
                            class="p-2 text-gray-500 rounded-lg hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-200"
                            onclick="toggleTheme()"
                        >
                            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                            </svg>
                            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        
                        <!-- User Menu -->
                        @auth
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->getRoleDisplayName() }}</div>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-50">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                                </div>
                                
                                <!-- Change Password Menu -->
                                <button onclick="openChangePasswordModal()" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                    Ubah Password
                                </button>
                                
                                <!-- Logout Form -->
                                <form method="POST" action="{{ route('logout') }}"  class="border-t border-gray-100 dark:border-gray-700">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-green-400 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" class="inline-flex text-green-400 dark:text-green-300 hover:text-green-600 dark:hover:text-green-100" onclick="this.parentElement.parentElement.parentElement.remove()">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-red-400 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="text-sm text-red-800 dark:text-red-200 space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" class="inline-flex text-red-400 dark:text-red-300 hover:text-red-600 dark:hover:text-red-100" onclick="this.parentElement.parentElement.parentElement.remove()">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Page Content -->
                <div class="py-6">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Compiled JavaScript -->
    <script src="{{ mix('js/app.js') }}"></script>
    
    <!-- Mobile menu toggle script -->
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
        
        // Theme toggle functionality
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
            }
            
            updateThemeIcons();
        }
        
        function updateThemeIcons() {
            const darkIcon = document.getElementById('theme-toggle-dark-icon');
            const lightIcon = document.getElementById('theme-toggle-light-icon');
            const isDark = document.documentElement.classList.contains('dark');
            
            if (isDark) {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            } else {
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            }
        }
        
        // Initialize theme icons on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateThemeIcons();
        });
        // Change Password Modal Functions
        function openChangePasswordModal() {
            const modal = document.getElementById('changePasswordModal');
            const modalContent = document.getElementById('changePasswordModalContent');
            
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }
        
        function closeChangePasswordModal() {
            const modal = document.getElementById('changePasswordModal');
            const modalContent = document.getElementById('changePasswordModalContent');
            
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            modal.classList.add('opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                // Reset form
                document.getElementById('changePasswordForm').reset();
                // Clear any error messages
                const errorElements = document.querySelectorAll('.change-password-error');
                errorElements.forEach(el => el.textContent = '');
            }, 300);
        }
    </script>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden z-50 transition-opacity duration-300">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" id="changePasswordModalContent">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ubah Password</h3>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <form id="changePasswordForm" onsubmit="submitChangePassword(event)">
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Password Saat Ini
                            </label>
                            <input type="password" id="current_password" name="current_password" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Masukkan password saat ini">
                            <div class="change-password-error text-red-500 text-xs mt-1" data-field="current_password"></div>
                        </div>

                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Password Baru
                            </label>
                            <input type="password" id="new_password" name="new_password" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Masukkan password baru" 
                                   minlength="8">
                            <div class="change-password-error text-red-500 text-xs mt-1" data-field="new_password"></div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Min. 8 karakter dengan huruf besar, kecil, angka, dan karakter khusus
                            </p>
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Konfirmasi password baru">
                            <div class="change-password-error text-red-500 text-xs mt-1" data-field="confirm_password"></div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-3 rounded-b-xl">
                        <button type="button" onclick="closeChangePasswordModal()" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Batal
                        </button>
                        <button type="submit" id="changePasswordBtn" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-lg hover:shadow-xl">
                            <span id="changePasswordText">Ubah Password</span>
                            <svg id="changePasswordLoading" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Submit change password form
        async function submitChangePassword(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('changePasswordBtn');
            const submitText = document.getElementById('changePasswordText');
            const submitLoading = document.getElementById('changePasswordLoading');
            
            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Mengubah...';
            submitLoading.classList.remove('hidden');
            
            // Clear previous errors
            const errorElements = document.querySelectorAll('.change-password-error');
            errorElements.forEach(el => el.textContent = '');
            
            try {
                const response = await fetch('{{ route("password.update") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Success
                    closeChangePasswordModal();
                    showNotification('Password berhasil diubah!', 'success');
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const errorElement = document.querySelector(`[data-field="${field}"]`);
                            if (errorElement) {
                                errorElement.textContent = data.errors[field][0];
                            }
                        });
                    } else {
                        showNotification(data.message || 'Terjadi kesalahan', 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Terjadi kesalahan sistem', 'error');
            } finally {
                // Reset loading state
                submitBtn.disabled = false;
                submitText.textContent = 'Ubah Password';
                submitLoading.classList.add('hidden');
            }
        }
        
        // Simple notification function
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-md z-50 ${
                type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 
                type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 
                'bg-blue-100 text-blue-800 border border-blue-200'
            }`;
            notification.textContent = message;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Add event listeners for change password modal
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('changePasswordModal');
            
            // Handle click outside modal
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeChangePasswordModal();
                }
            });
            
            // Handle ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeChangePasswordModal();
                }
            });
        });
    </script>
    
    <!-- Custom styles for change password modal animations -->
    <style>
        /* Change Password Modal styles */
        #changePasswordModal {
            transition: opacity 300ms ease-in-out;
        }
        
        #changePasswordModal.hidden {
            opacity: 0;
        }
        
        #changePasswordModalContent {
            transition: transform 300ms ease-in-out;
        }
        
        #changePasswordModalContent.scale-95 {
            transform: scale(0.95);
        }
        
        #changePasswordModalContent.scale-100 {
            transform: scale(1);
        }
        
        /* Backdrop blur effect */
        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }
        
        /* Loading animation */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
    
    @stack('scripts')
</body>
</html>