<?php

namespace App\Http\Controllers\Api\V2;

use DB;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Rating;
use App\Models\Address;
use App\Models\Product;
use App\Models\CouponUsage;
use App\Models\DeliveryBoy;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Jobs\AssignDeliveryBoy;
use App\Models\BusinessSetting;
use \App\Utility\NotificationUtility;
use App\Http\Controllers\AffiliateController;

class OrderController extends Controller
{

    public function assign_delivery_boy(Request $request)
    {

        if (addon_is_activated('delivery_boy')) {

            $order = Order::findOrFail($request->order_id);
            $order->assign_delivery_boy = $request->delivery_boy;
            $order->delivery_history_date = date("Y-m-d H:i:s");
            $order->save();

            $delivery_history = \App\Models\DeliveryHistory::where('order_id', $order->id)
                ->where('delivery_status', $order->delivery_status)
                ->first();

            if (empty($delivery_history)) {
                $delivery_history = new \App\Models\DeliveryHistory;

                $delivery_history->order_id = $order->id;
                $delivery_history->delivery_status = $order->delivery_status;
                $delivery_history->payment_type = $order->payment_type;
            }
            $delivery_history->delivery_boy_id = $request->delivery_boy;

            $delivery_history->save();
            // dd($order->delivery_boy->device_token);

            if (env('MAIL_USERNAME') != null && get_setting('delivery_boy_mail_notification') == '1') {
                $array['view'] = 'emails.invoice';
                $array['subject'] = translate('You are assigned to delivery an order. Order code') . ' - ' . $order->code;
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['order'] = $order;

                // try {
                //     Mail::to($order->delivery_boy->email)->queue(new InvoiceEmailManager($array));

                //     if(get_setting('google_firebase') == 1 && $order->delivery_boy->device_token != null) {
                //         $request->device_token = $order->delivery_boy->device_token;
                //         $request->title = "Order Assigned !";
                //         // $status = str_replace("_", "", $order->payment_status);
                //         $request->text = " You are assigned to delivery an order.  Order code {$order->code} ";

                //         $request->type = "order";
                //         $request->id = $order->id;
                //         $request->user_id = $order->delivery_boy->id;

                //         NotificationUtility::sendFirebaseNotification($request);
                //     }


                // } catch (\Exception $e) {
                // }
            }

            // if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'assign_delivery_boy')->first()->status == 1) {
            //     try {
            //         SmsUtility::assign_delivery_boy($order->delivery_boy->phone, $order->code);
            //     } catch (\Exception $e) {
            //     }
            // }
        }

        return 1;
    }

    // public function store(Request $request, $set_paid = false)
    // {
    //     if (get_setting('minimum_order_amount_check') == 1) {
    //         $subtotal = 0;
    //         foreach (Cart::where('user_id', auth()->user()->id)->active()->get() as $key => $cartItem) {
    //             $product = Product::find($cartItem['product_id']);
    //             $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
    //         }
    //         if ($subtotal < get_setting('minimum_order_amount')) {
    //             return $this->failed(translate("You order amount is less then the minimum order amount"));
    //         }
    //     }

    //     $cartItems = Cart::where('user_id', auth()->user()->id)->active()->get();

    //     if ($cartItems->isEmpty()) {
    //         return response()->json([
    //             'combined_order_id' => 0,
    //             'result' => false,
    //             'message' => translate('Cart is Empty')
    //         ]);
    //     }

    //     $user = User::find(auth()->user()->id);

    //     $address = Address::where('user_id', $user->id)->where('set_default', 1)->first();

        
    //     $shippingAddress = [];
    //     if ($address != null) {
    //         $shippingAddress['name']        = $user->name;
    //         $shippingAddress['email']       = $user->email;
    //         $shippingAddress['address']     = $address->address;
    //         // $shippingAddress['country']     = $address->country->name;
    //         // $shippingAddress['state']       = $address->state->name;
    //         $shippingAddress['city']        = $address->city_name;
    //         // $shippingAddress['postal_code'] = $address->postal_code;
    //         $shippingAddress['phone']       = $address->phone;
    //         if ($address->latitude || $address->longitude) {
    //             $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
    //         }
    //     }

    //     $combined_order = new CombinedOrder;
    //     $combined_order->user_id = $user->id;
    //     $combined_order->shipping_address = json_encode($shippingAddress);
    //     $combined_order->shipping_cost = $request->shipping_cost;
    //     $combined_order->save();

    //     $seller_products = array();
    //     foreach ($cartItems as $cartItem) {
    //         $product_ids = array();
    //         $product = Product::find($cartItem['product_id']);
    //         if (isset($seller_products[$product->user_id])) {
    //             $product_ids = $seller_products[$product->user_id];
    //         }
    //         array_push($product_ids, $cartItem);
    //         $seller_products[$product->user_id] = $product_ids;
    //     }

    //     foreach ($seller_products as $seller_product) {
    //         $order = new Order;
    //         $order->combined_order_id = $combined_order->id;
    //         $order->user_id = $user->id;
    //         $order->shipping_address = $combined_order->shipping_address;
    //         $order->additional_info = $request->delivery_instructions;

    //         $order->order_from = 'app';
    //         $order->payment_type = $request->payment_type;
    //         $order->delivery_viewed = '0';
    //         $order->payment_status_viewed = '0';
    //         $order->code = date('Ymd-His') . rand(10, 99);
    //         $order->date = strtotime('now');
    //         if ($set_paid) {
    //             $order->payment_status = 'paid';
    //         } else {
    //             $order->payment_status = 'unpaid';
    //         }

    //         $orderType = 'ecommerce';
    //         foreach ($seller_product as $cartItem) {
    //             $product = Product::find($cartItem['product_id']);
    //             $category = $product->category;

    //             if ($category && $category->is_quick) {
    //                 $orderType = 'Taiz';
    //                 break;
    //             }
    //         }

    //         $order->type = $orderType;

    //         $order->save();

    //         $subtotal = 0;
    //         $tax = 0;
    //         $shipping = 0;
    //         $coupon_discount = 0;


    //         //Order Details Storing
    //         foreach ($seller_product as $cartItem) {
    //             $product = Product::find($cartItem['product_id']);

    //             $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
    //             $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
    //             $coupon_discount += $cartItem['discount'];

    //             $product_variation = $cartItem['variation'];

    //             $product_stock = $product->stocks->where('variant', $product_variation)->first();
    //             if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
    //                 $order->delete();
    //                 $combined_order->delete();
    //                 return response()->json([
    //                     'combined_order_id' => 0,
    //                     'result' => false,
    //                     'message' => translate('The requested quantity is not available for ') . $product->name
    //                 ]);
    //             } elseif ($product->digital != 1) {
    //                 $product_stock->qty -= $cartItem['quantity'];
    //                 $product_stock->save();
    //             }

    //             $order_detail = new OrderDetail;
    //             $order_detail->order_id = $order->id;
    //             $order_detail->seller_id = $product->user_id;
    //             $order_detail->product_id = $product->id;
    //             $order_detail->variation = $product_variation;
    //             $order_detail->price = cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
    //             $order_detail->tax = cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
    //             $order_detail->shipping_type = $cartItem['shipping_type'];
    //             $order_detail->product_referral_code = $cartItem['product_referral_code'];
    //             $order_detail->shipping_cost = $cartItem['shipping_cost'] + $request->shipping_cost;

    //             $shipping += $order_detail->shipping_cost;

    //             //End of storing shipping cost
    //             if (addon_is_activated('club_point')) {
    //                 $order_detail->earn_point = $product->earn_point;
    //             }

    //             $order_detail->quantity = $cartItem['quantity'];
    //             $order_detail->save();

    //             $product->num_of_sale = $product->num_of_sale + $cartItem['quantity'];
    //             $product->save();

    //             $order->seller_id = $product->user_id;
    //             if($orderType == 'Taiz') {
    //                 $order->delivery_status ='confirmed';
    //             }
    //             $order->shipping_type = $cartItem['shipping_type'];
    //             if ($cartItem['shipping_type'] == 'pickup_point') {
    //                 $order->pickup_point_id = $cartItem['pickup_point'];
    //             }
    //             if ($cartItem['shipping_type'] == 'carrier') {
    //                 $order->carrier_id = $cartItem['carrier_id'];
    //             }

    //             if ($product->added_by == 'seller' && $product->user->seller != null) {
    //                 $seller = $product->user->seller;
    //                 $seller->num_of_sale += $cartItem['quantity'];
    //                 $seller->save();
    //             }

    //             if (addon_is_activated('affiliate_system')) {
    //                 if ($order_detail->product_referral_code) {
    //                     $referred_by_user = User::where('referral_code', $order_detail->product_referral_code)->first();

    //                     $affiliateController = new AffiliateController;
    //                     $affiliateController->processAffiliateStats($referred_by_user->id, 0, $order_detail->quantity, 0, 0);
    //                 }
    //             }
    //         }

    //        $order->grand_total = $subtotal + $tax + $shipping + get_setting('platform_fee');

    //         if ($seller_product[0]->coupon_code != null) {
    //             $order->coupon_discount = $coupon_discount;
    //             $order->grand_total -= $coupon_discount;

    //             $coupon_usage = new CouponUsage;
    //             $coupon_usage->user_id = $user->id;
    //             $coupon_usage->coupon_id = Coupon::where('code', $seller_product[0]->coupon_code)->first()->id;
    //             $coupon_usage->save();
    //         }

    //         $combined_order->grand_total += $order->grand_total;


    //         if (strpos($request->payment_type, "manual_payment_") !== false) { // if payment type like  manual_payment_1 or  manual_payment_25 etc)

    //             $order->manual_payment = 1;
    //             $order->save();
    //         }

    //         $order->save();

    //         $this->sendNewOrderNotification($order, $order->seller_id );

    //     }
    //     $combined_order->save();

    //     Cart::where('user_id', auth()->user()->id)->active()->delete();

    //     if (
    //         $request->payment_type == 'cash_on_delivery'
    //         || $request->payment_type == 'wallet'
    //         || strpos($request->payment_type, "manual_payment_") !== false // if payment type like  manual_payment_1 or  manual_payment_25 etc
    //     ) {
    //         NotificationUtility::sendOrderPlacedNotification($order);
    //     }
    //         $combine_order = CombinedOrder::findOrFail($combined_order->id);
    //         $first_order = $combine_order->orders->first();

    //         // Calculate totals
    //         $shipping_cost = 0;
    //         $price = 0;
    //         $tax = 0;
    //         $coupon_discount = 0;
    //         $platform_fees = BusinessSetting::where('type', 'platform_fee')->first()->value;

    //         foreach($combine_order->orders as $order){
    //             foreach ($order->orderDetails as $orderDetail){
    //                 $shipping_cost += $orderDetail->shipping_cost;
    //                 $price += $orderDetail->price;
    //                 $tax += $orderDetail->tax;
    //                 $coupon_discount += $orderDetail->coupon_discount;
    //             }
    //         }

    //         // Prepare order details
    //         $order_details = [];
    //         foreach ($combine_order->orders as $uperkey => $order) {
    //             foreach ($order->orderDetails as $key => $orderDetail) {
    //                 $order_details[] = [
    //                     'index' => $uperkey + 1,
    //                     'product_name' => $orderDetail->product ? $orderDetail->product->getTranslation('name') : 'Product Unavailable',
    //                     // 'combo_title' => $orderDetail->combo_id ? \App\ComboProduct::findOrFail($orderDetail->combo_id)->combo_title : null,
    //                     'variation' => $orderDetail->variation != null ? $orderDetail->variation : '',
    //                     'quantity' => $orderDetail->quantity,
    //                     'delivery_type' => $order->shipping_type == 'home_delivery' ? 'Home Delivery' :
    //                                        ($order->shipping_type == 'carrier' ? 'Carrier' :
    //                                        ($order->shipping_type == 'pickup_point' ?
    //                                         ($order->pickup_point ? $order->pickup_point->getTranslation('name') . ' (Pickup Point)' : 'Pickup Point') : '')),
    //                     'order_code' => $order->code,
    //                     'price' => single_price($orderDetail->price)
    //                 ];
    //             }
    //         }
             
    //         // Prepare shipping address
    //         $shipping_address = json_decode($first_order->shipping_address);

    //         return response()->json([
    //             'result' => true,
    //             'message' => translate('Your order has been placed successfully'),
    //             'comingFrom' => true,
    //             'combined_order_id' =>$first_order->id,
    //             'order_summary' => [
    //                 'combined_order_id' => $combine_order->id,
    //                 'order_date' => date('d-m-Y H:i A', $first_order->date),
    //                 'name' => $shipping_address->name,
    //                 'email' => $shipping_address->email,
    //                 'shipping_address' => $shipping_address->address . ', ' . $shipping_address->city,
    //                 'order_status' => translate(ucfirst(str_replace('_', ' ', $first_order->delivery_status))),
    //                 'payment_method' => translate(ucfirst(str_replace('_', ' ', $first_order->payment_type))),
    //             ],
    //             'order_details' => $order_details,
    //             'order_totals' => [
    //                 'subtotal' => single_price($price),
    //                 'shipping' => single_price($shipping_cost),
    //                 'tax' => single_price($tax),
    //                 'coupon_discount' => single_price($coupon_discount),
    //                 'platform_fees' => single_price($platform_fees),
    //                 'total' => single_price(floatval($combine_order->grand_total) + floatval($platform_fees))
    //             ]
    //         ]);
    // }

public function  store(Request $request, $set_paid = false)
{
    if (get_setting('minimum_order_amount_check') == 1) {
        $subtotal = 0;
        foreach (Cart::where('user_id', auth()->user()->id)->active()->get() as $key => $cartItem) {
            $product = Product::find($cartItem['product_id']);
            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
        }
        if ($subtotal < get_setting('minimum_order_amount')) {
            return $this->failed(translate("You order amount is less then the minimum order amount"));
        }
    }

    $cartItems = Cart::where('user_id', auth()->user()->id)->active()->get();

    if ($cartItems->isEmpty()) {
        return response()->json([
            'combined_order_id' => 0,
            'result' => false,
            'message' => translate('Cart is Empty')
        ]);
    }

    $user = User::find(auth()->user()->id);

    $address = Address::where('user_id', $user->id)->where('set_default', 1)->first();

    
    $shippingAddress = [];
    if ($address != null) {
        $shippingAddress['name']        = $user->name;
        $shippingAddress['email']       = $user->email;
        $shippingAddress['address']     = $address->address;
        // $shippingAddress['country']     = $address->country->name;
        // $shippingAddress['state']       = $address->state->name;
        $shippingAddress['city']        = $address->city_name;
        // $shippingAddress['postal_code'] = $address->postal_code;
        $shippingAddress['phone']       = $address->phone;
        if ($address->latitude || $address->longitude) {
            $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
        }
    }

    $combined_order = new CombinedOrder;
    $combined_order->user_id = $user->id;
    $combined_order->shipping_address = json_encode($shippingAddress);
    $combined_order->shipping_cost = $request->shipping_cost;
    $combined_order->save();

    $seller_products = array();
    foreach ($cartItems as $cartItem) {
        $product_ids = array();
        $product = Product::find($cartItem['product_id']);
        if (isset($seller_products[$product->user_id])) {
            $product_ids = $seller_products[$product->user_id];
        }
        array_push($product_ids, $cartItem);
        $seller_products[$product->user_id] = $product_ids;
    }

    // Track if shipping cost has been applied to avoid duplication
    $shipping_cost_applied = false;

    foreach ($seller_products as $seller_product) {
        $order = new Order;
        $order->combined_order_id = $combined_order->id;
        $order->user_id = $user->id;
        $order->shipping_address = $combined_order->shipping_address;
        $order->additional_info = $request->delivery_instructions;

        $order->order_from = 'app';
        $order->payment_type = $request->payment_type;
        $order->delivery_viewed = '0';
        $order->payment_status_viewed = '0';
        $order->code = date('Ymd-His') . rand(10, 99);
        $order->date = strtotime('now');
        if ($set_paid) {
            $order->payment_status = 'paid';
        } else {
            $order->payment_status = 'unpaid';
        }

        $orderType = 'ecommerce';
        foreach ($seller_product as $cartItem) {
            $product = Product::find($cartItem['product_id']);
            $category = $product->category;

            if ($category && $category->is_quick) {
                $orderType = 'Taiz';
                break;
            }
        }

        $order->type = $orderType;

        $order->save();

        $subtotal = 0;
        $tax = 0;
        $shipping = 0;
        $coupon_discount = 0;

        //Order Details Storing
        foreach ($seller_product as $cartItem) {
            $product = Product::find($cartItem['product_id']);

            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
            $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
            $coupon_discount += $cartItem['discount'];

            $product_variation = $cartItem['variation'];

            $product_stock = $product->stocks->where('variant', $product_variation)->first();
            if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
                $order->delete();
                $combined_order->delete();
                return response()->json([
                    'combined_order_id' => 0,
                    'result' => false,
                    'message' => translate('The requested quantity is not available for ') . $product->name
                ]);
            } elseif ($product->digital != 1) {
                $product_stock->qty -= $cartItem['quantity'];
                $product_stock->save();
            }

            $order_detail = new OrderDetail;
            $order_detail->order_id = $order->id;
            $order_detail->seller_id = $product->user_id;
            $order_detail->product_id = $product->id;
            $order_detail->variation = $product_variation;
            $order_detail->price = cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
            $order_detail->tax = cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
            $order_detail->shipping_type = $cartItem['shipping_type'];
            $order_detail->product_referral_code = $cartItem['product_referral_code'];
            
            $order_detail->shipping_cost = 0; 

            $shipping += $order_detail->shipping_cost;

            if (addon_is_activated('club_point')) {
                $order_detail->earn_point = $product->earn_point;
            }

            $order_detail->quantity = $cartItem['quantity'];
            $order_detail->save();

            $product->num_of_sale = $product->num_of_sale + $cartItem['quantity'];
            $product->save();

            $order->seller_id = $product->user_id;
            if($orderType == 'Taiz') {
                $order->delivery_status ='confirmed';
            }
            $order->shipping_type = $cartItem['shipping_type'];
            if ($cartItem['shipping_type'] == 'pickup_point') {
                $order->pickup_point_id = $cartItem['pickup_point'];
            }
            if ($cartItem['shipping_type'] == 'carrier') {
                $order->carrier_id = $cartItem['carrier_id'];
            }

            if ($product->added_by == 'seller' && $product->user->seller != null) {
                $seller = $product->user->seller;
                $seller->num_of_sale += $cartItem['quantity'];
                $seller->save();
            }

            if (addon_is_activated('affiliate_system')) {
                if ($order_detail->product_referral_code) {
                    $referred_by_user = User::where('referral_code', $order_detail->product_referral_code)->first();

                    $affiliateController = new AffiliateController;
                    $affiliateController->processAffiliateStats($referred_by_user->id, 0, $order_detail->quantity, 0, 0);
                }
            }
        }

       $order->grand_total = $subtotal + $tax + get_setting('platform_fee');

        if ($seller_product[0]->coupon_code != null) {
            $order->coupon_discount = $coupon_discount;
            $order->grand_total -= $coupon_discount;

            $coupon_usage = new CouponUsage;
            $coupon_usage->user_id = $user->id;
            $coupon_usage->coupon_id = Coupon::where('code', $seller_product[0]->coupon_code)->first()->id;
            $coupon_usage->save();
        }

        $combined_order->grand_total += $order->grand_total;

        if (strpos($request->payment_type, "manual_payment_") !== false) { // if payment type like  manual_payment_1 or  manual_payment_25 etc)
            $order->manual_payment = 1;
            $order->save();
        }

        $order->save();

        $this->sendNewOrderNotification($order, $order->seller_id );
    }
    
    $combined_order->shipping_cost = $request->shipping_cost;
    $combined_order->grand_total += $request->shipping_cost;
    $combined_order->save();

    Cart::where('user_id', auth()->user()->id)->active()->delete();

    if (
        $request->payment_type == 'cash_on_delivery'
        || $request->payment_type == 'wallet'
        || strpos($request->payment_type, "manual_payment_") !== false
    ) {
        NotificationUtility::sendOrderPlacedNotification($order);
    }
    
    $combine_order = CombinedOrder::findOrFail($combined_order->id);
    $first_order = $combine_order->orders->first();


    $shipping_cost = $request->shipping_cost; 
    $price = 0;
    $tax = 0;
    $coupon_discount = 0;
    
    // Don't add individual item shipping costs since we're using total shipping
    foreach($combine_order->orders as $order){
        foreach ($order->orderDetails as $orderDetail){
            // $shipping_cost += $orderDetail->shipping_cost; // Remove this line
            $price += $orderDetail->price;
            $tax += $orderDetail->tax;
            $coupon_discount += $orderDetail->coupon_discount;
        }
    }
    
    $total_platform_fees = get_setting('platform_fee');

    // Prepare order details
    $order_details = [];
    foreach ($combine_order->orders as $uperkey => $order) {
        foreach ($order->orderDetails as $key => $orderDetail) {
            $order_details[] = [
                'index' => $uperkey + 1,
                'product_name' => $orderDetail->product ? $orderDetail->product->getTranslation('name') : 'Product Unavailable',
                // 'combo_title' => $orderDetail->combo_id ? \App\ComboProduct::findOrFail($orderDetail->combo_id)->combo_title : null,
                'variation' => $orderDetail->variation != null ? $orderDetail->variation : '',
                'quantity' => $orderDetail->quantity,
                'delivery_type' => $order->shipping_type == 'home_delivery' ? 'Home Delivery' :
                                   ($order->shipping_type == 'carrier' ? 'Carrier' :
                                   ($order->shipping_type == 'pickup_point' ?
                                    ($order->pickup_point ? $order->pickup_point->getTranslation('name') . ' (Pickup Point)' : 'Pickup Point') : '')),
                'order_code' => $order->code,
                'price' => single_price($orderDetail->price)
            ];
        }
    }
     
    // Prepare shipping address
    $shipping_address = json_decode($first_order->shipping_address);

    return response()->json([
        'result' => true,
        'message' => translate('Your order has been placed successfully'),
        'comingFrom' => true,
        'combined_order_id' => $first_order->id,
        'order_summary' => [
            'combined_order_id' => $combine_order->id,
            'order_date' => date('d-m-Y H:i A', $first_order->date),
            'name' => $shipping_address->name,
            'email' => $shipping_address->email,
            'shipping_address' => $shipping_address->address . ', ' . $shipping_address->city,
            'order_status' => translate(ucfirst(str_replace('_', ' ', $first_order->delivery_status))),
            'payment_method' => translate(ucfirst(str_replace('_', ' ', $first_order->payment_type))),
        ],
        'order_details' => $order_details,
        'order_totals' => [
            'subtotal' => single_price($price),
            'shipping' => single_price($combine_order->shipping_cost), 
            'tax' => single_price($tax),
            'coupon_discount' => single_price($coupon_discount),
            'platform_fees' => single_price($total_platform_fees),
            'total' => single_price($combine_order->grand_total)
        ]
    ]);
}

      private function sendNewOrderNotification(Order $order, $sellerId)
{
    \Log::info("Attempting to send notification to Seller boy ID: {$sellerId}");

    $seller = User::find($sellerId);
    if ($seller && get_setting('google_firebase') == 1 && $seller->device_token != null) {
        $notification = new \stdClass();
        $notification->device_token = $seller->device_token;
        $notification->title = 'New Order Available!';
        $notification->text = "You have a new order";
        $notification->type = 'order';
        $notification->id = $order->id;
        $notification->user_id = $seller->id;
        $notification->for_type = "seller";

        $result = NotificationUtility::sendFirebaseNotification($notification);
        \Log::info("Notification sent to Seller boy ID: {$sellerId}, Result: " . ($result ? 'Success' : 'Failed'));

        return $result;
    }

    \Log::warning("Unable to send notification to Seller boy ID: {$sellerId}");
    return false;
}

    public function order_cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)->where('user_id', auth()->user()->id)->first();

        if ($order && ($order->delivery_status == 'pending' && $order->payment_status == 'unpaid')) {
            $order->delivery_status = 'cancelled';
            $order->save();

            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->delivery_status = 'cancelled';
                $orderDetail->save();
                product_restock($orderDetail);
            }

            // Send notification to customer
            if ($request != null && get_setting('google_firebase') == 1 && $order->user->device_token != null) {
                $customerNotification = new \stdClass();
                $customerNotification->device_token = $order->user->device_token;
                $customerNotification->title = "Order Canceled!";
                $customerNotification->text = "Your order {$order->code} has been canceled successfully.";
                $customerNotification->type = "order";
                $customerNotification->id = $order->id;
                $customerNotification->user_id = $order->user->id;

                NotificationUtility::sendFirebaseNotification($customerNotification);
            }

            // Send notification to seller
            $seller = User::find($order->seller_id);
            if ($seller && get_setting('google_firebase') == 1 && $seller->device_token != null) {
                $sellerNotification = new \stdClass();
                $sellerNotification->device_token = $seller->device_token;
                $sellerNotification->title = "Order Canceled!";
                $sellerNotification->text = "An order {$order->code} has been canceled by the customer.";
                $sellerNotification->type = "order";
                $sellerNotification->id = $order->id;
                $sellerNotification->user_id = $seller->id;

                NotificationUtility::sendFirebaseNotification($sellerNotification);
            }

            return $this->success(translate('Order has been canceled successfully'));
        } else {
            return $this->failed(translate('Something went wrong'));
        }
    }


    public function getDeliveryBoy($id){
        $order = Order::where('id', $id)->whereIn('delivery_status', ['confirmed', 'picked_up', 'on_the_way'])->first();
        $isReviewed = false;

        if($order && $order->assign_delivery_boy != null){
            $delivery_boy = User::where('id', $order->assign_delivery_boy)->first();
            $delivery_boy_details = DeliveryBoy::where('user_id', $order->assign_delivery_boy)->first();
            $isReviewed = Rating::where('delivery_boy_id', $delivery_boy_details->id)->where('user_id', auth()->user()->id)->where('order_id', $id)->exists();


            return response()->json([
                'result' => true,
                'message' => 'Delivery Boy assigned to this order',
                'delivery_boy_detail' => [
                    'id'=> $delivery_boy->id,
                    'name' => $delivery_boy->name,
                    'phone' => $delivery_boy->phone,
                    'image' => $delivery_boy->avatar_original != null ? uploaded_asset($delivery_boy->avatar_original) : '',
                    'rating' => format_float($delivery_boy_details->rating, 2),
                    'latitude' => format_float($delivery_boy_details->lat, 7),
                    'longitude' => format_float($delivery_boy_details->lng, 7),
                    'isReviewed' => $isReviewed
                ]
            ], 200, [], JSON_PRESERVE_ZERO_FRACTION);
        }else{
            return response()->json([
                'result' => false,
                'message' => translate('No Delivery Boy assigned to this order '),
                'delivery_boy_detail' =>[
                    'id'=> 0,
                    'name' => '',
                    'phone' => '',
                    'image' => '',
                    'rating'=> 0.00,
                    'latitude' =>0.00,
                    'longitude' =>0.00,
                    'isReviewed' => $isReviewed
                ]

            ]);
        }
    }
}
