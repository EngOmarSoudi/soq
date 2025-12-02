@extends('layouts.page')

@section('title', __('messages.my_orders') . ' - EcommStore')

@section('page-title', __('messages.my_orders'))

@section('page-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Orders List -->
    <div class="lg:col-span-2">
        @if($orders->isEmpty())
            <div class="card bg-white dark:bg-gray-800 rounded-xl p-8 text-center max-w-2xl mx-auto">
                <div class="mb-6">
                    <svg class="w-20 h-20 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">{{ __('messages.your_cart_empty') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.explore_thousands') }}</p>
                <a href="{{ route('home') }}" class="inline-block btn-primary text-white font-semibold py-3 px-8 rounded-lg transition">
                    {{ __('messages.continue_shopping') }}
                </a>
            </div>
        @else
            <div class="card bg-white dark:bg-gray-800 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('messages.order_number') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('messages.date') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('messages.total') }}</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900 dark:text-white">{{ __('messages.status') }}</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900 dark:text-white">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                        {{ $order->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                        ${{ number_format($order->total_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($order->status === 'pending')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-100 rounded-full text-sm font-semibold">
                                                {{ __('messages.pending') }}
                                            </span>
                                        @elseif($order->status === 'processing')
                                            <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-100 rounded-full text-sm font-semibold">
                                                {{ __('messages.processing') }}
                                            </span>
                                        @elseif($order->status === 'shipped')
                                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-100 rounded-full text-sm font-semibold">
                                                {{ __('messages.shipped') }}
                                            </span>
                                        @elseif($order->status === 'delivered')
                                            <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100 rounded-full text-sm font-semibold">
                                                {{ __('messages.delivered') }}
                                            </span>
                                        @elseif($order->status === 'cancelled')
                                            <span class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-100 rounded-full text-sm font-semibold">
                                                {{ __('messages.cancelled') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('order.confirmation', $order->id) }}" class="text-primary hover:text-blue-600 dark:hover:text-blue-400 font-semibold text-sm">
                                            {{ __('messages.view_order') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($orders->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
    
    <!-- Sidebar -->
    <div>
        <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ __('messages.settings') }}</h2>
            
            <nav class="space-y-2">
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    {{ __('messages.profile') }}
                </a>
                <a href="{{ route('profile.orders') }}" class="block px-4 py-2 bg-primary text-white rounded-lg">
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