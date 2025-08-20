<?php

namespace App\Http\Controllers;

use Auth;
use Mail;
use App\Models\Cart;
use App\Models\Shop;
use App\Models\User;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Product;
use App\Models\CouponUsage;
use App\Models\DeliveryBoy;
use App\Models\OrderDetail;
use App\Models\SmsTemplate;
use App\Utility\SmsUtility;
use OrderOfferNotification;
use App\Models\OrdersExport;
use CoreComponentRepository;
use Illuminate\Http\Request;
use MercadoPago\Config\Json;
use App\Models\CombinedOrder;
use App\Jobs\AssignDeliveryBoy;
use App\Models\BusinessSetting;
use App\Mail\InvoiceEmailManager;
use Illuminate\Http\JsonResponse;
use App\Utility\NotificationUtility;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Mail\SecondEmailVerifyMailManager;
use App\Http\Controllers\AffiliateController;

class OrderController extends Controller
{

    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_orders|view_inhouse_orders|view_seller_orders|view_pickup_point_orders'])->only('all_orders');
        $this->middleware(['permission:view_order_details'])->only('show');
        $this->middleware(['permission:delete_order'])->only('destroy', 'bulk_order_delete');
    }

    // All Orders
    public function all_orders(Request $request)
    {

        CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $sort_search = null;
        $delivery_status = null;
        $payment_status = '';

        $orders = Order::orderBy('id', 'desc');
        $admin_user_id = User::where('user_type', 'admin')->first()->id;


        if (
            Route::currentRouteName() == 'inhouse_orders.index' &&
            Auth::user()->can('view_inhouse_orders')
        ) {
            $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
        } else if (
            Route::currentRouteName() == 'seller_orders.index' &&
            Auth::user()->can('view_seller_orders')
        ) {
            $orders = $orders->where('orders.seller_id', '!=', $admin_user_id);
        } else if (
            Route::currentRouteName() == 'pick_up_point.index' &&
            Auth::user()->can('view_pickup_point_orders')
        ) {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
            }
            $orders->where('shipping_type', 'pickup_point')->orderBy('code', 'desc');
            if (
                Auth::user()->user_type == 'staff' &&
                Auth::user()->staff->pick_up_point != null
            ) {
                $orders->where('shipping_type', 'pickup_point')
                    ->where('pickup_point_id', Auth::user()->staff->pick_up_point->id);
            }
        } else if (
            Route::currentRouteName() == 'all_orders.index' &&
            Auth::user()->can('view_all_orders')
        ) {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
            }
        } else {
            abort(403);
        }

        if ($request->search) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($date != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])) . '  00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])) . '  23:59:59');
        }
        $orders = $orders->paginate(15);

        return view('backend.sales.index', compact('orders', 'sort_search', 'payment_status', 'delivery_status', 'date'));
    }

    public function show($id)
    {
        // dd($id);

        $order = Order::findOrFail(decrypt($id));

        $order_shipping_address = json_decode($order->shipping_address);
        // dd($order);
        // if(!$order_shipping_address){
        //     flash(translate('Shipping Address in empty'))->error();
        //     return redirect()->back();
        // }
        // $delivery_boys = User::where('city', $order_shipping_address->city)
        // ->where('user_type', 'delivery_boy')
        // ->whereIn('id', function ($query) {
        //     $query->select('user_id')->from('delivery_boys')->where('status', 1);
        // })
        // ->get();
        $delivery_boys = User::
        where('user_type', 'delivery_boy')
        ->whereIn('id', function ($query) {
            $query->select('user_id')->from('delivery_boys')->where('status', 1);
        })
        ->get();

        if (env('DEMO_MODE') == 'On') {
            $order->viewed = 1;
            $order->save();
        }

        $platform_fee = BusinessSetting::where('type','platform_fee')->first();

        return view('backend.sales.show', compact('order', 'delivery_boys', 'platform_fee'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)->active()->get();

        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $address = Address::where('id', $carts[0]['address_id'])->first();

        $shippingAddress = [];
        if ($address != null) {
            $shippingAddress['name']        = Auth::user()->name;
            $shippingAddress['email']       = Auth::user()->email;
            $shippingAddress['address']     = $address->address;
            // $shippingAddress['country']     = $address->country->name;
            // $shippingAddress['state']       = $address->state->name;
            // $shippingAddress['city']        = $address->city->name;
            $shippingAddress['city']        = $address->city_name;

            // $shippingAddress['postal_code'] = $address->postal_code;
            $shippingAddress['phone']       = $address->phone;
            if ($address->latitude || $address->longitude) {
                $shippingAddress['lat_lang'] = $address->latitude . ',' . $address->longitude;
            }
        }

        $combined_order = new CombinedOrder;
        $combined_order->user_id = Auth::user()->id;
        $combined_order->shipping_address = json_encode($shippingAddress);
        $combined_order->save();

        $seller_products = array();
        foreach ($carts as $cartItem) {
            $product_ids = array();
            $product = Product::find($cartItem['product_id']);
            if (isset($seller_products[$product->user_id])) {
                $product_ids = $seller_products[$product->user_id];
            }
            array_push($product_ids, $cartItem);
            $seller_products[$product->user_id] = $product_ids;
        }

        foreach($seller_products as $key => $seller_product) {
            $sellerId = $key;
            break;
        }
        $shop = Shop::where('user_id', $sellerId)->first();
        $shop->delivery_pickup_latitude = $shop->delivery_pickup_latitude;
        $shop->delivery_pickup_longitude = $shop->delivery_pickup_longitude;
        $user_address = Address::where('user_id', auth()->user()->id)
            ->where('set_default', 1)
            ->first();
            // dd($user_address->longitude);
            $user_address->long = $user_address->longitude;
            $user_address->lat = $user_address->latitude;

        $newCost = false;

        if($user_address !=null && $shop->delivery_pickup_latitude != null && $shop->delivery_pickup_longitude != null && $user_address->latitude != null && $user_address->longitude != null){

            // $distance = customer_shop_distance($shop->delivery_pickup_latitude,$shop->delivery_pickup_longitude, $user_address->latitude, $user_address->longitude);
            $distance = getMultipleRoutes($shop->delivery_pickup_latitude,$shop->delivery_pickup_longitude,$user_address->latitude, $user_address->longitude);

                if (!empty($distance)) {
                    $bestRoute = collect($distance)->sortBy('distance_km')->first();

                    $bestDistance = (float) $bestRoute['distance_km'];
                } else {
                    $bestDistance = 0;
                }

                $per_km = get_setting('per_km');
                // return $per_km;
                $shipping_cost = get_setting('flat_rate_shipping_cost');


                $per_km_cost = $shipping_cost/(float) $per_km;

                if ($bestDistance > (float) $per_km) {
                    $extra_km = $bestDistance - (float) $per_km;
                    $shipping_cost += (float) ($extra_km * (float) $per_km_cost);
                    $newCost = true;
                }


        }

        foreach ($seller_products as $seller_product) {
            $order = new Order;
            $order->combined_order_id = $combined_order->id;
            $order->user_id = Auth::user()->id;
            $order->shipping_address = $combined_order->shipping_address;
            $order->additional_info = $request->additional_info;
            $order->payment_type = $request->payment_option;
            $order->delivery_status = 'confirmed';
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His') . rand(10, 99);
            $order->date = strtotime('now');
            $order->save();

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            $coupon_discount = 0;

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

            //Order Details Storing
            foreach ($seller_product as $cartItem) {
                $product = Product::find($cartItem['product_id']);

                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $tax +=  cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $coupon_discount += $cartItem['discount'];

                $product_variation = $cartItem['variation'];

                $product_stock = $product->stocks->where('variant', $product_variation)->first();
                if ($product->digital != 1 && $cartItem['quantity'] > $product_stock->qty) {
                    flash(translate('The requested quantity is not available for ') . $product->getTranslation('name'))->warning();
                    $order->delete();
                    return redirect()->route('cart')->send();
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
                $order_detail->shipping_cost = $cartItem['shipping_cost'];

                $shipping += $order_detail->shipping_cost;
                //End of storing shipping cost

                $order_detail->quantity = $cartItem['quantity'];

                if (addon_is_activated('club_point')) {
                    $order_detail->earn_point = $product->earn_point;
                }

                $order_detail->save();

                $product->num_of_sale += $cartItem['quantity'];
                $product->save();

                $order->seller_id = $product->user_id;
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
            $platform = BusinessSetting::where('type', 'platform_fee')->first();
            $platform_fees = $platform->value;
            $order->grand_total = $subtotal + $tax + ($newCost ? $shipping_cost : $shipping) + $platform_fees;

            if ($seller_product[0]->coupon_code != null) {
                $order->coupon_discount = $coupon_discount;
                $order->grand_total -= $coupon_discount;

                $coupon_usage = new CouponUsage;
                $coupon_usage->user_id = Auth::user()->id;
                $coupon_usage->coupon_id = Coupon::where('code', $seller_product[0]->coupon_code)->first()->id;
                $coupon_usage->save();
            }

            $combined_order->grand_total += $order->grand_total;

            $combined_order->shipping_cost = $newCost ? $shipping_cost : $shipping;
            // if($orderType == 'Tiaz') {
            //     AssignDeliveryBoy::dispatch($order)->delay(now()->addSeconds(10));
            // }
            $order->save();

        }
        // $platform = BusinessSetting::where('type', 'platform_fee')->first();
        // $platform_fees = $platform->value;
        // $combined_order->grand_total += $platform_fees;
        $combined_order->save();



        $request->session()->put('combined_order_id', $combined_order->id);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            $order->commissionHistory()->delete();
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {
                    product_restock($orderDetail);
                } catch (\Exception $e) {
                }

                $orderDetail->delete();
            }
            $order->delete();
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function bulk_order_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $this->destroy($order_id);
            }
        }

        return 1;
    }

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('seller.order_details_seller', compact('order'));
    }

    public function update_delivery_status(Request $request)
    {

        $order = Order::findOrFail($request->order_id);
        // return $order->shop;
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $user_name = $order->user->name;
        $order_code = $order->code;
        $total_prdouct = 0;

        $product_names = $order->orderDetails->map(function ($orderDetail) use (&$quantity, &$total_prdouct)  {
            $quantity += $orderDetail->quantity;
            $total_prdouct += 1;
            return $orderDetail->product->name;  // Access the product name through the product relationship
        })->join(', ');

        $order_category = $order->orderDetails->map(function ($orderDetail)   {

            return $orderDetail->product->main_category->is_quick;  // Access the product name through the product relationship
        });

        $delivery_time = $order_category[0] ? 'Deliver will be in 1 hours' : '(Within 7 working days)';
        $shipping_fee = BusinessSetting::where('type', 'flat_rate_shipping_cost')->first();
        $shipping_data = json_decode($order->shipping_address, true);
        $shipping_address = $shipping_data['address'];
        // $order_discount;
        // dd($shipping_address);

        if($request->status == 'confirmed') {

            $array['view'] = 'emails.verification';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = translate('Your Shopeedo Order Confirmation code is ' . $order_code );
            $array['content'] = 'Hi ' . $user_name  . ',<br><br>
            Thank you for shopping with Shopeedo! We are excited to inform you that your order '.$order_code. ' has been successfully placed. We will notify you as soon as your package is on its way.
            <br><br>
            You can check the status of your order using the tracking link below and enable push notifications on the Shopeedo App to receive real-time updates.
            <br><br>
            <strong>TRACK MY ORDER</strong>
            <br><br>
            <a href="{{route("orders.track")}}">TRACKING LINK</a>
            <br><br>
            <strong>Delivery Details: (CLIENTS INFO)</strong>
            <br>
            <ul>
            <li><strong>Name:</strong> '.$order->user->name.'</li>
            <li><strong>Address:</strong> '.$shipping_address.'</li>
            <li><strong>Phone:</strong> '.$order->user->phone.'</li>
            <li><strong>Email:</strong> '.$order->user->email.'</li>
            </ul>
            <br><br>
            <strong>Order Details:</strong>
            <br>
            <ul>
            <li><strong>Item:</strong> '.$total_prdouct.'</li>
            <li><strong>Sold by:</strong> '.$order->shop->name.'</li>
            <li><strong>Estimated delivery:</strong> '. $delivery_time .'</li>
            <li><strong>Product:</strong> '.$product_names.'</li>
            <li><strong>Price:</strong> '.$order->combinedOrder->grand_total.'</li>
            <li><strong>Quantity:</strong> '.$quantity.'</li>
            </ul>
            <strong>Order Summary:</strong>
             <br>
            <ul>
            <li><strong>Order Total:</strong> '.$order->combinedOrder->grand_total.'</li>
            <li><strong>Delivery Fee:</strong> '.$shipping_fee->value.'</li>
            <li><strong>Total Discount:</strong> '.$order->coupon_discount.'</li>
            <li><strong>Total Payment (GST Incl):</strong> '.$order->combinedOrder->grand_total.'</li>
            <li><strong>Delivery Method:</strong> '.' standard '.'</li>
            <li><strong>Paid By:</strong> '.$order->payment_type.'</li>
            </ul>
            <br><br>
            <strong>Note: </strong>A cash payment fee has been applied to your total payment amount for Cash on Delivery orders. Learn more here.
            <br>
            <a href="{{route("privacypolicy")}}" style="text-align:center">Policy Link</a>
            <br><br>
            <strong>Need Help?</strong>
            <br><br>
            <strong>When will I receive my order?</strong>
            <br>
            To track your order, go to Account > My Orders > Select the order you want to track > Track Package to check the status and estimated delivery date.
            <br><br>
            <strong>How can I cancel my order?</strong>
            <br>
            You can cancel your order before it is shipped by going to Account > View All > Select Order > Cancel. If your order has already been shipped, please chat with us for further assistance.
            <br><br>
            <strong>Will I be contacted before delivery?</strong>
            <br>
            Our courier partner may contact you to confirm your address before delivery.
            <br><br>
            <strong>Can I change my payment method from COD to prepayment?</strong>
            <br>
            Once an order is placed, the payment method cannot be changed. However, you can pay at the doorstep using your bank debit or credit card through the Tap & Pay service on the Shopeedo riders device. Learn more Payment Options.
            <br><br>
            <strong>Can I update my delivery address after placing an order? </strong>
            <br>
            Yes, you can update your delivery address before the order is shipped by going to Account > View All > Select Order > Edit Address. If your order has already been shipped, please contact our customer support for assistance.
            <br><br>
            <strong>What should I do if I receive a damaged or incorrect item? </strong>
            <br>
            If you receive a damaged or incorrect item, please contact our customer support within 7 days of delivery. Go to Account > My Orders > Select the order > Report an Issue and our team will assist you with the return or exchange process.
            <br><br>
            <strong style="text-align:center">Still have questions?</strong>
            <br>
            <p style="text-align:center">Visit our Help Center or check our Return Policy.</p>
            <strong>Important Note: </strong>
            Please make sure all transactions are made through the Shopeedo platform. If a seller asks you to pay off-site or through an alternative channel, do not send them money and report the matter to us immediately.
             <br>
            <strong>Thank you for choosing Shopeedo. We look forward to serving you again!</strong>
            <br>
            <strong>Best regards,</strong>
            <br>
            The Shopeedo Support Team
            ';
            Mail::to($order->user->email)->queue(new SecondEmailVerifyMailManager($array));
        }

        if($request->has('reason')){
            $order->cancel_reason = $request->reason;
            $array['view'] = 'emails.verification';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = translate('Your Order with Shopeedo Has Been Cancelled');
            $order_shop_name = $order->shop->name;
             $quantity = 0;



            $array['content'] = 'Hi ' . $user_name  . ',<br><br>
            We regret to inform you that your recent order ' . $order_code   . ' has been cancelled.
            We understand how disappointing this news might be and we sincerely apologize for any inconvenience this may have caused.
            <br><br>
            <strong>Product:</strong> ' . $product_names . '<br><br>
            <strong>Quantity:</strong> ' . $quantity . '<br><br>
            <strong>Seller:</strong> ' . $order_shop_name . '<br><br>
            <strong>Why Was My Order Cancelled?</strong> <br><br>
            You can find the specific reason for your order cancellation below:
            <br><br>
            <h4 style="text-align:center">' . $request->reason . '</h4>
            <br><br>

            <strong>Payment Issue:</strong> Because of a problem with your payment methods such as a declined credit card, insufficient funds, or an issue with the payment processor, we cancelled your order. You can buy the order again after selecting the correct payment method.
            <br><br>

            <strong>Incorrect Pricing:</strong> Due to a pricing error on our website, the price listed for the item you ordered was incorrect. To ensure fairness, we have cancelled your order. You are welcome to place a new order once the correct pricing is updated. We apologize for any inconvenience this may have caused.
            <br><br>

            <strong>Item Out of Stock:</strong> Sorry to say that the item you ordered is out of stock, so we automatically cancelled your order. This can occur due to high demand or inventory errors. You can replace it with another order. HAPPY SHOPPING.
            <br><br>

            <strong>Fraud Detection:</strong> Shopeedo detects suspicious activity from your account. We cannot verify your identity, so we might cancel the order to protect you and the platform from potential fraud. You can verify your identity by adding your phone number and CNIC.
            <br><br>

            <strong>Shipping Problems:</strong> We may cancel your order due to shipment issues. Problems can include incorrect addresses, sourcing delays, damaged goods, customs problems, customer refusals, transit delays, technical problems, driver shortages, or bad weather conditions. You can shop on Shopeedo after a few days. We are trying our best to solve this problem.
            <br><br>

            <strong>Technical Issue:</strong> We apologize to inform you that your order has been cancelled due to a systematic error on our part. I hope you understand our situation, and our problem will be resolved soon. Then, you can place your order again.
            <br><br>

            <strong>What Happens Next?</strong><br><br>
            If you had pre-paid for your order, rest assured that your refund is being processed automatically.
            <br><br>

            <strong>Voucher Refunds:</strong><br><br>
            If your order was placed through a voucher, we will take care of it and it will be returned to your Shopeedo wallet after the cancellation process is complete.
            <br><br>

            <strong>Need Assistance?</strong><br><br>
            We’re here to help! If you have any questions or need further assistance, please visit our Shopeedo Help Center. You can also find detailed information about how to place a new order, our return policy, and more.
            <br><br>

            <strong>Important Notes:</strong><br><br>
            <ul>
            <li>For your safety, we ensure that all transactions are made directly through the Shopeedo platform.</li>
            <li>If a seller requests payment through another channel, please do not proceed and report the issue to us immediately.</li>
            </ul>
            We appreciate your understanding and hope to serve you better in the future.';

            Mail::to($order->user->email)->queue(new SecondEmailVerifyMailManager($array));
        }


        $order->save();
        // dd($order->delivery_boy->id);

        // $completed_rides = DeliveryBoy::where('user_id',$order->delivery_boy->id)->pluck('completed_rides')->first();

        // $completed_rides = $completed_rides+1;

        // $delivery_boy = DeliveryBoy::where('user_id',$order->delivery_boy->id);

        // $delivery_boy->update([
        //     "completed_rides" => $completed_rides
        // ]);

        // Retrieve and increment completed_rides in one step





        if ($order->delivery_status === 'delivered') {
            if($order->delivery_boy != null)
            {
            $delivery_boy = DeliveryBoy::where('user_id', $order->delivery_boy->id)->first();
            $delivery_boy->increment('completed_rides');
            }
           
            $array['view'] = 'emails.verification';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['subject'] = translate('Your Shopeedo Order Is Delivered, Order code is ' . $order_code );
            $array['content'] = 'Hi ' . $user_name  . ',<br><br>
            Thank you for shopping with Shopeedo! We are excited to inform you that your order '.$order_code. ' has been successfully Delivered.
            <br><br>
            <strong>Delivery Details: (CLIENTS INFO)</strong>
            <br>
            <ul>
            <li><strong>Name:</strong> '.$order->user->name.'</li>
            <li><strong>Address:</strong> '.$shipping_address.'</li>
            <li><strong>Phone:</strong> '.$order->user->phone.'</li>
            <li><strong>Email:</strong> '.$order->user->email.'</li>
            </ul>
            <br><br>
            <strong>Order Details:</strong>
            <br>
            <ul>
            <li><strong>Item:</strong> '.$total_prdouct.'</li>
            <li><strong>Sold by:</strong> '.$order->shop->name.'</li>
            <li><strong>Estimated delivery:</strong> '. $delivery_time .'</li>
            <li><strong>Product:</strong> '.$product_names.'</li>
            <li><strong>Price:</strong> '.$order->combinedOrder->grand_total.'</li>
            <li><strong>Quantity:</strong> '.$quantity.'</li>
            </ul>
            <strong>Order Summary:</strong>
             <br>
            <ul>
            <li><strong>Order Total:</strong> '.$order->combinedOrder->grand_total.'</li>
            <li><strong>Delivery Fee:</strong> '.$shipping_fee->value.'</li>
            <li><strong>Total Discount:</strong> '.$order->coupon_discount.'</li>
            <li><strong>Total Payment (GST Incl):</strong> '.$order->combinedOrder->grand_total.'</li>
            <li><strong>Delivery Method:</strong> '.' standard '.'</li>
            <li><strong>Paid By:</strong> '.$order->payment_type.'</li>
            </ul>
            <br><br>
            <a href="{{route("privacypolicy")}}" style="text-align:center">Policy Link</a>
            <br><br>
            <strong>Need Help?</strong>
            <br><br>
            <p style="text-align:center">Visit our Help Center or check our Return Policy.</p>
            <strong>Important Note: </strong>
            Please make sure all transactions are made through the Shopeedo platform. If a seller asks you to pay off-site or through an alternative channel, do not send them money and report the matter to us immediately.
             <br>
            <strong>Thank you for choosing Shopeedo. We look forward to serving you again!</strong>
            <br>
            <strong>Best regards,</strong>
            <br>
            The Shopeedo Support Team
            ';
            Mail::to($order->user->email)->queue(new SecondEmailVerifyMailManager($array));
     

            // Check if the delivery boy exists, then increment and update


            $order_details = OrderDetail::with('product')->where('order_id', $request->order_id)->get();
            foreach ($order_details as $order_detail) {
                if ($order_detail->product->current_stock < 2000) {
                    $product_name = $order_detail->product->name;
                    $seller = User::findOrFail($order_detail->seller_id);
                    $email = $seller->email;
                    $seller_name =  $seller->name;
                    sendLowStockProductEmail($product_name, $seller_name, $email);
                }
            }

        }


        if ($request->status == 'cancelled' && $order->payment_type == 'wallet') {
            $user = User::where('id', $order->user_id)->first();
            $user->balance += $order->grand_total;
            $user->save();
        }

        // If the order is cancelled and the seller commission is calculated, deduct seller earning
        if ($request->status == 'cancelled' && $order->user->user_type == 'seller' && $order->payment_status == 'paid' && $order->commission_calculated == 1) {
            $sellerEarning = $order->commissionHistory->seller_earning;
            $shop = $order->shop;
            $shop->admin_to_pay -= $sellerEarning;
            $shop->save();
        }

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    product_restock($orderDetail);
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {

                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    product_restock($orderDetail);
                }

                if (addon_is_activated('affiliate_system')) {
                    if (($request->status == 'delivered' || $request->status == 'cancelled') &&
                        $orderDetail->product_referral_code
                    ) {

                        $no_of_delivered = 0;
                        $no_of_canceled = 0;

                        if ($request->status == 'delivered') {
                            $no_of_delivered = $orderDetail->quantity;
                        }
                        if ($request->status == 'cancelled') {
                            $no_of_canceled = $orderDetail->quantity;
                        }

                        $referred_by_user = User::where('referral_code', $orderDetail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, 0, $no_of_delivered, $no_of_canceled);
                    }
                }
            }
        }
        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'delivery_status_change')->first()->status == 1) {
            try {
                SmsUtility::delivery_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {
            }
        }

        //sends Notifications to user
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->delivery_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('delivery_boy')) {
            // if (Auth::user()->user_type == 'delivery_boy') {
                $deliveryBoyController = new DeliveryBoyController;
                $deliveryBoyController->store_delivery_history($order);
            // }
        }

        return 1;
    }

    public function update_tracking_code(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->tracking_code = $request->tracking_code;
        $order->save();

        return 1;
    }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if (
            $order->payment_status == 'paid' &&
            $order->commission_calculated == 0
        ) {
            calculateCommissionAffilationClubPoint($order);
        }

        //sends Notifications to user
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->payment_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
            try {
                SmsUtility::payment_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }

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

                try {
                    Mail::to($order->delivery_boy->email)->queue(new InvoiceEmailManager($array));

                    if(get_setting('google_firebase') == 1 && $order->delivery_boy->device_token != null) {
                        $request->device_token = $order->delivery_boy->device_token;
                        $request->title = "Order Assigned !";
                        // $status = str_replace("_", "", $order->payment_status);
                        $request->text = " You are assigned to delivery an order.  Order code {$order->code} ";

                        $request->type = "order";
                        $request->id = $order->id;
                        $request->user_id = $order->delivery_boy->id;

                        NotificationUtility::sendFirebaseNotification($request);
                    }


                } catch (\Exception $e) {
                }
            }

            if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'assign_delivery_boy')->first()->status == 1) {
                try {
                    SmsUtility::assign_delivery_boy($order->delivery_boy->phone, $order->code);
                } catch (\Exception $e) {
                }
            }
        }

        return 1;
    }

    public function autoAssignDeliveryBoy($order)
    {

        $shop = \DB::table('shops')->where('user_id', $order->seller_id)->first();
        if (!$shop) {
            return null;
        }
        $deliveryBoy = \DB::table('delivery_boys')
            ->join('users', 'delivery_boys.user_id', '=', 'users.id')
            ->select(
                'delivery_boys.id',
                'users.name',
                'delivery_boys.rating',
                \DB::raw('ST_Distance_Sphere(point(delivery_boys.lng, delivery_boys.lat), point(?, ?)) as distance')
            )
            ->where('users.banned', 0)
            ->whereNotIn('delivery_boys.id', function ($query) {
                $query->select('assign_delivery_boy')->from('orders')->whereNotNull('assign_delivery_boy');
            })
            ->orderBy('distance', 'asc')
            ->orderBy('delivery_boys.rating', 'desc')
            ->setBindings([
                $shop->delivery_pickup_longitude, // Longitude of the shop
                $shop->delivery_pickup_latitude,  // Latitude of the shop
            ], 'select')
            ->first();

            return $deliveryBoy;

        if ($deliveryBoy) {
            $order->assign_delivery_boy = $deliveryBoy->id;
            $order->delivery_history_date = now();
            $order->save();

            $this->logDeliveryHistory($order, $deliveryBoy->id);
            return $deliveryBoy;
        }

        return null;
    }


private function logDeliveryHistory($order, $deliveryBoyId)
{
    $deliveryHistory = \App\Models\DeliveryHistory::firstOrNew([
        'order_id' => $order->id,
        'delivery_status' => $order->delivery_status,
    ]);

    $deliveryHistory->delivery_boy_id = $deliveryBoyId;
    $deliveryHistory->payment_type = $order->payment_type;
    $deliveryHistory->save();
}



    public function orderBulkExport(Request $request)
    {
        if ($request->id) {
            return Excel::download(new OrdersExport($request->id), 'orders.xlsx');
        }
        return back();
    }

    public function getDeliveryBoys($order_id)
{
    $order = Order::findOrFail($order_id);
    $order_shipping_address = json_decode($order->shipping_address);

    $delivery_boys = User::where('city', $order_shipping_address->city)
        ->where('user_type', 'delivery_boy')
        ->whereIn('id', function ($query) {
            $query->select('user_id')->from('delivery_boys')->where('status', 1);
        })
        ->get();

    return response()->json([
        'delivery_boys' => $delivery_boys,
        'assigned_delivery_boy' => $order->assign_delivery_boy,
    ]);
}

// public function assignDeliveryBoy(Request $request)
// {
//     $orders = Order::where('delivery_status', 'confirmed')
//         ->where('type', 'Taiz')
//         ->whereNull('assign_delivery_boy')
//         ->get();
//     foreach ($orders as $order) {
//         $deliveryBoyId = getAutoAssignedDeliveryBoy($order);
//         if ($deliveryBoyId != null) {
//             $assignRequest = new \Illuminate\Http\Request([
//                 '_token' => csrf_token(),
//                 'order_id' => $order->id,
//                 'delivery_boy' => $deliveryBoyId,
//             ]);
//             $this->assign_delivery_boy($assignRequest);
//         } else {
//             \Log::info("No delivery boy available for order ID: {$order->id}");
//         }
//     }
//     return response()->json(['message' => 'Delivery boys assigned successfully.']);
// }

// public function assignDeliveryBoy(Request $request)
// {

//     $orders = Order::where('delivery_status', 'confirmed')
//         ->where('type', 'Taiz')
//         ->whereNull('assign_delivery_boy')
//         ->whereNull('offer_expiry_time')
//         ->get();



//     foreach ($orders as $order) {
//         $candidates = $this->getDeliveryBoyCandidates($order);

//         if (!empty($candidates)) {
//             $order->assignment_candidates = json_encode($candidates);
//             $order->offer_expiry_time = now()->addMinutes(5);
//             $order->save();

//             $this->sendOrderOfferToDeliveryBoy($order, $candidates[0]);
//         } else {
//             \Log::info("No delivery boy available for order ID: {$order->id}");
//         }
//     }

//     return response()->json(['message' => 'Order offers sent successfully.']);
// }

public function assignDeliveryBoy(Request $request)
{

    $orders = Order::where('delivery_status', 'confirmed')
        ->where('type', 'Taiz')
        ->whereNull('assign_delivery_boy')
        ->whereNull('offer_expiry_time')
        ->get();

    $assignedCount = 0;
    foreach ($orders as $order) {
        $candidates = $this->getDeliveryBoyCandidates($order);

        if (!empty($candidates)) {
            // Store candidates as a simple JSON array for easier querying
            $order->assignment_candidates = json_encode(array_values($candidates));
            $order->offer_expiry_time = now()->addMinutes(1);
            $order->save();

            $this->sendOrderOfferToDeliveryBoy($order, $candidates[0]);
            $assignedCount++;
        } else {
            \Log::info("No delivery boy available for order ID: {$order->id}");
        }
    }

    return response()->json([
        'message' => 'Order offers sent successfully.',
        'orders_processed' => $orders->count(),
        'offers_sent' => $assignedCount
    ]);
}
private function getDeliveryBoyCandidates($order)
{
    $order_shipping_address = json_decode($order->shipping_address);
    $shop = Shop::where('user_id', $order->seller_id)->first();
    if (!$shop) return [];

    $shopLat = $shop->delivery_pickup_latitude;
    $shopLng = $shop->delivery_pickup_longitude;

    $delivery_boys = User::where('city', $order_shipping_address->city)
        ->where('user_type', 'delivery_boy')
        ->get();

    $deliveryBoys = DeliveryBoy::whereIn('user_id', $delivery_boys->pluck('id'))
        ->where('status', 1)
        ->get();

        $availableRiders = $deliveryBoys->filter(function ($rider) use ($order) {

            if ($rider->isAssignedToOrder()) {
                return false;
            }

            if ($order->payment_type == 'cash_on_delivery') {
                $maxCollection = get_setting('delivery_boy_max_collection');
                if ($maxCollection && $rider->total_collection >= $maxCollection) {
                    return false;
                }
            }

            return true;
        });

    if ($availableRiders->isEmpty()) return [];

    $availableRiders->each(function ($rider) use ($shopLat, $shopLng) {
        $rider->distance_to_shop = calculateDistance($rider->lat, $rider->lng, $shopLat, $shopLng);
    });

    return $availableRiders->sort(function ($a, $b) {
        // Primary sort: Distance to shop (closest first)
        if ($a->distance_to_shop != $b->distance_to_shop) {
            return $a->distance_to_shop <=> $b->distance_to_shop;
        }

        // Secondary sort: Rating-based logic (commented out for later use)
        /*
        if ($a->rating == 0 || $b->rating == 0) {
            // 30% chance to prioritize new rider when distances are equal
            if (mt_rand(1, 100) <= 30) {
                return $b->rating <=> $a->rating; // Puts 0-rated first
            }
        }

        // Tertiary sort: Higher rated riders
        return $b->rating <=> $a->rating;
        */

        // For now, maintain original order when distances are equal
        return 0;
    })->pluck('user_id')->toArray();
}

// private function sendOrderOfferToDeliveryBoy(Order $order, $deliveryBoyId)
// {
//     $deliveryBoy = User::find($deliveryBoyId);
//     if ($deliveryBoy) {
//         if (get_setting('google_firebase') == 1 && $deliveryBoy->device_token != null) {
//             $notificationData = [
//                 'device_token' => $deliveryBoy->device_token,
//                 'title' => 'Order Updated!',
//                 'text' => "New order Available",
//                 'type' => 'order',
//                 'id' => $order->id,
//                 'user_id' => $deliveryBoy->id,
//             ];
//             NotificationUtility::sendFirebaseNotification($notificationData);
//         }
//     }
// }

// private function sendOrderOfferToDeliveryBoy(Order $order, $deliveryBoyId)
// {
//     \Log::info("Notification sent to delivery boy ID: {$deliveryBoyId}, Result: ");
//     $deliveryBoy = User::find($deliveryBoyId);
//     if ($deliveryBoy && get_setting('google_firebase') == 1 && $deliveryBoy->device_token != null) {

//         $notification = new \stdClass();
//         $notification->device_token = $deliveryBoy->device_token;
//         $notification->title = 'New Order Available!';
//         $notification->text = "You have a new order offer to accept";
//         $notification->type = 'order';
//         $notification->id = $order->id;
//         $notification->user_id = $deliveryBoy->id;

//         $result = NotificationUtility::sendFirebaseNotification($notification);
//         \Log::info($result);
//         \Log::info("Notification sent to delivery boy ID: {$deliveryBoyId}, Result: " . ($result ? 'Success' : 'Failed'));
//     }
// }

private function sendOrderOfferToDeliveryBoy(Order $order, $deliveryBoyId)
{
    \Log::info("Attempting to send notification to delivery boy ID: {$deliveryBoyId}");

    $deliveryBoy = User::find($deliveryBoyId);
    if ($deliveryBoy && get_setting('google_firebase') == 1 && $deliveryBoy->device_token != null) {
        $notification = new \stdClass();
        $notification->device_token = $deliveryBoy->device_token;
        $notification->title = 'New Order Available!';
        $notification->text = "You have a new order offer to accept";
        $notification->type = 'order';
        $notification->id = $order->id;
        $notification->user_id = $deliveryBoy->id;

        $result = NotificationUtility::sendFirebaseNotification($notification);
        \Log::info("Notification sent to delivery boy ID: {$deliveryBoyId}, Result: " . ($result ? 'Success' : 'Failed'));

        return $result;
    }

    \Log::warning("Unable to send notification to delivery boy ID: {$deliveryBoyId}");
    return false;
}
}
