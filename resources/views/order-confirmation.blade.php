@extends('layouts.page')

@section('title', __('messages.order_confirmation') . ' - EcommStore')

@section('page-title', __('messages.order_confirmation'))

@section('page-content')
<div class="max-w-2xl mx-auto text-center mb-12">
    <div class="mb-6">
        <svg class="w-20 h-20 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <h1 class="text-4xl font-bold text-green-600 dark:text-green-400 mb-4">{{ __('messages.order_confirmed') }}</h1>
    <p class="text-gray-600 dark:text-gray-400 text-lg">{{ __('messages.thank_you_purchase') }}</p>
</div>

<div class="max-w-2xl mx-auto card bg-white dark:bg-gray-800 rounded-xl p-8 mb-8">
    <!-- Order Details -->
    <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-600">
        <h2 class="text-2xl font-bold mb-4">{{ __('messages.order_details') }}</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.order_number') }}</p>
                <p class="font-semibold">{{ $order->order_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.order_date') }}</p>
                <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.status') }}</p>
                <p class="font-semibold">
                    <span class="px-3 py-1 rounded-full text-xs font-bold 
                        @if($order->status === 'pending')
                            bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'processing')
                            bg-blue-100 text-blue-800
                        @elseif($order->status === 'shipped')
                            bg-purple-100 text-purple-800
                        @elseif($order->status === 'delivered')
                            bg-green-100 text-green-800
                        @else
                            bg-red-100 text-red-800
                        @endif
                    ">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.payment_method') }}</p>
                <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
            </div>
        </div>
    </div>
    
    <!-- Items -->
    <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-600">
        <h3 class="text-xl font-bold mb-4">{{ __('messages.items') }}</h3>
        <div class="space-y-4">
            @foreach($order->items as $item)
                <div class="flex justify-between items-center">
                    <div>
                        <p class="font-medium">{{ $item->product->name }}</p>
                        @if($item->color || $item->size)
                            <div class="flex gap-2 mt-1">
                                @if($item->color)
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-200 dark:bg-gray-600 rounded">{{ __('messages.color') }}: {{ $item->color }}</span>
                                @endif
                                @if($item->size)
                                    <span class="inline-block px-2 py-1 text-xs bg-gray-200 dark:bg-gray-600 rounded">{{ __('messages.size') }}: {{ $item->size }}</span>
                                @endif
                            </div>
                        @endif
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.qty') }}: {{ $item->quantity }}</p>
                    </div>
                    <p class="font-semibold">SAR {{ number_format($item->total_price, 2) }}</p>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Totals -->
    <div class="mb-8">
        <div class="space-y-2">
            <div class="flex justify-between">
                <span>{{ __('messages.subtotal') }}</span>
                <span>SAR {{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('messages.shipping') }}</span>
                <span>SAR {{ number_format($order->shipping_cost, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>{{ __('messages.tax') }}</span>
                <span>SAR {{ number_format($order->tax_amount, 2) }}</span>
            </div>
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-green-600">
                    <span>{{ __('messages.discount') }}</span>
                    <span>-SAR {{ number_format($order->discount_amount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-lg font-bold border-t border-gray-200 dark:border-gray-600 pt-2 mt-2">
                <span>{{ __('messages.total') }}</span>
                <span>SAR {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>
    
    <!-- Shipping Address -->
    <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-600">
        <h3 class="text-xl font-bold mb-4">{{ __('messages.shipping_address') }}</h3>
        <p class="font-medium">{{ $order->shippingAddress->name ?? 'N/A' }}</p>
        <p class="text-gray-600 dark:text-gray-400">{{ $order->shippingAddress->street }}</p>
        <p class="text-gray-600 dark:text-gray-400">{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->postal_code }}</p>
        <p class="text-gray-600 dark:text-gray-400">{{ $order->shippingAddress->country }}</p>
    </div>
    
    <!-- Actions -->
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="{{ route('profile.orders') }}" class="flex-1 btn-primary text-white font-bold py-3 px-4 rounded-lg transition text-center">
            {{ __('messages.view_all_orders') }}
        </a>
        <a href="{{ route('home') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-bold py-3 px-4 rounded-lg transition text-center">
            {{ __('messages.continue_shopping') }}
        </a>
    </div>
</div>

<!-- Payment Instructions -->
@if($order->payment_method === 'bank_transfer' && $order->payment_status === 'pending')
    <div class="max-w-2xl mx-auto card bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-6 rounded mb-8">
        <h3 class="text-lg font-bold text-blue-900 dark:text-blue-300 mb-3">{{ __('messages.payment_instructions') }}</h3>
        <p class="text-blue-800 dark:text-blue-200 mb-3">{{ __('messages.please_transfer_amount') }}</p>
        @if($bankAccounts && $bankAccounts->count() > 0)
            <div class="space-y-4 mb-4">
                @foreach($bankAccounts as $account)
                    <div class="bg-white dark:bg-gray-700 p-4 rounded text-sm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div><strong>{{ __('messages.bank_name') }}:</strong> {{ $account->bank_name }}</div>
                            <div><strong>{{ __('messages.account_name') }}:</strong> {{ $account->account_name }}</div>
                            <div><strong>{{ __('messages.account_number') }}:</strong> {{ $account->account_number }}</div>
                            @if($account->routing_number)
                                <div><strong>{{ __('messages.routing_number') }}:</strong> {{ $account->routing_number }}</div>
                            @endif
                            @if($account->swift_code)
                                <div><strong>{{ __('messages.swift_code') }}:</strong> {{ $account->swift_code }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="bg-white dark:bg-gray-700 p-4 rounded mb-4">
                <div><strong>{{ __('messages.amount') }}:</strong> SAR {{ number_format($order->total_amount, 2) }}</div>
                <div><strong>{{ __('messages.reference') }}:</strong> {{ $order->order_number }}</div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-700 p-4 rounded space-y-2 mb-4">
                <p>No active bank accounts found. Please contact support.</p>
                <div><strong>{{ __('messages.amount') }}:</strong> SAR {{ number_format($order->total_amount, 2) }}</div>
                <div><strong>{{ __('messages.reference') }}:</strong> {{ $order->order_number }}</div>
            </div>
        @endif
        
        <!-- Uploaded Receipt Preview -->
        @if($order->payment_reference)
            <div class="mt-4">
                <h4 class="font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.uploaded_receipt') }}</h4>
                <div class="border rounded-lg overflow-hidden">
                    <img src="{{ Storage::url($order->payment_reference) }}" alt="Transfer Receipt" class="w-full max-h-64 object-contain">
                </div>
            </div>
        @endif
    </div>
@endif
@endsection