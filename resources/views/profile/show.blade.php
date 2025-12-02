@extends('layouts.page')

@section('title', __('messages.profile') . ' - EcommStore')

@section('page-title', __('messages.profile'))

@section('page-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Profile Info -->
    <div class="lg:col-span-2">
        <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 mb-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">{{ __('messages.account_information') }}</h2>
            
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.name') }}</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.email_address') }}</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.phone_number') }}</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <button type="submit" class="btn-primary text-white font-semibold py-3 px-6 rounded-lg transition">
                            {{ __('messages.update') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div>
        <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ __('messages.settings') }}</h2>
            
            <nav class="space-y-2">
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 bg-primary text-white rounded-lg">
                    {{ __('messages.profile') }}
                </a>
                <a href="{{ route('profile.orders') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    {{ __('messages.orders') }}
                </a>
                <a href="{{ route('profile.addresses') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    {{ __('messages.addresses') }}
                </a>
                <a href="{{ route('wishlist.index') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    {{ __('messages.my_wishlist') }}
                </a>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                        {{ __('messages.logout') }}
                    </button>
                </form>
            </nav>
        </div>
    </div>
</div>
@endsection