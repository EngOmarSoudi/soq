@extends('layouts.page')

@section('title', __('messages.checkout'))

@section('page-title', __('messages.checkout'))

@section('page-description', __('messages.complete_purchase'))

@section('page-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <!-- Shipping Address Selection -->
        <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4">{{ __('messages.shipping_address') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4" id="addressSelection">
                @foreach(auth()->user()->addresses as $address)
                    <label class="flex items-start p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary transition address-option" data-address-id="{{ $address->id }}">
                        <input type="radio" name="shipping_address_id" value="{{ $address->id }}" class="mt-1 w-4 h-4 text-primary address-radio" {{ $loop->first ? 'checked' : '' }} required>
                        <div class="ml-3 flex-1">
                            <p class="font-semibold">{{ $address->label ?? 'Address' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $address->street_address }}, {{ $address->city }}, {{ $address->country }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-500">{{ $address->postal_code }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
            
            <button type="button" id="addNewAddressBtn" class="w-full py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg text-gray-500 dark:text-gray-400 hover:border-primary hover:text-primary transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                {{ __('messages.add_new_address') }}
            </button>
            
            <!-- Add New Address Form (Hidden by default) -->
            <div id="mapForm" class="mt-6 hidden">
                <h3 class="text-xl font-bold mb-4">{{ __('messages.add_new_address') }}</h3>
                
                <form id="addressFormElement">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('messages.name') }}</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('messages.phone') }}</label>
                            <input type="text" name="phone" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('messages.country') }}</label>
                            <input type="text" name="country" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('messages.city') }}</label>
                            <input type="text" name="city" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">{{ __('messages.street_address') }}</label>
                            <input type="text" name="street_address" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('messages.postal_code') }}</label>
                            <input type="text" name="postal_code" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('messages.label') }} ({{ __('messages.optional') }})</label>
                            <input type="text" name="label" placeholder="{{ __('messages.home_work_other') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">{{ __('messages.location') }} ({{ __('messages.click_map_select') }})</label>
                            <div id="map" class="h-64 rounded-lg border border-gray-300 dark:border-gray-600"></div>
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" id="cancelAddressBtn" class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="flex-1 btn-primary text-white font-bold py-2 px-4 rounded-lg transition">
                            {{ __('messages.save_address') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Payment Method -->
        <div class="card bg-white dark:bg-gray-800 rounded-xl p-6">
            <h2 class="text-2xl font-bold mb-4">{{ __('messages.payment_method') }}</h2>
            
            <div class="space-y-4">
                <label class="flex items-start p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary transition">
                    <input type="radio" name="payment_method" value="credit_card" class="mt-1 w-4 h-4 text-primary" required>
                    <div class="ml-3">
                        <p class="font-semibold">{{ __('messages.credit_card') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.pay_with_credit_card') }}</p>
                    </div>
                </label>
                
                <label class="flex items-start p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary transition">
                    <input type="radio" name="payment_method" value="bank_transfer" class="mt-1 w-4 h-4 text-primary" required>
                    <div class="ml-3">
                        <p class="font-semibold">{{ __('messages.bank_transfer') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.transfer_funds_bank') }}</p>
                    </div>
                </label>
            </div>
        </div>
    </div>
    
    <div>
        <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 sticky top-6">
            <h2 class="text-xl font-bold mb-4">{{ __('messages.order_summary') }}</h2>
            
            <div class="space-y-4 mb-6">
                @foreach($cartItems as $item)
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ $item->product->name[app()->getLocale()] ?? $item->product->name['en'] }}</p>
                            <p class="text-gray-600 dark:text-gray-400">{{ __('messages.qty') }}: {{ $item->quantity }}</p>
                        </div>
                        <p class="font-semibold">SAR {{ number_format($item->price * $item->quantity, 2) }}</p>
                    </div>
                @endforeach
            </div>
            
            <!-- Totals -->
            <div class="space-y-3 border-t border-gray-200 dark:border-gray-600 pt-4">
                <div class="flex justify-between">
                    <span>{{ __('messages.subtotal') }}</span>
                    <span class="font-semibold">SAR {{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.shipping') }}</span>
                    <span class="font-semibold">SAR <span id="shippingCostDisplay">{{ number_format($shippingCost ?? 10.00, 2) }}</span></span>
                </div>
                <div class="flex justify-between">
                    <span>{{ __('messages.tax') }}</span>
                    <span class="font-semibold">SAR {{ number_format($taxAmount, 2) }}</span>
                </div>
                @if($coupon)
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <span>{{ __('messages.discount') }} ({{ $coupon->code }})</span>
                        <span class="font-semibold">- SAR {{ number_format($discountAmount, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200 dark:border-gray-600">
                    <span>{{ __('messages.total') }}</span>
                    <span>SAR <span id="totalAmountDisplay">{{ number_format($total, 2) }}</span></span>
                </div>
            </div>
            
            <!-- Place Order Form -->
            <form id="placeOrderForm" method="POST" action="{{ route('order.place') }}" class="mt-6" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="shipping_address_id" id="selectedAddressId">
                <input type="hidden" name="payment_method" id="selectedPaymentMethod">
                
                <button type="submit" class="w-full btn-primary text-white font-bold py-3 px-4 rounded-lg transition">
                    {{ __('messages.place_order') }}
                </button>
            </form>
        </div>
    </div>
</div>

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    let map, marker;
    
    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeMap();
        
        // Handle address selection
        document.querySelectorAll('.address-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const addressId = this.value;
                document.getElementById('selectedAddressId').value = addressId;
            });
        });
        
        // Handle payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('selectedPaymentMethod').value = this.value;
            });
        });
        
        // Set initial values
        const firstAddressRadio = document.querySelector('.address-radio:checked');
        if (firstAddressRadio) {
            document.getElementById('selectedAddressId').value = firstAddressRadio.value;
        }
        
        const firstPaymentRadio = document.querySelector('input[name="payment_method"]:checked');
        if (firstPaymentRadio) {
            document.getElementById('selectedPaymentMethod').value = firstPaymentRadio.value;
        }
        
        // Add new address button
        document.getElementById('addNewAddressBtn').addEventListener('click', function() {
            document.getElementById('mapForm').classList.remove('hidden');
            this.classList.add('hidden');
        });
        
        // Cancel address button
        document.getElementById('cancelAddressBtn').addEventListener('click', function() {
            document.getElementById('mapForm').classList.add('hidden');
            document.getElementById('addNewAddressBtn').classList.remove('hidden');
        });
        
        // Handle place order form submission
        document.getElementById('placeOrderForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const shippingAddressId = document.getElementById('selectedAddressId').value;
            const paymentMethod = document.getElementById('selectedPaymentMethod').value;
            
            if (!shippingAddressId) {
                showToast('{{ __("messages.select_shipping_address") }}', 'error');
                return;
            }
            
            if (!paymentMethod) {
                showToast('{{ __("messages.select_payment_method") }}', 'error');
                return;
            }
            
            // Submit the form
            this.submit();
        });
    });
    
    function initializeMap() {
        // Initialize map centered on Riyadh, Saudi Arabia
        map = L.map('map').setView([24.7136, 46.6753], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add click event to map
        map.on('click', function(e) {
            // Remove existing marker if present
            if (marker) {
                map.removeLayer(marker);
            }
            
            // Add new marker
            marker = L.marker(e.latlng).addTo(map);
            
            // Update hidden inputs
            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
        });
    }
    
    // Handle address form submission
    document.getElementById('addressFormElement').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("address.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the address selection area
                location.reload();
            } else {
                showToast(data.message || '{{ __("messages.error_occurred") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('{{ __("messages.error_occurred") }}', 'error');
        });
    });
</script>
@endpush
@endsection