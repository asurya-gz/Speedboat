<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Ganti Password - Speedboat Ticketing</title>
    
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
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
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
            id="theme-toggle" 
            class="p-3 text-gray-500 dark:text-gray-400 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-full hover:text-gray-900 dark:hover:text-white hover:bg-white/90 dark:hover:bg-gray-700/90 transition-all duration-200 shadow-lg"
            onclick="toggleTheme()"
        >
            <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
            </svg>
        </button>
    </div>

<div class="flex flex-col items-center justify-center min-h-screen px-6 py-8 relative z-10">
    <div class="w-full max-w-md fade-in-up">
        <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-8">
                <div class="text-center mb-8">
                    <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900 mb-4">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Ganti Password
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Silakan buat password baru untuk akun Anda
                    </p>
                </div>

                @if(session('info'))
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200 px-4 py-3 rounded-md mb-6">
                        {{ session('info') }}
                    </div>
                @endif

                <form class="space-y-6" action="{{ route('password.change') }}" method="POST">
                    @csrf
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password Baru</label>
                        <div class="relative">
                            <input id="password" 
                                   name="password" 
                                   type="password" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 @error('password') border-red-500 @enderror" 
                                   placeholder="Password baru (minimal 8 karakter)"
                                   onkeyup="validatePassword()">
                            <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg id="eye-open-password" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-closed-password" class="h-5 w-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        
                        <!-- Password Requirements Indicator -->
                        <div id="password-requirements" class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Persyaratan Password:</p>
                            <div class="space-y-1">
                                <div id="req-length" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-red-600 dark:text-red-400">Minimal 8 karakter</span>
                                </div>
                                <div id="req-lowercase" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-red-600 dark:text-red-400">Minimal 1 huruf kecil (a-z)</span>
                                </div>
                                <div id="req-uppercase" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-red-600 dark:text-red-400">Minimal 1 huruf besar (A-Z)</span>
                                </div>
                                <div id="req-number" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-red-600 dark:text-red-400">Minimal 1 angka (0-9)</span>
                                </div>
                                <div id="req-special" class="flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-red-600 dark:text-red-400">Minimal 1 karakter khusus (@$!%*?&)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input id="password_confirmation" 
                                   name="password_confirmation" 
                                   type="password" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400" 
                                   placeholder="Konfirmasi password baru"
                                   onkeyup="validatePasswordConfirmation()">
                            <button type="button" onclick="togglePasswordVisibility('password_confirmation')" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg id="eye-open-confirm" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-closed-confirm" class="h-5 w-5 text-gray-400 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="password-match" class="mt-2 hidden">
                            <div class="flex items-center text-sm">
                                <svg id="match-icon" class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <span id="match-text"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2-2m2 2a2 2 0 002 2m0 0a2 2 0 01-2 2m-2-2a2 2 0 002-2"></path>
                                </svg>
                                Ganti Password
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
    
    // Password visibility toggle
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const eyeOpenId = fieldId === 'password' ? 'eye-open-password' : 'eye-open-confirm';
        const eyeClosedId = fieldId === 'password' ? 'eye-closed-password' : 'eye-closed-confirm';
        const eyeOpen = document.getElementById(eyeOpenId);
        const eyeClosed = document.getElementById(eyeClosedId);
        
        if (field.type === 'password') {
            field.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            field.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
    
    // Password validation
    function validatePassword() {
        const password = document.getElementById('password').value;
        
        // Check length
        const lengthReq = document.getElementById('req-length');
        const lengthValid = password.length >= 8;
        updateRequirement(lengthReq, lengthValid);
        
        // Check lowercase
        const lowercaseReq = document.getElementById('req-lowercase');
        const lowercaseValid = /[a-z]/.test(password);
        updateRequirement(lowercaseReq, lowercaseValid);
        
        // Check uppercase
        const uppercaseReq = document.getElementById('req-uppercase');
        const uppercaseValid = /[A-Z]/.test(password);
        updateRequirement(uppercaseReq, uppercaseValid);
        
        // Check number
        const numberReq = document.getElementById('req-number');
        const numberValid = /\d/.test(password);
        updateRequirement(numberReq, numberValid);
        
        // Check special character
        const specialReq = document.getElementById('req-special');
        const specialValid = /[@$!%*?&]/.test(password);
        updateRequirement(specialReq, specialValid);
        
        // Update password confirmation validation
        validatePasswordConfirmation();
        
        // Update submit button state
        updateSubmitButton();
    }
    
    function validatePasswordConfirmation() {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        const matchDiv = document.getElementById('password-match');
        const matchIcon = document.getElementById('match-icon');
        const matchText = document.getElementById('match-text');
        
        if (confirmation.length > 0) {
            matchDiv.classList.remove('hidden');
            
            if (password === confirmation) {
                // Passwords match
                matchIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
                matchIcon.className = 'w-4 h-4 mr-2 text-green-500';
                matchText.className = 'text-green-600 dark:text-green-400';
                matchText.textContent = 'Password cocok';
            } else {
                // Passwords don't match
                matchIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
                matchIcon.className = 'w-4 h-4 mr-2 text-red-500';
                matchText.className = 'text-red-600 dark:text-red-400';
                matchText.textContent = 'Password tidak cocok';
            }
        } else {
            matchDiv.classList.add('hidden');
        }
        
        updateSubmitButton();
    }
    
    function updateRequirement(element, isValid) {
        const icon = element.querySelector('svg');
        const text = element.querySelector('span');
        
        if (isValid) {
            // Valid - show checkmark
            icon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
            icon.className = 'w-4 h-4 mr-2 text-green-500';
            text.className = 'text-green-600 dark:text-green-400';
        } else {
            // Invalid - show X
            icon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
            icon.className = 'w-4 h-4 mr-2 text-red-500';
            text.className = 'text-red-600 dark:text-red-400';
        }
    }
    
    function updateSubmitButton() {
        const password = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        const submitButton = document.querySelector('button[type="submit"]');
        
        const lengthValid = password.length >= 8;
        const lowercaseValid = /[a-z]/.test(password);
        const uppercaseValid = /[A-Z]/.test(password);
        const numberValid = /\d/.test(password);
        const specialValid = /[@$!%*?&]/.test(password);
        const passwordsMatch = password === confirmation && confirmation.length > 0;
        
        const allValid = lengthValid && lowercaseValid && uppercaseValid && numberValid && specialValid && passwordsMatch;
        
        if (allValid) {
            submitButton.disabled = false;
            submitButton.className = 'w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500';
        } else {
            submitButton.disabled = true;
            submitButton.className = 'w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-gray-400 cursor-not-allowed opacity-50';
        }
    }
    
    // Initialize theme icons on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateThemeIcons();
        updateSubmitButton(); // Initialize button state
        
        // Force re-apply theme after page load to ensure Tailwind is ready
        setTimeout(() => {
            initTheme();
            updateThemeIcons();
        }, 100);
    });
</script>
</body>
</html>