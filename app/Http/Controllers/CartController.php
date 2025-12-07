<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\BankAccount;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\PaymentService;
use App\Services\AliExpressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->price);
        $taxAmount = $this->calculateCartTax($cartItems);
        $shippingCost = $this->calculateShippingCost($cartItems);
        $total = $subtotal + $taxAmount + $shippingCost;
        
        return view('cart.index', compact('cartItems', 'subtotal', 'taxAmount', 'total', 'shippingCost'));
    }
    
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
        ]);
        
        $product = Product::findOrFail($request->product_id);
        
        // Check if product has variants and if they're required
        if ($product->hasVariants()) {
            if (!empty($product->getAvailableColors()) && !$request->color) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a color',
                ]);
            }
            
            if (!empty($product->getAvailableSizes()) && !$request->size) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select a size',
                ]);
            }
        }
        
        // Check if there's enough stock (but don't reduce it yet)
        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available',
            ]);
        }
        
        // First, try to find an existing cart item with the same product and variants
        $existingCartItem = CartItem::where([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'color' => $request->color,
            'size' => $request->size,
        ])->first();
        
        if ($existingCartItem) {
            // If item exists, update the quantity (add the new quantity to existing)
            $newQuantity = $existingCartItem->quantity + $request->quantity;
            
            // Check if there's enough stock for the new total quantity
            if ($product->stock_quantity < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available. Current stock: ' . $product->stock_quantity,
                ]);
            }
            
            $existingCartItem->update(['quantity' => $newQuantity]);
            $cartItem = $existingCartItem;
        } else {
            // If item doesn't exist, create a new one
            $cartItem = CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'color' => $request->color,
                'size' => $request->size,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);
        }
        
        $cartCount = Auth::user()->cartItems()->sum('quantity');
        $productQuantity = Auth::user()->cartItems()->where('product_id', $request->product_id)->sum('quantity');
        
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cart_count' => $cartCount,
            'product_quantity' => $productQuantity,
        ]);
    }
    
    public function update(Request $request, $itemId)
    {
        try {
            $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);
            
            \Log::info('Cart update request received', [
                'item_id' => $itemId,
                'quantity' => $request->quantity,
                'user_id' => Auth::id()
            ]);
            
            $cartItem = CartItem::where('user_id', Auth::id())->where('id', $itemId)->firstOrFail();
            $product = $cartItem->product;
            
            \Log::info('Found cart item', [
                'item_id' => $cartItem->id,
                'product_id' => $product->id,
                'current_quantity' => $cartItem->quantity,
                'new_quantity' => $request->quantity
            ]);
            
            // Check if we have enough stock for the new quantity
            if ($product->stock_quantity < $request->quantity) {
                $response = [
                    'success' => false,
                    'message' => 'Insufficient stock available. Available stock: ' . $product->stock_quantity,
                ];
                \Log::info('Insufficient stock', $response);
                return response()->json($response);
            }
            
            // Update cart item quantity
            $result = $cartItem->update(['quantity' => $request->quantity]);
            
            \Log::info('Cart item update result', [
                'item_id' => $cartItem->id,
                'requested_quantity' => $request->quantity,
                'updated_quantity' => $cartItem->quantity,
                'update_result' => $result
            ]);
            
            // Refresh the model to ensure we have the latest data
            $cartItem->refresh();
            
            \Log::info('Cart item after refresh', [
                'item_id' => $cartItem->id,
                'quantity' => $cartItem->quantity
            ]);
            
            // Verify the update was saved by querying the database directly
            $verifiedItem = CartItem::find($cartItem->id);
            \Log::info('Cart item verified from database', [
                'item_id' => $verifiedItem->id,
                'quantity' => $verifiedItem->quantity
            ]);
            
            // Clear any possible cache
            Cache::flush();
            
            // Also clear model cache if any
            DB::table('cache')->truncate();
            
            $cartItems = Auth::user()->cartItems()->with('product')->get();
            $newTotal = $cartItems->sum(fn($item) => $item->quantity * $item->price);
            $itemTotal = $request->quantity * $cartItem->price;
            
            // Calculate updated cart count
            $cartCount = $cartItems->sum('quantity');
            
            $response = [
                'success' => true,
                'message' => 'Cart updated successfully',
                'new_total' => number_format($newTotal, 2, '.', ''),
                'item_total' => number_format($itemTotal, 2, '.', ''),
                'cart_count' => $cartCount,
            ];
            
            \Log::info('Cart update response', $response);
            
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Error updating cart item: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'item_id' => $itemId,
                'quantity' => $request->quantity,
                'trace' => $e->getTraceAsString()
            ]);
            
            $response = [
                'success' => false,
                'message' => 'An error occurred while updating the cart. Please try again.',
            ];
            
            \Log::info('Cart update error response', $response);
            
            return response()->json($response, 500);
        }
    }
    
    public function remove($itemId)
    {
        $cartItem = CartItem::where('user_id', Auth::id())->where('id', $itemId)->firstOrFail();
        
        $cartItem->delete();
        
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $newTotal = $cartItems->sum(fn($item) => $item->quantity * $item->price);
        
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'new_total' => number_format($newTotal, 2),
        ]);
    }
    
    /**
     * Calculate tax amount based on individual product tax rates
     */
    private function calculateCartTax($cartItems)
    {
        $taxAmount = 0;
        
        foreach ($cartItems as $item) {
            $productTaxRate = $item->product->tax_amount ?? 0;
            $itemTax = ($item->quantity * $item->price) * ($productTaxRate / 100);
            $taxAmount += $itemTax;
        }
        
        return $taxAmount;
    }
    
    /**
     * Calculate shipping cost based on product types and customer location
     */
    private function calculateShippingCost($cartItems, $customerAddress = null)
    {
        $shippingCost = 0;
        $hasOnlineProducts = false;
        $hasLocalProducts = false;
        $onlineProductCount = 0;
        $localProductCount = 0;
        
        // Check what types of products are in the cart
        foreach ($cartItems as $item) {
            if ($item->product->supplier_type === 'online') {
                $hasOnlineProducts = true;
                $onlineProductCount += $item->quantity;
            } else {
                $hasLocalProducts = true;
                $localProductCount += $item->quantity;
            }
        }
        
        // Calculate shipping for online products
        if ($hasOnlineProducts) {
            // For demonstration, we'll use a more realistic rate for online products
            // In a real implementation, this would call the AliExpress service
            $shippingCost += ($onlineProductCount * 3.00); // Base shipping per online item
        }
        
        // Calculate shipping for local products
        if ($hasLocalProducts) {
            // Flat rate for local shipping regardless of quantity
            $shippingCost += 10.00; // Base local shipping cost
        }
        
        return $shippingCost;
    }
    
    /**
     * Calculate shipping cost for a specific address
     */
    public function calculateShippingForAddress(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id,user_id,' . Auth::id(),
        ]);
        
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $address = Auth::user()->addresses()->findOrFail($request->address_id);
        
        // For online products, we need to estimate shipping based on location
        $aliExpressService = new AliExpressService();
        $totalShippingCost = 0;
        $hasOnlineProducts = false;
        
        foreach ($cartItems as $item) {
            if ($item->product->supplier_type === 'online') {
                $hasOnlineProducts = true;
                // Prepare customer location data
                $customerLocation = [
                    'latitude' => $address->latitude,
                    'longitude' => $address->longitude,
                    'country' => $address->country,
                    'city' => $address->city
                ];
                
                // Estimate shipping cost for this product
                $shippingInfo = $aliExpressService->estimateShippingCost(
                    $item->product->sku, 
                    $customerLocation
                );
                
                $totalShippingCost += $shippingInfo['cost'] * $item->quantity;
            }
        }
        
        // Add local shipping if there are local products
        $hasLocalProducts = $cartItems->contains(function ($item) {
            return $item->product->supplier_type === 'local';
        });
        
        if ($hasLocalProducts) {
            $totalShippingCost += 10.00; // Fixed local shipping cost
        }
        
        return response()->json([
            'success' => true,
            'shipping_cost' => number_format($totalShippingCost, 2),
            'has_online_products' => $hasOnlineProducts,
            'has_local_products' => $hasLocalProducts
        ]);
    }
    
    public function checkout()
    {
        \Log::info('Checkout method called', [
            'user_id' => Auth::id(),
            'timestamp' => now()->toISOString()
        ]);
        
        // Force fresh retrieval without any caching
        // Use newQuery() to bypass any model caching
        $cartItems = CartItem::where('user_id', Auth::id())->with('product')->get();        
        \Log::info('Retrieved cart items for checkout (with proper relationships)', [
            'user_id' => Auth::id(),
            'cart_items_count' => $cartItems->count(),
            'cart_items' => $cartItems->map(function($item) {
                $productName = 'Unknown';
                if ($item->product) {
                    if (is_array($item->product->name)) {
                        $productName = $item->product->name[app()->getLocale()] ?? $item->product->name['en'] ?? $item->product->name['ar'] ?? 'Unknown';
                    } else {
                        $productName = $item->product->name;
                    }
                }
                
                return [
                    'item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'product_name' => $productName
                ];
            })->toArray()        ]);        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }
        
        // Validate stock for all items in cart
        foreach ($cartItems as $item) {
            if ($item->product->stock_quantity < $item->quantity) {
                return redirect()->route('cart.index')->with('error', 'Insufficient stock for item: ' . $item->product->name . '. Please update your cart.');
            }
        }
        
        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->price);
        
        // Calculate tax based on individual product tax rates
        $taxAmount = $this->calculateCartTax($cartItems);
        
        // Calculate initial shipping cost (will be recalculated with address selection)
        $shippingCost = $this->calculateShippingCost($cartItems, Auth::user()->addresses->first());
        
        // Check if there's an applied coupon in the session
        $coupon = session('applied_coupon');
        $discountAmount = 0;
        
        if ($coupon) {
            \Log::info('Applied coupon found in session', [
                'coupon_code' => $coupon->code,
                'coupon_id' => $coupon->id
            ]);
            
            // Validate that the coupon still meets requirements
            // Check if coupon still exists and is active
            $dbCoupon = Coupon::find($coupon->id);
            if (!$dbCoupon || !$dbCoupon->is_active) {
                // Remove invalid coupon from session
                session()->forget('applied_coupon');
                $coupon = null;
                \Log::info('Invalid or inactive coupon removed from session');
            } 
            // Check minimum order amount
            else if ($dbCoupon->minimum_order_amount && $subtotal < $dbCoupon->minimum_order_amount) {
                // Remove coupon that no longer meets minimum order requirement
                session()->forget('applied_coupon');
                $coupon = null;
                \Log::info('Coupon removed from session due to minimum order requirement not met', [
                    'coupon_code' => $dbCoupon->code,
                    'subtotal' => $subtotal,
                    'minimum_order_amount' => $dbCoupon->minimum_order_amount
                ]);
            }
            // Check validity period
            else if (($dbCoupon->valid_from && $dbCoupon->valid_from > now()) || 
                     ($dbCoupon->valid_until && $dbCoupon->valid_until < now())) {
                // Remove expired or not yet valid coupon
                session()->forget('applied_coupon');
                $coupon = null;
                \Log::info('Coupon removed from session due to validity period', [
                    'coupon_code' => $dbCoupon->code,
                    'valid_from' => $dbCoupon->valid_from,
                    'valid_until' => $dbCoupon->valid_until
                ]);
            }
            // If coupon is still valid, calculate discount
            else if ($coupon) {
                $discountAmount = $this->calculateDiscount($subtotal, $coupon);
            }
        }
        
        $total = $subtotal + $shippingCost + $taxAmount - $discountAmount; // shipping + tax - discount
        
        \Log::info('Checkout page loaded', [
            'user_id' => Auth::id(),
            'cart_items_count' => $cartItems->count(),
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'has_coupon' => $coupon ? true : false
        ]);
        
        $bankAccounts = BankAccount::where('is_active', true)->get();
        
        return response()
            ->view('checkout', compact('cartItems', 'subtotal', 'total', 'coupon', 'discountAmount', 'taxAmount', 'shippingCost', 'bankAccounts'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);
        
        // Log the coupon code being checked
        \Log::info('Checking coupon code', ['coupon_code' => $request->coupon_code, 'user_id' => Auth::id()]);
        
        $coupon = Coupon::where('code', $request->coupon_code)->first();
        
        if (!$coupon) {
            \Log::info('Coupon not found', ['coupon_code' => $request->coupon_code]);
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_or_expired_coupon'),
            ]);
        }
        
        // Log coupon details for debugging
        \Log::info('Coupon found', [
            'coupon_code' => $coupon->code,
            'is_active' => $coupon->is_active,
            'valid_from' => $coupon->valid_from,
            'valid_until' => $coupon->valid_until,
            'current_time' => now()
        ]);
        
        // Check if coupon is active
        if (!$coupon->is_active) {
            \Log::info('Coupon is not active', ['coupon_code' => $coupon->code]);
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_or_expired_coupon'),
            ]);
        }
        
        // Check validity period
        if ($coupon->valid_from && $coupon->valid_from > now()) {
            \Log::info('Coupon not yet valid', ['coupon_code' => $coupon->code, 'valid_from' => $coupon->valid_from]);
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_or_expired_coupon'),
            ]);
        }
        
        if ($coupon->valid_until && $coupon->valid_until < now()) {
            \Log::info('Coupon has expired', ['coupon_code' => $coupon->code, 'valid_until' => $coupon->valid_until]);
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_or_expired_coupon'),
            ]);
        }
        
        // Check usage limits
        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            \Log::info('Coupon usage limit reached', [
                'coupon_code' => $coupon->code,
                'usage_count' => $coupon->usage_count,
                'usage_limit' => $coupon->usage_limit
            ]);
            return response()->json([
                'success' => false,
                'message' => __('messages.coupon_usage_limit_reached'),
            ]);
        }
        
        // Check per user limit
        $userUsageCount = Auth::user()->orders()->where('coupon_id', $coupon->id)->count();
        if ($coupon->per_user_limit && $userUsageCount >= $coupon->per_user_limit) {
            \Log::info('User coupon limit reached', [
                'coupon_code' => $coupon->code,
                'user_usage_count' => $userUsageCount,
                'per_user_limit' => $coupon->per_user_limit
            ]);
            return response()->json([
                'success' => false,
                'message' => __('messages.coupon_user_limit_reached'),
            ]);
        }
        
        // Get cart subtotal
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $subtotal = $cartItems->sum(fn($item) => $item->quantity * $item->price);
        
        \Log::info('Cart subtotal calculated', ['subtotal' => $subtotal]);
        
        // Check minimum order amount
        if ($coupon->minimum_order_amount && $subtotal < $coupon->minimum_order_amount) {
            \Log::info('Minimum order amount not met', [
                'coupon_code' => $coupon->code,
                'subtotal' => $subtotal,
                'minimum_order_amount' => $coupon->minimum_order_amount
            ]);
            return response()->json([
                'success' => false,
                'message' => __('messages.minimum_order_amount_required', ['amount' => number_format($coupon->minimum_order_amount, 2)]),
            ]);
        }
        
        // Store coupon in session
        session(['applied_coupon' => $coupon]);
        
        // Calculate discount
        $discountAmount = $this->calculateDiscount($subtotal, $coupon);
        $newTotal = $subtotal + 10.00 + ($subtotal * 0.1) - $discountAmount;
        
        \Log::info('Coupon applied successfully', [
            'coupon_code' => $coupon->code,
            'discount_amount' => $discountAmount,
            'new_total' => $newTotal
        ]);
        
        return response()->json([
            'success' => true,
            'message' => __('messages.coupon_applied_successfully'),
            'discount_amount' => number_format($discountAmount, 2),
            'new_total' => number_format($newTotal, 2),
        ]);
    }
    
    private function calculateDiscount($subtotal, $coupon)
    {
        $discountAmount = 0;
        
        if ($coupon->discount_type === 'percentage') {
            $discountAmount = $subtotal * ($coupon->discount_value / 100);
        } else {
            $discountAmount = $coupon->discount_value;
        }
        
        // Apply maximum discount limit if set
        if ($coupon->maximum_discount && $discountAmount > $coupon->maximum_discount) {
            $discountAmount = $coupon->maximum_discount;
        }
        
        return $discountAmount;
    }
    
    public function placeOrder(Request $request)
    {
        try {
            \DB::beginTransaction();
            
            \Log::info('Place order request started', ['user_id' => Auth::id()]);
            
            // Validate the request
            $rules = [
                'shipping_address_id' => 'required|exists:addresses,id,user_id,' . Auth::id(),
                'payment_method' => 'required|in:bank_transfer,credit_card',
            ];
            
            // Add conditional validation for payment method
            if ($request->payment_method === 'bank_transfer') {
                $rules['payment_receipt'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
            } elseif ($request->payment_method === 'credit_card') {
                $rules['card_number'] = 'required|string';
                $rules['cardholder_name'] = 'required|string';
                $rules['expiry_month'] = 'required|numeric';
                $rules['expiry_year'] = 'required|numeric';
                $rules['cvv'] = 'required|string';
            }
            
            \Log::info('Validating request with rules', ['rules' => $rules]);
            
            $validatedData = $request->validate($rules);
            
            \Log::info('Validation passed');
            
            // Validate credit card fields if payment method is credit_card
            if ($request->payment_method === 'credit_card') {
                \Log::info('Processing credit card payment');
                $paymentResult = PaymentService::processPayment([
                    'card_number' => $request->card_number,
                    'cardholder_name' => $request->cardholder_name,
                    'expiry_month' => $request->expiry_month,
                    'expiry_year' => $request->expiry_year,
                    'cvv' => $request->cvv,
                ]);
                
                \Log::info('Payment result', ['result' => $paymentResult]);
                
                if (!$paymentResult['success']) {
                    return redirect()->back()->with('error', 'Payment failed: ' . implode(', ', $paymentResult['errors']));
                }
            }
            
            $cartItems = auth()->user()->cartItems()->with('product')->get();
            
            if ($cartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your cart is empty');
            }
            
            // Validate stock for all items in cart before processing order
            foreach ($cartItems as $item) {
                if ($item->product->stock_quantity < $item->quantity) {
                    \DB::rollBack();
                    return redirect()->route('cart.index')->with('error', 'Insufficient stock for item: ' . ($item->product->name[app()->getLocale()] ?? $item->product->name['en'] ?? $item->product->name) . '. Please update your cart.');
                }
            }
            
            // Get applied coupon from session
            $appliedCoupon = session('applied_coupon');
            
            // Calculate totals
            $subtotal = $cartItems->sum(function($item) {
                return $item->quantity * $item->price;
            });
            
            // Recalculate shipping cost with the selected address
            $shippingAddress = auth()->user()->addresses()->find($request->shipping_address_id);
            $shippingCost = $this->calculateShippingCost($cartItems, $shippingAddress);
            
            $taxAmount = $this->calculateCartTax($cartItems); // Calculate tax based on product tax rates
            
            // Calculate discount if coupon is applied
            $discountAmount = 0;
            if ($appliedCoupon) {
                $discountAmount = $this->calculateDiscount($subtotal, $appliedCoupon);
            }
            
            $totalAmount = $subtotal + $shippingCost + $taxAmount - $discountAmount;
            
            // Handle bank transfer receipt upload
            $receiptPath = null;
            if ($request->payment_method === 'bank_transfer' && $request->hasFile('payment_receipt')) {
                \Log::info('Uploading receipt file');
                $receiptPath = $request->file('payment_receipt')->store('receipts', 'public');
                \Log::info('Receipt uploaded', ['path' => $receiptPath]);
            }
            
            // Create order
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . Str::random(6),
                'user_id' => auth()->id(),
                'shipping_address_id' => $request->shipping_address_id,
                'billing_address_id' => $request->shipping_address_id,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'credit_card' ? 'completed' : 'pending',
                'payment_reference' => $receiptPath, // Store receipt path for bank transfers
                'status' => $request->payment_method === 'credit_card' ? 'processing' : 'pending',
                'coupon_id' => $appliedCoupon ? $appliedCoupon->id : null,
            ]);
            
            \Log::info('Order created', ['order_id' => $order->id]);
            
            // Increment coupon usage count if coupon was applied
            if ($appliedCoupon) {
                $appliedCoupon->increment('usage_count');
            }
            
            // Clear applied coupon from session
            session()->forget('applied_coupon');
            
            // Create order items and reduce stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'total_price' => $item->quantity * $item->price,
                    'color' => $item->color,
                    'size' => $item->size,
                ]);
                
                // Reduce product stock only for local products
                if ($item->product->supplier_type === 'local') {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }
            }
            
            // Clear cart
            auth()->user()->cartItems()->delete();
            
            \DB::commit();
            \Log::info('Order placement completed successfully');
            
            return redirect()->route('order.confirmation', $order->id)->with('success', 'Order placed successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \DB::rollBack();
            \Log::error('Validation error placing order', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error placing order', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while placing your order: ' . $e->getMessage())->withInput();
        }
    }
    
    public function confirmation($orderId)
    {
        $order = Order::with('items.product', 'shippingAddress')->findOrFail($orderId);
        
        // Check if user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        $bankAccounts = [];
        if ($order->payment_method === 'bank_transfer') {
            $bankAccounts = BankAccount::where('is_active', true)->get();
        }
        
        return view('order-confirmation', compact('order', 'bankAccounts'));
    }
}