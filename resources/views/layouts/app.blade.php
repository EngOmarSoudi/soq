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
<body class="antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-white transition-colors duration-300">
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
                        <a href="{{ route('home') }}" class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium">
                            {{ __('messages.home') }}
                        </a>
                        <!-- Products link - always goes to products page -->
                        <a href="{{ route('products.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium">
                            {{ __('messages.products') }}
                        </a>
                        <!-- Categories link - goes to categories browsing page -->
                        <a href="{{ route('categories.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium">
                            {{ __('messages.categories') }}
                        </a>
                        
                        <!-- Static Pages Navigation -->
                        @foreach(\App\Helpers\NavigationHelper::getStaticPagesForNavigation() as $page)
                            <a href="{{ route('static-pages.show', $page->slug) }}" class="text-gray-700 dark:text-gray-300 hover:text-primary transition font-medium">
                                {{ $page->title }}
                            </a>
                        @endforeach
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
    
    <!-- Footer -->
    <footer class="bg-gray-900 dark:bg-gray-950 text-gray-300 mt-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">EcommStore</h3>
                    <p class="text-sm text-gray-400">{{ __('messages.trusted_platform') }}</p>
                </div>
                
                <!-- Quick Links -->
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
                
                <!-- Customer Service -->
                <div>
                    <h4 class="text-white font-semibold mb-4">{{ __('messages.customer_service') }}</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-primary transition">{{ __('messages.contact_us') }}</a></li>
                        <li><a href="#" class="hover:text-primary transition">{{ __('messages.shipping_info') }}</a></li>
                        <li><a href="#" class="hover:text-primary transition">{{ __('messages.returns') }}</a></li>
                        <li><a href="#" class="hover:text-primary transition">{{ __('messages.faqs') }}</a></li>
                    </ul>
                </div>
                
                <!-- Newsletter -->
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
                    <p class="text-sm text-gray-400">© 2025 EcommStore. {{ __('messages.all_rights_reserved') }}</p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <!-- Static Pages Legal Links -->
                        @foreach(\App\Helpers\NavigationHelper::getStaticPagesForNavigation() as $page)
                            @if(in_array($page->slug, ['privacy-policy', 'terms-of-service']))
                                <a href="{{ route('static-pages.show', $page->slug) }}" class="text-gray-400 hover:text-primary transition">{{ $page->title }}</a>
                            @endif
                        @endforeach
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
    </script>
    
    @stack('scripts')
</body>
</html>