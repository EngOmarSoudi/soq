@extends('layouts.page')

@section('title', __('messages.addresses') . ' - EcommStore')

@section('page-title', __('messages.addresses'))

@section('page-content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Addresses List -->
    <div class="lg:col-span-2">
        @if($addresses->isEmpty())
            <div class="card bg-white dark:bg-gray-800 rounded-xl p-8 text-center">
                <div class="mb-6">
                    <svg class="w-20 h-20 mx-auto text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">{{ __('messages.no_addresses') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('messages.add_new_address_continue') }}</p>
                <button onclick="showAddAddressForm()" class="inline-block btn-primary text-white font-semibold py-3 px-8 rounded-lg transition">
                    {{ __('messages.add_new_address') }}
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                @foreach($addresses as $address)
                    <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 relative">
                        @if($address->is_default)
                            <span class="absolute top-3 right-3 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100 text-xs font-semibold px-2.5 py-0.5 rounded">
                                {{ __('messages.default') }}
                            </span>
                        @endif
                        
                        <h3 class="text-lg font-bold mb-2 text-gray-900 dark:text-white">{{ $address->label }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-1">{{ $address->name }}</p>
                        <p class="text-gray-600 dark:text-gray-400 mb-1">{{ $address->street_address }}</p>
                        <p class="text-gray-600 dark:text-gray-400 mb-1">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $address->country }}</p>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('messages.phone') }}: {{ $address->phone }}</p>
                        
                        <div class="flex space-x-3">
                            <button onclick="editAddress({{ $address->id }})" class="text-primary hover:text-blue-600 font-semibold text-sm">
                                {{ __('messages.edit') }}
                            </button>
                            <form action="{{ route('profile.address.delete', $address->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:hover:text-red-400 font-semibold text-sm" onclick="return confirm('{{ __('messages.confirm_delete_address') }}')">
                                    {{ __('messages.delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <button onclick="showAddAddressForm()" class="inline-block btn-primary text-white font-semibold py-3 px-8 rounded-lg transition">
                {{ __('messages.add_new_address') }}
            </button>
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
                <a href="{{ route('profile.orders') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                    {{ __('messages.orders') }}
                </a>
                <a href="{{ route('profile.addresses') }}" class="block px-4 py-2 bg-primary text-white rounded-lg">
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
        
        <!-- Add/Edit Address Form -->
        <div class="card bg-white dark:bg-gray-800 rounded-xl p-6 sticky top-20">
            <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-white" id="addressFormTitle">{{ __('messages.add_new_address') }}</h2>
            
            <form id="addressForm" action="{{ route('profile.address.add') }}" method="POST">
                @csrf
                <input type="hidden" id="addressId" name="address_id">
                
                <div class="space-y-4">
                    <div>
                        <label for="label" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.address_label') }}</label>
                        <input type="text" id="label" name="label" placeholder="{{ __('messages.eg_home_office') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.full_name') }}</label>
                        <input type="text" id="name" name="name" placeholder="{{ __('messages.full_name') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="street" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.street_address') }}</label>
                        <input type="text" id="street" name="street" placeholder="{{ __('messages.street_address_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="city" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.city') }}</label>
                        <input type="text" id="city" name="city" placeholder="{{ __('messages.city_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.state_province') }}</label>
                        <input type="text" id="state" name="state" placeholder="{{ __('messages.state_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="postal_code" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.postal_code') }}</label>
                        <input type="text" id="postal_code" name="postal_code" placeholder="{{ __('messages.postal_code_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="country" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.country') }}</label>
                        <input type="text" id="country" name="country" placeholder="{{ __('messages.country_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">{{ __('messages.phone_number') }}</label>
                        <input type="text" id="phone" name="phone" placeholder="{{ __('messages.phone_placeholder') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <!-- Hidden latitude and longitude fields -->
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">
                    
                    <div class="mt-4">
                        <button type="button" id="toggleMapForm" class="text-primary text-sm font-medium">
                            {{ __('messages.select_on_map') }}
                        </button>
                        
                        <div id="mapForm" class="mt-4 hidden">
                            <div id="addressMap" style="height: 300px;" class="rounded-lg"></div>
                            <button type="button" id="cancelMapForm" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('messages.cancel') }}
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 btn-primary text-white font-semibold py-3 px-4 rounded-lg transition">
                            {{ __('messages.save_address') }}
                        </button>
                        <button type="button" onclick="hideAddressForm()" class="flex-1 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-semibold py-3 px-4 rounded-lg transition">
                            {{ __('messages.cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let map;
    let marker;
    const mapCenter = [20.5937, 78.9629]; // Default to India center
    
    // Initialize map
    function initMap() {
        map = L.map('addressMap').setView(mapCenter, 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Click to select location
        map.on('click', function(e) {
            selectLocation(e.latlng.lat, e.latlng.lng);
        });
        
        // Geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.setView([lat, lng], 13);
                selectLocation(lat, lng);
            });
        }
    }
    
    // Select location on map
    function selectLocation(lat, lng) {
        // Remove existing marker
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Add new marker
        marker = L.marker([lat, lng]).addTo(map);
        
        // Update hidden fields
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }
    
    // Show add address form
    function showAddAddressForm() {
        document.getElementById('addressFormTitle').textContent = '{{ __('messages.add_new_address') }}';
        document.getElementById('addressForm').action = '{{ route("profile.address.add") }}';
        document.getElementById('addressForm').reset();
        document.getElementById('addressId').value = '';
    }
    
    // Hide address form
    function hideAddressForm() {
        // Reset form
        document.getElementById('addressForm').reset();
        document.getElementById('addressId').value = '';
        
        // Hide map if visible
        document.getElementById('mapForm').classList.add('hidden');
    }
    
    // Edit address
    function editAddress(addressId) {
        // Fetch address data
        fetch(`/api/address/${addressId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const address = data.address;
                    
                    // Populate form
                    document.getElementById('addressFormTitle').textContent = '{{ __('messages.edit_address') }}';
                    document.getElementById('addressForm').action = `/address/update/${address.id}`;
                    document.getElementById('addressId').value = address.id;
                    document.getElementById('label').value = address.label || '';
                    document.getElementById('name').value = address.name;
                    document.getElementById('street').value = address.street_address;
                    document.getElementById('city').value = address.city;
                    document.getElementById('state').value = address.state || '';
                    document.getElementById('postal_code').value = address.postal_code;
                    document.getElementById('country').value = address.country;
                    document.getElementById('phone').value = address.phone;
                    document.getElementById('latitude').value = address.latitude || '';
                    document.getElementById('longitude').value = address.longitude || '';
                    
                    // If coordinates exist, show map and set marker
                    if (address.latitude && address.longitude) {
                        document.getElementById('mapForm').classList.remove('hidden');
                        initMap();
                        setTimeout(() => {
                            map.setView([address.latitude, address.longitude], 13);
                            selectLocation(address.latitude, address.longitude);
                        }, 500);
                    }
                } else {
                    showToast('{{ __("messages.error_occurred") }}', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('{{ __("messages.error_occurred") }}', 'error');
            });
    }
    
    // Toggle map form
    document.getElementById('toggleMapForm').addEventListener('click', function() {
        const mapForm = document.getElementById('mapForm');
        if (mapForm.classList.contains('hidden')) {
            mapForm.classList.remove('hidden');
            initMap();
        } else {
            mapForm.classList.add('hidden');
        }
    });
    
    // Cancel map form
    document.getElementById('cancelMapForm').addEventListener('click', function() {
        document.getElementById('mapForm').classList.add('hidden');
    });
    
    // Handle form submission
    document.getElementById('addressForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const action = this.action;
        
        fetch(action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('{{ __("messages.address_saved") }}', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1500);
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