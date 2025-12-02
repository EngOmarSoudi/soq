
@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-12">
    <div class="max-w-2xl w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-6">{{ __('messages.register') }}</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Basic Information Section -->
            <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('messages.account_information') }}</h3>
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.name') }}</label>
                    <input id="name" type="text" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.email_address') }}</label>
                    <input id="email" type="email" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.phone_number') }}</label>
                    <input id="phone" type="tel" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('phone') border-red-500 @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="tel">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.password') }}</label>
                    <input id="password" type="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('password') border-red-500 @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.confirm_password') }}</label>
                    <input id="password_confirmation" type="password" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <!-- Shipping Address Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('messages.shipping_address') }}</h3>
                
                <div class="mb-4">
                    <label for="address_label" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.address_label') }}</label>
                    <input id="address_label" type="text" placeholder="{{ __('messages.eg_home_office') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('address_label') border-red-500 @enderror" name="address_label" value="{{ old('address_label') }}" required>
                    @error('address_label')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="street_address" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.street_address') }}</label>
                    <input id="street_address" type="text" placeholder="{{ __('messages.street_address_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('street_address') border-red-500 @enderror" name="street_address" value="{{ old('street_address') }}" required>
                    @error('street_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="city" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.city') }}</label>
                        <input id="city" type="text" placeholder="{{ __('messages.city_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('city') border-red-500 @enderror" name="city" value="{{ old('city') }}" required>
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="state" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.state_province') }}</label>
                        <input id="state" type="text" placeholder="{{ __('messages.state_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('state') border-red-500 @enderror" name="state" value="{{ old('state') }}">
                        @error('state')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="postal_code" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.postal_code') }}</label>
                        <input id="postal_code" type="text" placeholder="{{ __('messages.postal_code_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('postal_code') border-red-500 @enderror" name="postal_code" value="{{ old('postal_code') }}" required>
                        @error('postal_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="country" class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">{{ __('messages.country') }}</label>
                    <input id="country" type="text" placeholder="{{ __('messages.country_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('country') border-red-500 @enderror" name="country" value="{{ old('country') }}" required>
                    @error('country')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                {{ __('messages.register') }}
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600 dark:text-gray-400">{{ __('messages.already_have_account') }} <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">{{ __('messages.login') }}</a></p>
        </div>
    </div>
</div>
@endsection
