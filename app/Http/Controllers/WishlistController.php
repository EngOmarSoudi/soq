<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle($productId)
    {
        $product = Product::findOrFail($productId);
        
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();
        
        if ($wishlist) {
            // Product is already in wishlist, return appropriate message
            return response()->json([
                'success' => true,
                'added' => false,
                'message' => __('messages.product_already_in_wishlist')
            ]);
        } else {
            // Product is not in wishlist, add it
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $productId,
            ]);
            
            return response()->json([
                'success' => true,
                'added' => true,
                'message' => __('messages.product_added_to_wishlist')
            ]);
        }
    }
    
    public function index()
    {
        $wishlists = auth()->user()->wishlists()->with('product')->paginate(12);
        return view('wishlist.index', ['wishlists' => $wishlists]);
    }
    
    public function remove($id)
    {
        Wishlist::where('id', $id)->where('user_id', auth()->id())->delete();
        return response()->json([
            'success' => true,
            'message' => __('messages.product_removed_from_wishlist')
        ]);
    }
}