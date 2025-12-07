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
                            <input type="text" name="street" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
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
                            <button type="button" onclick="getCurrentLocation()" class="mt-2 text-sm text-primary hover:text-primary-dark flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ __('messages.use_current_location') }}
                            </button>
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
                <label class="block p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary transition mb-4">
                    <div class="flex items-start">
                        <input type="radio" name="payment_method" value="credit_card" class="mt-1 w-4 h-4 text-primary" required>
                        <div class="ml-3 flex items-start flex-1">
                            <div class="mr-3 mt-1">
                                <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold">{{ __('messages.credit_card') }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.pay_with_credit_card') }}</p>
                                
                                <!-- CC Form -->
                                <div id="creditCardForm" class="hidden mt-4 space-y-4 border-t pt-4 border-gray-100 dark:border-gray-700">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">{{ __('messages.card_number') }}</label>
                                        <input type="text" name="card_number" placeholder="4242 4242 4242 4242" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium mb-1">{{ __('messages.cardholder_name') }}</label>
                                        <input type="text" name="cardholder_name" placeholder="John Doe" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium mb-1">{{ __('messages.expiry_date') }}</label>
                                            <div class="flex gap-2">
                                                <input type="text" name="expiry_month" placeholder="MM" maxlength="2" class="w-1/2 px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                                                <input type="text" name="expiry_year" placeholder="YY" maxlength="2" class="w-1/2 px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium mb-1">{{ __('messages.cvv') }}</label>
                                            <input type="text" name="cvv" placeholder="123" maxlength="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </label>
                
                <label class="flex items-start p-4 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:border-primary transition">
                    <input type="radio" name="payment_method" value="bank_transfer" class="mt-1 w-4 h-4 text-primary" required>
                    <div class="ml-3 flex items-start">
                        <div class="mr-3 mt-1">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">{{ __('messages.bank_transfer') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('messages.transfer_funds_bank') }}</p>
                        </div>
                    </div>
                </label>
                
                <!-- Bank Transfer Receipt Upload (Hidden by default) -->
                <div id="receiptUploadSection" class="hidden mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <!-- Bank Details -->
                    @if(isset($bankAccounts) && $bankAccounts->count() > 0)
                        <div class="mb-4 space-y-3">
                            <h4 class="font-semibold text-sm text-gray-700 dark:text-gray-300">{{ __('messages.payment_instructions') }}</h4>
                            @foreach($bankAccounts as $account)
                                <div class="bg-white dark:bg-gray-600 p-3 rounded border border-gray-200 dark:border-gray-500 text-sm">
                                    <div class="grid grid-cols-1 gap-1">
                                        <div class="font-bold">{{ $account->bank_name }}</div>
                                        <div>{{ __('messages.account_name') }}: {{ $account->account_name }}</div>
                                        <div>{{ __('messages.account_number') }}: {{ $account->account_number }}</div>
                                        @if($account->routing_number)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.routing_number') }}: {{ $account->routing_number }}</div>
                                        @endif
                                        @if($account->swift_code)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.swift_code') }}: {{ $account->swift_code }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                                {{ __('messages.amount') }}: SAR <span id="bankTransferAmount">{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    @else
                         <div class="mb-4 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-200 p-3 rounded text-sm">
                            No active bank accounts found. Please contact support.
                         </div>
                    @endif

                    <label class="block text-sm font-medium mb-2">{{ __('messages.upload_transfer_receipt') }}</label>
                    <input type="file" name="payment_receipt" accept="image/*" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg focus:ring-2 focus:ring-primary">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.receipt_upload_help') }}</p>
                </div>
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
                            <p class="font-medium">{{ is_array($item->product->name) ? (app()->getLocale() === 'en' ? ($item->product->name['en'] ?? '') : ($item->product->name['ar'] ?? '')) : $item->product->name }}</p>                            <p class="text-gray-600 dark:text-gray-400">{{ __('messages.qty') }}: {{ $item->quantity }}</p>
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
                
                // Show/hide receipt upload section based on payment method
                const receiptSection = document.getElementById('receiptUploadSection');
                const ccForm = document.getElementById('creditCardForm');
                
                if (this.value === 'bank_transfer') {
                    receiptSection.classList.remove('hidden');
                    ccForm.classList.add('hidden');
                } else if (this.value === 'credit_card') {
                    receiptSection.classList.add('hidden');
                    ccForm.classList.remove('hidden');
                } else {
                    receiptSection.classList.add('hidden');
                    ccForm.classList.add('hidden');
                }
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
            
            // If bank transfer is selected, check if receipt is uploaded
            if (paymentMethod === 'bank_transfer') {
                const receiptInput = document.querySelector('input[name="payment_receipt"]');
                if (receiptInput && receiptInput.files.length > 0) {
                    // Add receipt file to form data
                    const formData = new FormData(this);
                    formData.append('payment_receipt', receiptInput.files[0]);
                    
                    // Submit with file
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => {
                        // Check if response is JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            // If not JSON, redirect to success page
                            window.location.href = '{{ route("order.confirmation", [":orderId"]) }}'.replace(':orderId', response.url.split('/').pop());
                            return;
                        }
                    })
                    .then(data => {
                        if (data && data.success) {
                            window.location.href = data.redirect || '{{ route("order.confirmation", [":orderId"]) }}'.replace(':orderId', data.order_id || '');
                        } else {
                            showToast(data.message || '{{ __("messages.error_occurred") }}', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('{{ __("messages.error_occurred") }}', 'error');
                    });
                    return;
                }
            }
            
            // Submit the form normally
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
            
            // Reverse geocode to get address details
            geocodePosition(e.latlng.lat, e.latlng.lng);
        });
    }

    function geocodePosition(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.address) {
                    const addr = data.address;
                    
                    // Populate fields
                    const streetInput = document.querySelector('input[name="street"]');
                    const cityInput = document.querySelector('input[name="city"]');
                    const countryInput = document.querySelector('input[name="country"]');
                    const postalCodeInput = document.querySelector('input[name="postal_code"]');
                    
                    if (countryInput) countryInput.value = addr.country || '';
                    if (cityInput) cityInput.value = addr.city || addr.town || addr.village || addr.county || '';
                    if (postalCodeInput) postalCodeInput.value = addr.postcode || '';
                    
                    // Construct street address
                    let street = addr.road || addr.pedestrian || '';
                    if (addr.house_number) street = addr.house_number + ' ' + street;
                    if (addr.suburb && street) street += ', ' + addr.suburb;
                    else if (addr.suburb) street = addr.suburb;
                    
                    if (streetInput) streetInput.value = street;
                }
            })
            .catch(error => console.error('Geocoding error:', error));
    }

    function getCurrentLocation() {
        if (!navigator.geolocation) {
            showToast('Geolocation is not supported by your browser', 'error');
            return;
        }

        const btn = document.querySelector('button[onclick="getCurrentLocation()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="animate-pulse">Locating...</span>';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(position => {
            const { latitude, longitude } = position.coords;
            
            // Move map to location
            if (map) {
                map.setView([latitude, longitude], 16);
                
                // Add/Move marker
                if (marker) {
                    marker.setLatLng([latitude, longitude]);
                } else {
                    marker = L.marker([latitude, longitude]).addTo(map);
                }
            }
            
            // Update inputs
            document.getElementById('latitude').value = latitude;
            document.getElementById('longitude').value = longitude;
            
            // Geocode
            geocodePosition(latitude, longitude);
            
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, error => {
            console.error('Geolocation error:', error);
            showToast('Unable to retrieve location: ' + error.message, 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
    
    // Handle address form submission
    document.getElementById('addressFormElement').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("profile.address.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
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