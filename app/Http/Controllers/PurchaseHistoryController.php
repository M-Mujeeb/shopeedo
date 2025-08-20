<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use DB;
use Auth;
use App\Models\Order;
use App\Models\Upload;
use App\Models\Product;
use App\Utility\CartUtility;
use Cookie;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\CombinedOrder;
use App\Models\DeliveryBoy;
use App\Models\Rating;
use App\Models\Shop;
use App\Models\User;

class PurchaseHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::with('orderDetails')->where('user_id', Auth::user()->id)->orderBy('code', 'desc')->paginate(10);
        return view('frontend.user.purchase_history', compact('orders'));
    }

    public function digital_index()
    {
        $orders = DB::table('orders')
            ->orderBy('code', 'desc')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->where('orders.user_id', Auth::user()->id)
            ->where('products.digital', '1')
            ->where('order_details.payment_status', 'paid')
            ->select('order_details.id')
            ->paginate(15);
        return view('frontend.user.digital_purchase_history', compact('orders'));
    }

    public function purchase_history_details($id)
    {

        $order = Order::findOrFail(decrypt($id));
        if (env('DEMO_MODE') != 'On') {
            $order->delivery_viewed = 1;
            $order->payment_status_viewed = 1;
            $order->save();
        }
        $thankYou = false;
        session()->forget('order_id');
        if(session()->has('thankYou'))
        {
             $thankYou = session()->get('thankYou');

            session()->forget('thankYou');
        }


        $shop = Shop::where('user_id', $order->seller_id)->first();
        $user_address = Address::where('user_id', auth()->user()->id)
            ->where('set_default', 1)
            ->first();
        $platform = BusinessSetting::where('type', 'platform_fee')->first();
        $platform_fees = $platform->value;

        $sellerLat = $shop->delivery_pickup_latitude;    // Replace with seller's latitude
        $sellerLng = $shop->delivery_pickup_longitude;   // Replace with seller's longitude
        // $deliveryBoyLat = 40.7306; // Replace with delivery boy's latitude
        // $deliveryBoyLng = -73.9352;
        $customerLat = $user_address->latitude;
        $customerLng = $user_address->longitude;
        // dd($order);
        return view('frontend.user.order_details_customer', compact('order', 'thankYou', 'platform_fees','sellerLat', 'sellerLng', 'customerLat', 'customerLng'));
    }

    public function current_lat_long_delivery($id) {

        $order = Order::findOrFail($id);
        // dd($order->delivery_boy);
        if($order->delivery_boy != null ){
           return response()->json([
            'delivery_status' => $order->delivery_status ,
            'lat' => $order->delivery_boy->latitude,
            'lng'  => $order->delivery_boy->longitude

                // "delivery_status" => "picked_up",
                // "lat"=> 31.587370658176372,
                // "lng"=>  74.30903823758972


           ], 200);
        }


    }

    public function purchase_history_details_status($id)
    {
        $order = Order::findOrFail($id);
        // dd($order);
        if (env('DEMO_MODE') != 'On') {
            $order->delivery_viewed = 1;
            $order->payment_status_viewed = 1;
            $order->save();
        }
        
        $assign_delivery_boy_id = $order->assign_delivery_boy;

        $delivery_boy = User::where('id', $assign_delivery_boy_id)->first();

        
        $current_time = date('H:i:s');
        $combined_order = CombinedOrder::where('id', $order->combined_order_id)->first();
        // dd($combined_order->created_at->format('H:i:s'));
        $time_difference_in_minutes = "";
        // dd($current_time);

        // if($order->delivery_status == 'confirmed'){
        //    if($combined_order->confirmed_time == null) {
        //     $combined_order->confirmed_time = $current_time;
        //     $combined_order->save();
        //    } else {


        //     // dd($time_difference_in_minutes);

        //    }
        // }

        $current_time_obj = new \DateTime($current_time);
        $confirmed_time_obj = new \DateTime($combined_order->created_at->format('H:i:s'));

        // Calculate the difference
        $time_difference = $confirmed_time_obj->diff($current_time_obj);

        // Get the total time difference in minutes
        $time_difference_in_minutes = ($time_difference->h * 60) + $time_difference->i;

        // dd($combined_order->confirmed_time);
        $over_time = false;
        if($time_difference_in_minutes <= 60) {
            return response()->json(['delivery_status' => $order->delivery_status, 'over_time' => $over_time, 'time_difference' => $time_difference_in_minutes, 'delivery_boy' => $delivery_boy, 'order_id' => $order->id ], 200);
        } else {
            $over_time = true;
            return response()->json(['delivery_status' => $order->delivery_status, 'over_time' => $over_time, 'time_difference' => $time_difference_in_minutes, 'delivery_boy' => $delivery_boy, 'order_id' => $order->id ], 200);
        }




        // return view('frontend.user.order_details_customer', compact('order'));
    }


    public function download(Request $request)
    {
        $product = Product::findOrFail(decrypt($request->id));
        $downloadable = false;
        foreach (Auth::user()->orders as $key => $order) {
            foreach ($order->orderDetails as $key => $orderDetail) {
                if ($orderDetail->product_id == $product->id && $orderDetail->payment_status == 'paid') {
                    $downloadable = true;
                    break;
                }
            }
        }
        if ($downloadable) {
            $upload = Upload::findOrFail($product->file_name);
            if (env('FILESYSTEM_DRIVER') == "s3") {
                return \Storage::disk('s3')->download($upload->file_name, $upload->file_original_name . "." . $upload->extension);
            } else {
                if (file_exists(base_path('public/' . $upload->file_name))) {
                    return response()->download(base_path('public/' . $upload->file_name));
                }
            }
        } else {
            flash(translate('You cannot download this product.'))->success();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function order_cancel($id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->user()->id)->first();
        if ($order && ($order->delivery_status == 'confirmed' && $order->payment_status == 'unpaid')) {
            $order->delivery_status = 'cancelled';
            $order->cancel_by = 'customer';
            $order->save();

            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = 'cancelled';
                $orderDetail->save();
                product_restock($orderDetail);
            }

            flash(translate('Order has been canceled successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }

        return back();
    }

    public function re_order($id)
    {
        $user_id = Auth::user()->id;

        // if Cart has auction product check
        $carts = Cart::where('user_id', $user_id)->get();
        foreach ($carts as $cartItem) {
            $cart_product = Product::where('id', $cartItem['product_id'])->first();
            if ($cart_product->auction_product == 1) {
                flash(translate('Remove auction product from cart to add products.'))->error();
                return back();
            }
        }

        $order = Order::findOrFail(decrypt($id));
        $success_msgs = [];
        $failed_msgs = [];
        $data['user_id'] = $user_id;
        foreach ($order->orderDetails as $key => $orderDetail) {
            $product = $orderDetail->product;

            if (
                !$product || $product->published == 0 ||
                $product->approved == 0 || ($product->wholesale_product && !addon_is_activated("wholesale"))
            ) {
                array_push($failed_msgs, translate('An item from this order is not available now.'));
                continue;
            }

            if ($product->auction_product == 0) {

                // If product min qty is greater then the ordered qty, then update the order qty
                $order_qty = $orderDetail->quantity;
                if ($product->digital == 0 && $order_qty < $product->min_qty) {
                    $order_qty = $product->min_qty;
                }

                $cart = Cart::firstOrNew([
                    'variation' => $orderDetail->variation,
                    'user_id' => auth()->user()->id,
                    'product_id' => $product->id
                ]);

                $product_stock = $product->stocks->where('variant', $orderDetail->variation)->first();
                if ($product_stock) {
                    $quantity = 1;
                    if ($product->digital != 1) {
                        $quantity = $product_stock->qty;
                        if ($quantity > 0) {
                            if ($cart->exists) {
                                $order_qty = $cart->quantity + $order_qty;
                            }
                            //If order qty is greater then the product stock, set order qty = current product stock qty
                            $quantity = $quantity >= $order_qty ? $order_qty : $quantity;
                        } else {
                            array_push($failed_msgs, $product->getTranslation('name') . ' ' . translate(' is stock out.'));
                            continue;
                        }
                    }
                    $price = CartUtility::get_price($product, $product_stock, $quantity);
                    $tax = CartUtility::tax_calculation($product, $price);

                    CartUtility::save_cart_data($cart, $product, $price, $tax, $quantity);
                    array_push($success_msgs, $product->getTranslation('name') . ' ' . translate('added to cart.'));
                } else {
                    array_push($failed_msgs, $product->getTranslation('name') . ' ' . translate('is stock out.'));
                }
            } else {
                array_push($failed_msgs, translate('You can not re order an auction product.'));
                break;
            }
        }

        foreach ($failed_msgs as $msg) {
            flash($msg)->warning();
        }
        foreach ($success_msgs as $msg) {
            flash($msg)->success();
        }

        return redirect()->route('cart');
    }

    public function rateDeliveryBoyStore(Request $request){
        $request->validate([
            'delivery_id' => 'required',
            'order_id' => 'required',

        ]);

        Rating::create([
            'delivery_boy_id' => $request->delivery_id,
            'user_id' => auth()->user()->id,
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);


        flash(translate('Review has been submitted successfully'))->success();
        return back();


    }
}
