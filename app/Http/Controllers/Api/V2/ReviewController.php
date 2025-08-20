<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\ReviewCollection;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;

class ReviewController extends Controller
{
    public function index($id)
    {
        return new ReviewCollection(Review::where('product_id', $id)->where('status', 1)->orderBy('updated_at', 'desc')->paginate(10));
    }

    public function submit(Request $request)
{
    $product = Product::find($request->product_id);
    $reviewable = false;
    $existingReview = \App\Models\Review::where('user_id', $request->user_id)
        ->where('product_id', $product->id)
        ->first();
    foreach ($product->orderDetails as $orderDetail) {
        if (
            $orderDetail->order != null
            && $orderDetail->order->user_id == $request->user_id
            && $orderDetail->delivery_status == 'delivered'
        ) {
            $reviewable = true;
            break;
        }
    }

    if ($existingReview) {
        return response()->json([
            'result' => false,
            'message' => translate('You have already rated this Product.'),
            'reviewable' => false
        ]);
    }

    if (!$reviewable) {
        return response()->json([
            'result' => false,
            'message' => translate('You are not eligible to review this product.'),
            'reviewable' => false
        ]);
    }

    // Proceed to save the review
    $review = new \App\Models\Review;
    $review->product_id = $request->product_id;
    $review->user_id = $request->user_id;
    $review->rating = $request->rating;
    $review->comment = $request->comment;
    $review->photos =  $request->photos;
    $review->viewed = 0;
    $review->save();

    // Update product rating
    $count = Review::where('product_id', $product->id)->where('status', 1)->count();
    $product->rating = $count > 0
        ? Review::where('product_id', $product->id)->where('status', 1)->sum('rating') / $count
        : 0;
    $product->save();

    // Update seller rating
    if ($product->added_by == 'seller') {
        $seller = $product->user->shop;
        $seller->rating = (($seller->rating * $seller->num_of_reviews) + $review->rating) / ($seller->num_of_reviews + 1);
        $seller->num_of_reviews += 1;
        $seller->save();
    }

    return response()->json([
        'result' => true,
        'message' => translate('Review Submitted'),
        'reviewable' => true
    ]);
}

}
