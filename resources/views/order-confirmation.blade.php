@extends('layouts.app')

@section('title', __('messages.order_confirmation'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 text-center">
            <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.thank_you_order') }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.order_received_email') }}</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4 print:hidden">
                <div class="flex gap-2">
                    <button onclick="printReceipt('large')" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-black transition flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        {{ __('messages.print_a4') }}
                    </button>
                    <button onclick="printReceipt('thermal')" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition flex items-center gap-2 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l4 4a1 1 0 01.586 1.414V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ __('messages.print_thermal') }}
                    </button>
                </div>
                <a href="{{ route('products.index') }}" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-600 transition">
                    {{ __('messages.continue_shopping') }}
                </a>
                <a href="{{ route('profile.orders') }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ __('messages.view_my_orders') }}
                </a>
            </div>
        </div>

        <div id="order-receipt" class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('messages.order_details') }}</h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">#{{ $order->order_number }}</span>
            </div>
            
            <div class="p-6">
                <!-- Order Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('messages.order_info') }}</h3>
                        <p class="text-gray-900 dark:text-white"><span class="font-medium">{{ __('messages.date') }}:</span> {{ $order->created_at->format('M d, Y') }}</p>
                        <p class="text-gray-900 dark:text-white"><span class="font-medium">{{ __('messages.payment_method') }}:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                        <p class="text-gray-900 dark:text-white">
                            <span class="font-medium">{{ __('messages.payment_status') }}:</span> 
                            <span class="{{ $order->payment_status === 'paid' ? 'text-green-500' : 'text-yellow-500' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">{{ __('messages.shipping_address') }}</h3>
                        <p class="text-gray-900 dark:text-white">{{ $order->shippingAddress->address_line1 }}</p>
                        @if($order->shippingAddress->address_line2)
                            <p class="text-gray-900 dark:text-white">{{ $order->shippingAddress->address_line2 }}</p>
                        @endif
                        <p class="text-gray-900 dark:text-white">{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                        <p class="text-gray-900 dark:text-white">{{ $order->shippingAddress->country }}</p>
                        <p class="text-gray-900 dark:text-white">{{ $order->shippingAddress->phone_number }}</p>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('messages.items') }}</h3>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-md overflow-hidden">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name['en'] ?? $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white max-w-xs truncate">
                                            {{ $item->product ? ($item->product->name[app()->getLocale()] ?? $item->product->name['en'] ?? $item->product->name) : 'Product Unavailable' }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ __('messages.quantity') }}: {{ $item->quantity }}
                                            @if($item->color) | {{ __('messages.color') }}: {{ $item->color }} @endif
                                            @if($item->size) | {{ __('messages.size') }}: {{ $item->size }} @endif
                                        </p>
                                    </div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ number_format($item->total_price, 2) }} SAR
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Totals -->
                <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6">
                    <div class="w-full md:w-1/2 ml-auto space-y-2">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>{{ __('messages.subtotal') }}</span>
                            <span>{{ number_format($order->subtotal, 2) }} SAR</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>{{ __('messages.shipping') }}</span>
                            <span>{{ number_format($order->shipping_cost, 2) }} SAR</span>
                        </div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>{{ __('messages.tax') }}</span>
                            <span>{{ number_format($order->tax_amount, 2) }} SAR</span>
                        </div>
                        @if($order->discount_amount > 0)
                            <div class="flex justify-between text-green-600 dark:text-green-400">
                                <span>{{ __('messages.discount') }}</span>
                                <span>-{{ number_format($order->discount_amount, 2) }} SAR</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span>{{ __('messages.total') }}</span>
                            <span>{{ number_format($order->total_amount, 2) }} SAR</span>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer Instructions -->
                @if($order->payment_method === 'bank_transfer' && $bankAccounts->count() > 0)
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-6 print:hidden">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('messages.bank_transfer_details') }}</h3>
                        <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ __('messages.please_transfer_to') }}:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($bankAccounts as $account)
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded border border-gray-200 dark:border-gray-700">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $account->bank_name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.account_name') }}: {{ $account->account_name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.account_number') }}: {{ $account->account_number }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.iban') }}: {{ $account->iban }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function printReceipt(type) {
        const receipt = document.getElementById('order-receipt');
        const styleId = 'print-page-size';
        let style = document.getElementById(styleId);
        
        if (!style) {
            style = document.createElement('style');
            style.id = styleId;
            document.head.appendChild(style);
        }
        
        // Reset classes
        receipt.classList.remove('thermal-mode');
        
        if (type === 'thermal') {
            receipt.classList.add('thermal-mode');
            // Force 80mm paper size
            style.innerHTML = '@page { size: 80mm auto; margin: 0mm; }';
        } else {
            // Reset to default (likely A4)
            style.innerHTML = '';
        }
        
        setTimeout(() => {
            window.print();
        }, 100);
    }
</script>
@endpush

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #order-receipt, #order-receipt * {
            visibility: visible;
        }
        #order-receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 20px;
            box-shadow: none;
            background: white;
            color: black;
        }
        
        /* Thermal Mode Specific Styles */
        #order-receipt.thermal-mode {
            padding: 5px;
            width: 78mm; /* Slightly less than 80mm to prevent overflow */
            max-width: 78mm;
            margin: 0 auto; /* Center on page if possible */
            font-size: 11px;
            box-sizing: border-box;
        }
        
        #order-receipt.thermal-mode img {
            display: none !important;
        }
        
        #order-receipt.thermal-mode .grid {
            display: block !important;
        }
        
        #order-receipt.thermal-mode .grid > div {
            margin-bottom: 20px;
        }
        
        #order-receipt.thermal-mode h1, 
        #order-receipt.thermal-mode h2, 
        #order-receipt.thermal-mode h3 {
            font-size: 16px !important;
            margin-bottom: 5px;
        }
        
        #order-receipt.thermal-mode .flex.items-center.justify-between {
            margin-bottom: 5px;
        }
        
        #order-receipt.thermal-mode .w-full.md\:w-1\/2.ml-auto {
            width: 100% !important;
            margin-left: 0 !important;
        }
        
        /* Hide non-printable elements */
        .print\:hidden {
            display: none !important;
        }
        
        /* Ensure dark mode colors are reset for printing */
        .dark\:bg-gray-800 {
            background-color: white !important;
        }
        .dark\:text-white {
            color: black !important;
        }
        .dark\:bg-gray-900 {
            background-color: #f9fafb !important; /* gray-50 */
        }
        .dark\:border-gray-700 {
            border-color: #e5e7eb !important; /* gray-200 */
        }
        
        /* Reset containers */
        .container {
            max-width: 100% !important;
            padding: 0 !important;
        }
    }
</style>
@endpush
