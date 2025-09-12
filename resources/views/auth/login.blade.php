<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Login - Speedboat Ticketing</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script>
        // Configure Tailwind before loading
        window.tailwindConfig = {
            darkMode: 'class',
            theme: {
                extend: {}
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Dark mode initialization script -->
    <script>
        // Initialize dark mode
        function initTheme() {
            console.log('Initializing theme...');
            console.log('localStorage.theme:', localStorage.theme);
            console.log('System prefers dark:', window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                console.log('Applied dark theme');
            } else {
                document.documentElement.classList.remove('dark');
                console.log('Applied light theme');
            }
        }
        
        // Run immediately
        initTheme();
        
        // Also run after DOM content loaded
        document.addEventListener('DOMContentLoaded', initTheme);
    </script>
    
    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.98) !important;
            backdrop-filter: blur(5px) !important;
            border: 1px solid rgba(200, 200, 200, 0.3) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }
        
        html.dark .glass-effect {
            background: rgba(31, 41, 55, 0.95) !important;
            border: 1px solid rgba(55, 65, 81, 0.8) !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4) !important;
        }
        
        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .wave-bg {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
        }
        
        html.dark .wave-bg {
            background: linear-gradient(135deg, #1f2937 0%, #111827 50%, #0f172a 100%);
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-btn {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }
        
        .input-focus {
            transition: all 0.3s ease;
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .wave-decoration {
            position: absolute;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: wave 4s linear infinite;
        }
        
        @keyframes wave {
            0% { transform: translateX(-50%) translateY(-50%) rotate(0deg); }
            100% { transform: translateX(-50%) translateY(-50%) rotate(360deg); }
        }
        
        .opacity-02 {
            opacity: 0.02;
        }
        
        .opacity-01 {
            opacity: 0.01;
        }
        
        /* Light mode styles - FIXED */
        html:not(.dark) .glass-effect h1,
        html:not(.dark) .glass-effect h2,
        html:not(.dark) .glass-effect p,
        html:not(.dark) .glass-effect label {
            color: #1f2937 !important;
        }
        
        /* Light mode input styles - FIXED */
        html:not(.dark) .glass-effect input {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
            color: #1f2937 !important;
        }
        
        html:not(.dark) .glass-effect input::placeholder {
            color: #9ca3af !important;
        }
        
        html:not(.dark) .glass-effect input:focus {
            background-color: #ffffff !important;
            border-color: #3b82f6 !important;
            color: #1f2937 !important;
        }
        
        /* Dark mode input styles */
        html.dark .glass-effect input {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #ffffff !important;
        }
        
        html.dark .glass-effect input::placeholder {
            color: #9ca3af !important;
        }
        
        html.dark .glass-effect input:focus {
            background-color: #374151 !important;
            border-color: #3b82f6 !important;
            color: #ffffff !important;
        }
        
        /* Force header title visibility in light mode */
        html:not(.dark) h1,
        html:not(.dark) .fade-in-up h1,
        html:not(.dark) .fade-in-up p {
            color: #111827 !important;
            text-shadow: 0 2px 4px rgba(255, 255, 255, 0.8);
        }
        
        /* Icon colors for light/dark mode */
        html:not(.dark) .glass-effect svg {
            color: #6b7280 !important;
        }
        
        html.dark .glass-effect svg {
            color: #9ca3af !important;
        }
        
        /* Checkbox styles */
        html:not(.dark) input[type="checkbox"] {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
        }
        
        html.dark input[type="checkbox"] {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }
        
        /* Link colors */
        html:not(.dark) .glass-effect a {
            color: #2563eb !important;
        }
        
        html.dark .glass-effect a {
            color: #60a5fa !important;
        }
    </style>
</head>
<body class="min-h-screen wave-bg">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-blue-400 dark:bg-blue-600 opacity-20 dark:opacity-30 floating-animation"></div>
        <div class="absolute top-40 -left-20 w-60 h-60 rounded-full bg-blue-300 dark:bg-blue-500 opacity-15 dark:opacity-25 floating-animation" style="animation-delay: -1s;"></div>
        <div class="absolute bottom-40 right-20 w-40 h-40 rounded-full bg-blue-500 dark:bg-blue-400 opacity-25 dark:opacity-35 floating-animation" style="animation-delay: -2s;"></div>
    </div>
    
    <!-- Dark Mode Toggle - Top Right -->
    <div class="fixed top-4 right-4 z-20">
        <button 
            type="button" 
            id="theme-toggle-login" 
            class="p-3 text-gray-500 dark:text-gray-400 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-full hover:text-gray-900 dark:hover:text-white hover:bg-white/90 dark:hover:bg-gray-700/90 transition-all duration-200 shadow-lg"
            onclick="toggleLoginTheme()"
        >
            <svg id="theme-toggle-dark-icon-login" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <svg id="theme-toggle-light-icon-login" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>

    <div class="flex flex-col items-center justify-center min-h-screen px-6 py-8 relative z-10">
        <!-- Logo and Brand -->
        <div class="flex items-center mb-12 fade-in-up">
            <div class="relative">
                <div class="w-20 h-20 rounded-full glass-effect flex items-center justify-center mr-4">
                    <img src="{{ asset('image/logo.png') }}" alt="Speedboat Logo" class="w-14 h-14 object-contain">
                </div>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Speedboat</h1>
                <p class="text-lg text-gray-700 dark:text-gray-300 font-medium">Ticketing System</p>
            </div>
        </div>

        <!-- Login Form -->
        <div class="w-full max-w-md fade-in-up" style="animation-delay: 0.2s;">
            <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Selamat Datang Kembali</h2>
                        <p class="text-gray-800 dark:text-gray-300">Masuk ke akun Anda untuk melanjutkan</p>
                    </div>
                    
                    <!-- Success Alert -->
                    @if(session('success'))
                        <div class="mb-6">
                            <div class="flex items-center p-4 text-sm text-green-800 dark:text-green-200 border border-green-300 dark:border-green-700 rounded-lg bg-green-50/90 dark:bg-green-900/50 backdrop-blur-sm">
                                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                </svg>
                                <div>{{ session('success') }}</div>
                            </div>
                        </div>
                    @endif

                    <!-- Error Alert -->
                    @if($errors->any())
                        <div class="mb-6">
                            <div class="flex items-center p-4 text-sm text-red-800 dark:text-red-200 border border-red-300 dark:border-red-700 rounded-lg bg-red-50/90 dark:bg-red-900/50 backdrop-blur-sm">
                                <svg class="flex-shrink-0 inline w-4 h-4 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    @foreach($errors->all() as $error)
                                        {{ $error }}
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <form class="space-y-6" action="{{ route('login') }}" method="POST">
                        @csrf
                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                    </svg>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="input-focus bg-white dark:bg-gray-700 border @error('email') border-red-300 dark:border-red-600 @else border-gray-300 dark:border-gray-600 @enderror text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-4" 
                                       placeholder="name@company.com" 
                                       value="{{ old('email') }}" 
                                       required>
                            </div>
                            <div id="email-error" class="hidden mt-2 text-sm text-red-600 dark:text-red-400"></div>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       placeholder="••••••••" 
                                       class="input-focus bg-white dark:bg-gray-700 border @error('password') border-red-300 dark:border-red-600 @else border-gray-300 dark:border-gray-600 @enderror text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-4 pr-12" 
                                       required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <button type="button" onclick="togglePassword()" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 transition-colors">
                                        <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div id="password-error" class="hidden mt-2 text-sm text-red-600 dark:text-red-400"></div>
                        </div>

                        <!-- Remember & Forgot -->
                        <div class="flex items-center justify-between">
      
                            <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500 transition-colors">Lupa password?</a>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" 
                                class="login-btn w-full text-white font-semibold rounded-xl text-sm px-5 py-4 text-center shadow-lg">
                            <span id="login-text">Masuk</span>
                            <svg id="login-loading" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>

                    <!-- Sign Up Link -->
                    <div class="text-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <p class="text-sm text-gray-800 dark:text-gray-300">
                            Belum punya akun? 
                            <a href="#" class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500 transition-colors">Daftar di sini</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 fade-in-up" style="animation-delay: 0.4s;">
            <p class="text-gray-500 dark:text-gray-400 text-sm">© {{ date('Y') }} Speedboat Ticketing. All rights reserved.</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Theme toggle functionality for login page
        function toggleLoginTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            
            console.log('Toggling theme, currently dark:', isDark);
            
            if (isDark) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
                console.log('Switched to light mode');
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
                console.log('Switched to dark mode');
            }
            
            updateLoginThemeIcons();
        }
        
        function updateLoginThemeIcons() {
            const darkIcon = document.getElementById('theme-toggle-dark-icon-login');
            const lightIcon = document.getElementById('theme-toggle-light-icon-login');
            const isDark = document.documentElement.classList.contains('dark');
            
            console.log('Updating icons, isDark:', isDark);
            console.log('HTML classes:', document.documentElement.className);
            
            if (isDark) {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            } else {
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            }
        }

        // Add floating animation to input fields on focus
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('transform', 'scale-105');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('transform', 'scale-105');
            });
        });
        
        // Initialize theme icons on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateLoginThemeIcons();
            
            // Force re-apply theme after page load to ensure Tailwind is ready
            setTimeout(() => {
                initTheme();
                updateLoginThemeIcons();
            }, 100);
        });
    </script>
</body>
</html>