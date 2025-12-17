<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ar' ? 'ar' : 'en' }}" class="scroll-smooth {{ session('theme') === 'dark' ? 'dark' : '' }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'EcommStore') - Modern E-commerce Platform</title>
    <meta name="description" content="@yield('meta_description', 'Shop amazing products at EcommStore')">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Leaflet Map Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
    <style>
        /* RTL Support for Arabic */
        [dir="rtl"] .rtl\:ml-auto { margin-left: auto; margin-right: 0; }
        [dir="rtl"] .rtl\:mr-auto { margin-right: auto; margin-left: 0; }
        [dir="rtl"] .rtl\:pr-0 { padding-right: 0; padding-left: 1rem; }
        [dir="rtl"] .rtl\:pl-0 { padding-left: 0; padding-right: 1rem; }
        
        /* Toast Animation */
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        /* Toast Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        /* Enhanced UI Styles for All Pages */
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .section-title {
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        /* Hero Section Enhancements */
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Category Card Enhancements */
        .category-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Product Card Enhancements */
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* CTA Section Enhancements */
        .cta-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Background Animation */
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }
        
        /* Dropdown Menu Styles (matching filter dropdowns) */
        .dropdown-menu {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .dropdown-item {
            transition: all 0.2s ease;
            padding: 0.75rem 1rem;
            border-radius: 0.25rem;
            margin: 0.25rem 0.5rem;
        }
        
        .dropdown-item:hover {
            background-color: #f3f4f6;
            color: #667eea !important;
        }
        
        .dark .dropdown-item:hover {
            background-color: #374151;
        }
    </style>
    <!-- Immediate Theme Initialization Script -->
    <script>
        // Initialize theme immediately to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme') || '{{ session('theme', 'light') }}';
            const html = document.documentElement;
            
            if (savedTheme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="antialiased bg-gray-200 dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-300">
    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left Side - Logo & Navigation -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-primary dark:text-blue-400 hover:text-primary-dark transition">
                        Store
                    </a>
                    
                    <nav class="hidden md:flex space-x-6">

                        
                        <!-- Static Pages Navigation -->
                        <!-- More Menu Button -->
                        <button onclick="toggleMoreMenu()" class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium flex items-center gap-1">
                            {{ __('messages.more') }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </nav>
                </div>
                
                <!-- Right Side - Auth, Cart, Settings -->
                <div class="flex items-center space-x-4">
                    <!-- Language Switcher -->
                    <button onclick="toggleLanguage()" class="flex items-center space-x-2 text-gray-700 dark:text-gray-300 hover:text-primary dark:hover:text-blue-400 transition p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 font-semibold" title="{{ __('messages.change_language') }}">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.493 13.918a2 2 0 110.868 3.815c-.121.362-.073.737.127 1.025A5.988 5.988 0 0020 16a5.988 5.988 0 00-9.512-5.418c.2.288.248.663.127 1.025a2 2 0 01-.868 3.815l-.102-.01A7.995 7.995 0 0110 5a7.993 7.993 0 00-6.84 3.918A7.993 7.993 0 005.493 13.918zm6.014-6.014a2 2 0 110.868 3.815c-.121.362-.073.737.127 1.025A5.988 5.988 0 0020 10a5.988 5.988 0 00-9.512-5.418c.2.288.248.663.127 1.025a2 2 0 01-.868 3.815l-.102-.01A7.995 7.995 0 0110 1a7.993 7.993 0 00-6.84 3.918A7.993 7.993 0 005.493 7.918z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="hidden sm:inline text-sm">{{ app()->getLocale() === 'en' ? '🇬🇧 EN' : '🇸🇦 AR' }}</span>
                    </button>
                    
                    <!-- Theme Toggler -->
                    <button onclick="toggleTheme()" class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition" title="{{ __('messages.toggle_theme') }}">
                        <svg class="dark:hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg class="hidden dark:block w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.707.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm5.657-9.193a1 1 0 00-1.414 1.414l.707.707a1 1 0 001.414-1.414l-.707-.707zM5 8a1 1 0 100-2H4a1 1 0 100 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    
                    <!-- Auth Links / User Profile -->
                    @if(auth()->check())
                        <a href="{{ route('profile.show') }}" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            @if(auth()->user()->profile_image)
                                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="hidden sm:inline text-sm font-medium">{{ auth()->user()->name }}</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-primary transition px-3 py-2 font-medium">
                            {{ __('messages.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition font-medium">
                            {{ __('messages.sign_up') }}
                        </a>
                    @endif
                    
                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        @if(auth()->check())
                            <span class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center shadow-lg">
                                {{ auth()->user()->cartItems()->count() }}
                            </span>
                        @endif
                    </a>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- More Menu Sidebar Overlay -->
    <div id="more-menu-overlay" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 transition-opacity opacity-0" id="more-menu-backdrop" onclick="toggleMoreMenu()"></div>
        
        <!-- Sidebar -->
        <div class="absolute right-0 top-0 h-full w-80 bg-white dark:bg-gray-800 shadow-2xl transform transition-transform translate-x-full" id="more-menu-sidebar">
            <div class="flex flex-col h-full">
                <!-- Header -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('messages.more') }}</h2>
                    <button onclick="toggleMoreMenu()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Links -->
                <div class="flex-1 overflow-y-auto p-6 space-y-2">
                    @foreach(\App\Helpers\NavigationHelper::getStaticPagesForNavigation() as $page)
                        <a href="{{ route('static-pages.show', $page->slug) }}" class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 hover:text-primary transition group">
                            <span class="bg-gray-100 dark:bg-gray-700 group-hover:bg-white dark:group-hover:bg-gray-600 p-2 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </span>
                            <span class="font-medium">{{ $page->title }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-gray-300 mt-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">{{ $siteSetting->site_name ?? 'EcommStore' }}</h3>
                    <p class="text-sm text-gray-400 mb-4">{{ $siteSetting->site_description ?? __('messages.trusted_platform') }}</p>
                    
                    <div class="space-y-3">
                        @if($siteSetting->site_address)
                            <a href="{{ $siteSetting->site_address_map_url ?? 'https://maps.google.com/?q=' . urlencode($siteSetting->site_address) }}" target="_blank" class="flex items-start space-x-3 text-sm text-gray-400 hover:text-primary transition group">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $siteSetting->site_address }}</span>
                            </a>
                        @endif
                        
                        @if($siteSetting->site_email)
                            <a href="mailto:{{ $siteSetting->site_email }}" class="flex items-center space-x-3 text-sm text-gray-400 hover:text-primary transition group">
                                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $siteSetting->site_email }}</span>
                            </a>
                        @endif

                        @if($siteSetting->site_phone)
                            <a href="tel:{{ $siteSetting->site_phone }}" class="flex items-center space-x-3 text-sm text-gray-400 hover:text-primary transition group">
                                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>{{ $siteSetting->site_phone }}</span>
                            </a>
                        @endif
                    </div>

                    <!-- Social Media Links -->
                    <div class="flex space-x-4 mt-6">
                        @if($siteSetting->facebook_url)
                            <a href="{{ $siteSetting->facebook_url }}" target="_blank" class="text-gray-400 hover:text-blue-500 transition" title="Facebook">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        @endif
                        @if($siteSetting->twitter_url)
                            <a href="{{ $siteSetting->twitter_url }}" target="_blank" class="text-gray-400 hover:text-blue-400 transition" title="Twitter">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 00-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                        @endif
                        @if($siteSetting->instagram_url)
                            <a href="{{ $siteSetting->instagram_url }}" target="_blank" class="text-gray-400 hover:text-pink-500 transition" title="Instagram">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.204-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.069-4.85.069-3.204 0-3.584-.012-4.849-.069-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.072 4.849-.072zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        @endif
                        @if($siteSetting->linkedin_url)
                            <a href="{{ $siteSetting->linkedin_url }}" target="_blank" class="text-gray-400 hover:text-blue-600 transition" title="LinkedIn">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
                
                <!-- Quick Links (unchanged) -->
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('messages.quick_links') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-primary transition">{{ __('messages.home') }}</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-primary transition">{{ __('messages.products') }}</a></li>
                        <li><a href="{{ route('categories.index') }}" class="hover:text-primary transition">{{ __('messages.categories') }}</a></li>
                        
                        <!-- Static Pages in Footer -->
                        @foreach(\App\Helpers\NavigationHelper::getStaticPagesForNavigation() as $page)
                            <li><a href="{{ route('static-pages.show', $page->slug) }}" class="hover:text-primary transition">{{ $page->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Customer Service (unchanged) -->
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('messages.customer_service') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-primary transition">{{ __('messages.contact_us') }}</a></li>
                        <li><a href="#" class="hover:text-primary transition">{{ __('messages.shipping_info') }}</a></li>
                        <li><a href="#" class="hover:text-primary transition">{{ __('messages.returns') }}</a></li>
                        <li><a href="#" class="hover:text-primary transition">{{ __('messages.faqs') }}</a></li>
                    </ul>
                </div>
                
                <!-- Newsletter (unchanged) -->
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('messages.newsletter') }}</h4>
                    <p class="text-sm text-gray-400 mb-3">{{ __('messages.subscribe_exclusive') }}</p>
                    <form class="flex flex-col space-y-2">
                        <input type="email" placeholder="{{ __('messages.your_email') }}" class="px-4 py-2 rounded bg-gray-800 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary">
                        <button type="submit" class="bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded transition">{{ __('messages.subscribe') }}</button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-sm text-gray-400">© {{ date('Y') }} {{ $siteSetting->site_name ?? 'EcommStore' }}. {{ __('messages.all_rights_reserved') }}</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <!-- Translatable Legal Links -->
                        <a href="{{ route('static-pages.show', 'about') }}" class="text-gray-400 hover:text-primary transition">{{ __('messages.about_us') }}</a>
                        <a href="{{ route('static-pages.show', 'contact') }}" class="text-gray-400 hover:text-primary transition">{{ __('messages.contact_us') }}</a>
                        <a href="{{ route('static-pages.show', 'privacy-policy') }}" class="text-gray-400 hover:text-primary transition">{{ __('messages.privacy_policy') }}</a>
                        <a href="{{ route('static-pages.show', 'terms-of-service') }}" class="text-gray-400 hover:text-primary transition">{{ __('messages.terms_conditions') }}</a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">{{ __('messages.cookie_policy') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scripts -->
    <script>
        // Initialize app state from server
        const appState = {
            currentTheme: localStorage.getItem('theme') || '{{ session('theme', 'light') }}',
            currentLocale: '{{ app()->getLocale() }}',
            isDarkMode: function() {
                return this.currentTheme === 'dark';
            }
        };
        
        // Theme Toggle Function - Updates immediately
        function toggleTheme() {
            const html = document.documentElement;
            const newTheme = appState.currentTheme === 'dark' ? 'light' : 'dark';
            
            // Update state immediately
            appState.currentTheme = newTheme;
            
            // Update DOM immediately - This is the key to making it work
            html.classList.toggle('dark');
            
            // Ensure the correct state
            if (newTheme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
            
            // Save to localStorage immediately (client-side persistence)
            localStorage.setItem('theme', newTheme);
            
            // Save to server asynchronously (no need to wait)
            fetch('{{ route("set-theme") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ theme: newTheme })
            })
            .then(response => response.json())
            .catch(error => console.error('Theme update error:', error));
        }
        
        // Language Switch Function
        function switchLanguage(locale) {
            if (locale === appState.currentLocale) {
                return; // Already on this language
            }
            
            // Save theme preference before redirecting
            localStorage.setItem('theme', appState.currentTheme);
            
            // Navigate to locale route using absolute path with leading slash
            window.location.href = '/' + locale;
        }
        
        // Toggle Language Function (switches between EN and AR)
        function toggleLanguage() {
            const currentLocale = appState.currentLocale;
            const newLocale = currentLocale === 'en' ? 'ar' : 'en';
            switchLanguage(newLocale);
        }
        
        // Initialize theme on page load
        function initTheme() {
            const html = document.documentElement;
            const savedTheme = localStorage.getItem('theme') || '{{ session('theme', 'light') }}';
            
            // Update state
            appState.currentTheme = savedTheme;
            
            // Apply to DOM
            if (savedTheme === 'dark') {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
        }
        
        // Initialize on page load (already done in HEAD, but ensure consistency)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTheme);
        } else {
            initTheme();
        }
        
        // Global Toast Notification Function
        function showToast(message, type = 'success') {
            // Create toast element
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? '✓' : '✕';
            
            toast.innerHTML = `
                <div class="${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
                    <span class="text-xl font-bold">${icon}</span>
                    <span>${message}</span>
                </div>
            `;
            
            toast.className = 'toast-notification fixed top-4 right-4 z-50 animate-slide-in';
            
            // Add unique identifier for proper cleanup
            const toastId = 'toast-' + Date.now() + '-' + Math.floor(Math.random() * 1000);
            toast.setAttribute('data-toast-id', toastId);
            
            document.body.appendChild(toast);
            
            // Auto-remove after 4 seconds
            const timeoutId = setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 4000);
            
            // Store timeout ID on the element for potential cleanup
            toast.timeoutId = timeoutId;
        }
        
        // Global function to update cart count
        function updateCartCount(count) {
            const cartCountElement = document.querySelector('.cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = count;
            }
        }

        // Check for session messages on load
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            
            @if(session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
            
            @if($errors->any())
                @foreach($errors->all() as $error)
                    showToast("{{ $error }}", 'error');
                @endforeach
            @endif
        });
    </script>
    
    <script>
        function toggleMoreMenu() {
            const overlay = document.getElementById('more-menu-overlay');
            const sidebar = document.getElementById('more-menu-sidebar');
            const backdrop = document.getElementById('more-menu-backdrop');
            
            if (overlay.classList.contains('hidden')) {
                // Open
                overlay.classList.remove('hidden');
                // Trigger reflow
                void overlay.offsetWidth;
                
                backdrop.classList.remove('opacity-0');
                sidebar.classList.remove('translate-x-full');
                
                // Prevent body scroll
                document.body.style.overflow = 'hidden';
            } else {
                // Close
                backdrop.classList.add('opacity-0');
                sidebar.classList.add('translate-x-full');
                
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300); // Match transition duration
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
