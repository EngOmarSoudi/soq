<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }
    
    public function orders()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->paginate(10);
        return view('profile.orders', compact('orders'));
    }
    
    public function addresses()
    {
        $addresses = Auth::user()->addresses;
        return view('profile.addresses', compact('addresses'));
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
        ]);
        
        $user = Auth::user();
        $user->update($request->only('name', 'email', 'phone'));
        
        return redirect()->back()->with('success', 'Profile updated successfully');
    }
    
    public function addAddress(Request $request)
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
            'street' => 'required|string',
            'city' => 'required|string',
            'state' => 'nullable|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'phone' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);
        
        try {
            $address = Address::create([
                'user_id' => auth()->id(),
                'label' => $request->label ?? 'New Address',
                'name' => $request->name ?? '',
                'street_address' => $request->street,  // Map 'street' to 'street_address'
                'city' => $request->city,
                'state' => $request->state ?? '',
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'phone' => $request->phone,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_default' => false,
            ]);
            
            // If AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address saved successfully',
                    'address' => $address,
                ]);
            }
            
            return redirect()->back()->with('success', 'Address added successfully');
        } catch (\Exception $e) {
            \Log::error('Error saving address: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error saving address: ' . $e->getMessage(),
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error saving address: ' . $e->getMessage());
        }
    }
    
    public function deleteAddress($id)
    {
        try {
            $address = Address::where('id', $id)->where('user_id', auth()->id())->first();
            
            if (!$address) {
                return redirect()->back()->with('error', 'Address not found');
            }
            
            // Check if address is being used in any orders
            $usedInOrders = \App\Models\Order::where('shipping_address_id', $id)
                ->orWhere('billing_address_id', $id)
                ->exists();
            
            if ($usedInOrders) {
                return redirect()->back()->with('error', 'Cannot delete address as it is being used in existing orders');
            }
            
            $address->delete();
            return redirect()->back()->with('success', 'Address deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting address: ' . $e->getMessage());
        }
    }
}
