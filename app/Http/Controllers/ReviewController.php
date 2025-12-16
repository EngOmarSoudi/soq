<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        $productId = $request->product_id;
        $product = Product::findOrFail($productId);
        
        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $productId)
            ->first();
        
        if ($existingReview) {
            return redirect()->back()->with('error', __('messages.already_reviewed'));
        }
        
        // Create review
        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $productId,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending', // Reviews need to be approved by admin
        ]);
        
        return redirect()->back()->with('success', __('messages.review_submitted'));
    }
    
    public function update(Request $request, $reviewId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);
        
        $review = Review::where('id', $reviewId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
        
        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending', // Reset to pending for re-approval
        ]);
        
        return redirect()->back()->with('success', __('messages.review_updated'));
    }
}
