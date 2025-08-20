<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Utility\CartUtility;
use App\Utility\NagadUtility;
use Illuminate\Http\Request;
use App\Models\Coupon;


class CartController extends Controller
{
    public function summary(Request $request)
    {
        // $user  = auth()->user();
        $user  = $request->user_id != null ? User::where('id', $request->user_id)->first() : null;
        $items = ($user != null) ?
                Cart::where('user_id', $user->id)->active()->get() :
                ($request->has('temp_user_id') ? Cart::where('temp_user_id', $request->temp_user_id)->active()->get() : [] );



        if ($items->isEmpty()) {
            return response()->json([
                'sub_total' => format_price(0.00),
                'tax' => format_price(0.00),
                'shipping_cost' => format_price(0.00),
                'discount' => format_price(0.00),
                'grand_total' => format_price(0.00),
                'grand_total_value' => 0.00,
                'coupon_code' => "",
                'coupon_applied' => false,
            ]);
        }


        $sum = 0.00;
        $subtotal = 0.00;
        $tax = 0.00;
        foreach ($items as $cartItem) {
            $product = Product::find($cartItem['product_id']);
            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
            $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
        }

        $shipping_cost = $items->sum('shipping_cost');
        $discount = $items->sum('discount');
        $sum = ($subtotal + $tax ) - $discount;
        // $sum = ($subtotal + get_shippment_fees() + get_platform_fees()) - $discount;

        // single_price(round($grand_total + get_shippment_fees() + get_platform_fees(), 2)),

        // return $items[0]->owner_id;

        $shop = Shop::where('user_id', $items[0]->owner_id)->first();
        $shop->delivery_pickup_latitude = $shop->delivery_pickup_latitude;
        $shop->delivery_pickup_longitude = $shop->delivery_pickup_longitude;

        $user_address = Address::where('user_id', $request->user_id)
            ->where('set_default', 1)
            ->first();

            $user_address->long = $user_address->longitude;
            $user_address->lat = $user_address->latitude;

        $newCost = false;

        $distance = customer_shop_distance($shop->delivery_pickup_latitude,$shop->delivery_pickup_longitude, $user_address->long, $user_address->lat);
        $per_km = get_setting('per_km');
        $shipping_cost_new = get_setting('flat_rate_shipping_cost');

        $per_km_cost = $shipping_cost/(float) $per_km;

        if((float) $distance > (float) $per_km){
            $extra_km = (float) $distance - (float) $per_km;
            $shipping_cost_new = $shipping_cost_new + (float)($extra_km * (float)$per_km_cost);
            $newCost = true;
        }


        return response()->json([
            'sub_total' => single_price($subtotal),
            'tax' => single_price($tax),
            'shipping_cost' =>  $newCost ? single_price($shipping_cost_new) :  single_price($shipping_cost),
            'discount' => single_price($discount),
            'grand_total' => single_price(round($sum,2)),
            'plateform_fee' => get_platform_fees(),
            'grand_total_value' => convert_price($sum) + get_platform_fees() + get_shippment_fees(),
            'coupon_code' => $items[0]->coupon_code,
            'coupon_applied' => $items[0]->coupon_applied == 1,
        ]);
    }

    public function count(Request $request)
    {
        $user_id = $request->user_id;
        $temp_user_id = $request->temp_user_id;
        $items  = ($user_id != null) ?
                    Cart::where('user_id', $user_id)->active()->get() :
                    ($temp_user_id != null ? Cart::where('temp_user_id', $temp_user_id)->active()->get() : [] );

        return response()->json([
            'count' => sizeof($items),
            'status' => true,
        ]);
    }

    public function getList(Request $request)
    {
        $user_id = $request->user_id;
        $temp_user_id = $request->temp_user_id;

        $owner_ids = ($user_id != null) ?
            Cart::where('user_id', $user_id)->active()->select('owner_id')->groupBy('owner_id')->pluck('owner_id')->toArray() :
            ($temp_user_id != null ? Cart::where('temp_user_id', $temp_user_id)->active()->select('owner_id')->groupBy('owner_id')->pluck('owner_id')->toArray() : [] );


        $currency_symbol = currency_symbol();
        $shops = [];
        $sub_total = 0.00;
        $grand_total = 0.00;
        $counter = 0;
        $customer_logitude = 0.00;
        $customer_latitude = 0.00;
        $total_shipping_cost = 0.00;
        if (!empty($owner_ids)) {
            foreach ($owner_ids as $owner_id) {
                $shop = Shop::where('user_id', $owner_id)->first();
                $user_address = Address::where('user_id', $user_id)
                    ->where('set_default', 1)
                    ->first();
                if($user_address){
                     $customer_logitude = $user_address->longitude;
                    $customer_latitude = $user_address->latitude;
                }else{
                    $customer_logitude =  0.00;
                    $customer_latitude  =  0.00;
                }

                $newCost = false;

                $distance = getMultipleRoutes($shop->delivery_pickup_latitude,$shop->delivery_pickup_longitude,$customer_latitude, $customer_logitude );


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
            $coupons = Coupon::where('start_date', '<=', strtotime(date('d-m-Y')))->where('end_date', '>=', strtotime(date('d-m-Y')))->where('user_id', $owner_id)->get();

                $shop = array();
                $shop_items_raw_data = ($user_id != null) ?
                    Cart::where('user_id', $user_id)->where('owner_id', $owner_id)->active()->get()->toArray() :
                    ($temp_user_id != null ? Cart::where('temp_user_id', $temp_user_id)->where('owner_id', $owner_id)->active()->get()->toArray() : [] );
                $shop_items_data = array();


                if (!empty($shop_items_raw_data)) {
                    foreach ($shop_items_raw_data as $shop_items_raw_data_item) {
                        $product = Product::where('id', $shop_items_raw_data_item["product_id"])->first();
                        $price = cart_product_price($shop_items_raw_data_item, $product, false, false) * intval($shop_items_raw_data_item["quantity"]);
                        $tax = cart_product_tax($shop_items_raw_data_item, $product, false);
                        $shop_items_data_item["id"] = intval($shop_items_raw_data_item["id"]);
                        $shop_items_data_item["status"] = intval($shop_items_raw_data_item["status"]);
                        $shop_items_data_item["owner_id"] = intval($shop_items_raw_data_item["owner_id"]);
                        $shop_items_data_item["user_id"] = intval($shop_items_raw_data_item["user_id"]);
                        $shop_items_data_item["product_id"] = intval($shop_items_raw_data_item["product_id"]);
                        $shop_items_data_item["product_name"] = $product->getTranslation('name');
                        $shop_items_data_item["auction_product"] = $product->auction_product;
                        $shop_items_data_item["product_thumbnail_image"] = uploaded_asset($product->thumbnail_img);
                        $shop_items_data_item["variation"] = $shop_items_raw_data_item["variation"];
                        $shop_items_data_item["price"] = (float) cart_product_price($shop_items_raw_data_item, $product, false, false);
                        $shop_items_data_item["currency_symbol"] = $currency_symbol;
                        $shop_items_data_item["tax"] = (float) cart_product_tax($shop_items_raw_data_item, $product, false);
                        $shop_items_data_item["price"] = (string) $price;
                        $shop_items_data_item["currency_symbol"] = $currency_symbol;
                        $shop_items_data_item["tax"] = single_price($tax);
                        // $shop_items_data_item["shipping_cost"] = (float) $shop_items_raw_data_item["shipping_cost"];
                        $shop_items_data_item["shipping_cost"] = $newCost ? $shipping_cost : get_shippment_fees();
                        $total_shipping_cost += $shop_items_data_item["shipping_cost"];

                        $shop_items_data_item["platform_fees"] = get_platform_fees();


                        $shop_items_data_item["quantity"] = intval($shop_items_raw_data_item["quantity"]);
                        $shop_items_data_item["lower_limit"] = intval($product->min_qty);
                        $shop_items_data_item["upper_limit"] = intval($product->stocks->where('variant', $shop_items_raw_data_item['variation'])->first()->qty);
                        $shop_items_data_item["is_quick"] = (bool) $product->category->is_quick;


        // Filter coupons of type 'product_base'
                $productBaseCoupons = $coupons->filter(function ($coupon) {
                    return $coupon->type === 'product_base';
                });

                // Find the matching coupon for the product
                $matchingCoupon = $productBaseCoupons->reduce(function ($carry, $coupon) use ($product) {
                    $products_json_id = json_decode($coupon->details, true);

                    // Check if the product ID exists in the coupon details
                    $isMatch = collect($products_json_id)->contains(function ($product_json_id) use ($product) {
                        return (int) $product_json_id['product_id'] === (int) $product->id;
                    });

                    // If a match is found, return the coupon details
                    if ($isMatch) {
                        return [
                            "coupon_code" => $coupon->code,
                            "coupon_discount" => $coupon->discount,
                        ];
                    }

                    return $carry; // Return the existing carry if no match is found
                }, [
                    "coupon_code" => "", // Default coupon code
                    "coupon_discount" => "0", // Default discount
                ]);

// Set the coupon details
                    $shop_items_data_item["coupon_details"] = $matchingCoupon;


                        $sub_total += $price + $tax;
                        $shop_items_data[] = $shop_items_data_item;
                    }
                }

                $grand_total += $sub_total;

                $shop_data = Shop::where('user_id', $owner_id)->first();
                if ($shop_data) {
                    $shop['name'] = translate($shop_data->name);
                    $shop['owner_id'] = (int) $owner_id;
                    $shop['sub_total'] = single_price($sub_total);
                    $shop['slug'] =  $shop_data->slug;
                    $shop['cart_items'] = $shop_items_data;
                    $shop['seller_lat'] =$shop_data->delivery_pickup_latitude;
                    $shop['seller_lng'] = $shop_data->delivery_pickup_longitude;
                } else {
                    $shop['name'] = translate("Inhouse");
                    $shop['owner_id'] = (int) $owner_id;
                    $shop['sub_total'] = single_price($sub_total);
                    $shop['cart_items'] = $shop_items_data;
                    // $shop['coupon_details'] = 'h';
                }
                $shops[] = $shop;
                $sub_total = 0.00;
            }
        }

        return response()->json([
            "grand_total" => single_price(round($grand_total + $total_shipping_cost + get_platform_fees(), 2)),
            "data" => $shops
        ]);
    }

    public function add(Request $request)
    {
        $user_id =  $request->user_id != null ? $request->user_id : null;
        $temp_user_id =   $request->temp_user_id != null ? $request->temp_user_id : null;
        if($user_id != null) {
            $carts = Cart::where('user_id', $user_id)->active()->get();
        }
        else {
            if($temp_user_id == null){
                $temp_user_id = bin2hex(random_bytes(10));
            }
            $carts = Cart::where('temp_user_id', $temp_user_id)->active()->get();
        }

        $check_auction_in_cart = CartUtility::check_auction_in_cart($carts);
        $product = Product::findOrFail($request->id);

        if ($check_auction_in_cart && $product->auction_product == 0) {
            return response()->json([
                'result' => false,
                'temp_user_id' => $temp_user_id,
                'message' => translate('Remove auction product from cart to add this product.')
            ], 200);
        }
        if ($check_auction_in_cart == false && count($carts) > 0 && $product->auction_product == 1) {
            return response()->json([
                'result' => false,
                'temp_user_id' => $temp_user_id,
                'message' => translate('Remove other products from cart to add this auction product.')
            ], 200);
        }

        if ($product->min_qty > $request->quantity) {
            return response()->json([
                'result' => false,
                'temp_user_id' => $temp_user_id,
                'message' => translate("Minimum") . " {$product->min_qty} " . translate("item(s) should be ordered")
            ], 200);
        }

        $variant = $request->variant;
        $tax = 0;
        $quantity = $request->quantity;

        $product_stock = $product->stocks->where('variant', $variant)->first();

        if($user_id != null) {
            $cart = Cart::firstOrNew([
                'variation' => $variant,
                'user_id' => $user_id,
                'product_id' => $request['id']
            ]);
        } else {
            $cart = Cart::firstOrNew([
                'variation' => $variant,
                'temp_user_id' => $temp_user_id,
                'product_id' => $request['id']
            ]);
        }


        $variant_string = $variant != null && $variant != "" ? translate("for") . " ($variant)" : "";

        if ($cart->exists && $product->digital == 0) {
            if ($product->auction_product == 1 && ($cart->product_id == $product->id)) {
                return response()->json([
                    'result' => false,
                    'message' => translate('This auction product is already added to your cart.')
                ], 200);
            }
            if ($product_stock->qty < $cart->quantity + $request['quantity']) {
                if ($product_stock->qty == 0) {
                    return response()->json([
                        'result' => false,
                        'temp_user_id' => $temp_user_id,
                        'message' => translate("Stock out")
                    ], 200);
                } else {
                    return response()->json([
                        'result' => false,
                        'temp_user_id' => $temp_user_id,
                        'message' => translate("Only") . " {$product_stock->qty} " . translate("item(s) are available") . " {$variant_string}"
                    ], 200);
                }
            }
            if ($product->digital == 1 && ($cart->product_id == $product->id)) {
                return response()->json([
                    'result' => false,
                    'temp_user_id' => $temp_user_id,
                    'message' => translate('Already added this product')
                ]);
            }
            $quantity = $cart->quantity + $request['quantity'];
        }

        $price = CartUtility::get_price($product, $product_stock, $request->quantity);
        $tax = CartUtility::tax_calculation($product, $price);
        CartUtility::save_cart_data($cart, $product, $price, $tax, $quantity);

        if (NagadUtility::create_balance_reference($request->cost_matrix) == false) {
            return response()->json(['result' => false, 'message' => 'Cost matrix error']);
        }

        return response()->json([
            'result' => true,
            'temp_user_id' => $temp_user_id,
            'message' => translate('Product added to cart successfully')
        ]);
    }
    public function changeQuantity(Request $request)
    {
        $cart = Cart::find($request->id);
        if ($cart != null) {
            $product = Product::find($cart->product_id);
            if ($product->auction_product == 1) {
                return response()->json(['result' => false, 'message' => translate('Maximum available quantity reached')], 200);
            }
            if ($cart->product->stocks->where('variant', $cart->variation)->first()->qty >= $request->quantity) {
                $cart->update([
                    'quantity' => $request->quantity
                ]);

                return response()->json(['result' => true, 'message' => translate('Cart updated')], 200);
            } else {
                return response()->json(['result' => false, 'message' => translate('Maximum available quantity reached')], 200);
            }
        }

        return response()->json(['result' => false, 'message' => translate('Something went wrong')], 200);
    }

    public function process(Request $request)
    {
        $cart_ids = explode(",", $request->cart_ids);
        $cart_quantities = explode(",", $request->cart_quantities);

        if (!empty($cart_ids)) {
            $i = 0;
            foreach ($cart_ids as $cart_id) {
                $cart_item = Cart::where('id', $cart_id)->first();
                $product = Product::where('id', $cart_item->product_id)->first();

                if ($product->min_qty > $cart_quantities[$i]) {
                    return response()->json(['result' => false, 'message' => translate("Minimum") . " {$product->min_qty} " . translate("item(s) should be ordered for") . " {$product->name}"], 200);
                }

                $stock = $cart_item->product->stocks->where('variant', $cart_item->variation)->first()->qty;
                $variant_string = $cart_item->variation != null && $cart_item->variation != "" ? " ($cart_item->variation)" : "";
                if ($stock >= $cart_quantities[$i] || $product->digital == 1) {
                    $cart_item->update([
                        'quantity' => $cart_quantities[$i]
                    ]);
                } else {
                    if ($stock == 0) {
                        return response()->json(['result' => false, 'message' => translate("No item is available for") . " {$product->name}{$variant_string}," . translate("remove this from cart")], 200);
                    } else {
                        return response()->json(['result' => false, 'message' => translate("Only") . " {$stock} " . translate("item(s) are available for") . " {$product->name}{$variant_string}"], 200);
                    }
                }

                $i++;
            }

            return response()->json(['result' => true, 'message' => translate('Cart updated')], 200);
        } else {
            return response()->json(['result' => false, 'message' => translate('Cart is empty')], 200);
        }
    }

    public function destroy($id)
    {
        Cart::destroy($id);
        return response()->json(['result' => true, 'message' => translate('Product is successfully removed from your cart')], 200);
    }

    public function guestCustomerInfoCheck(Request $request){
        $user = addon_is_activated('otp_system') ?
                User::where('email', $request->email)->orWhere('phone','+'.$request->phone)->first() :
                User::where('email', $request->email)->first();

        return response()->json([
            'result' => ($user != null) ? true : false
        ]);
    }

    public function updateCartStatus(Request $request)
    {
        $product_ids = $request->product_ids;
        $user_id = $request->user_id;
        $temp_user_id = $request->temp_user_id;
        $carts  = ($user_id != null) ?
                    Cart::where('user_id', $user_id)->get() :
                    ($temp_user_id != null ? Cart::where('temp_user_id', $temp_user_id)->get() : [] );

        $carts->toQuery()->update(['status' => 0]);
        if($product_ids != null){
            $carts->toQuery()->whereIn('product_id', $product_ids)->update(['status' => 1]);
        }

        return response()->json([
            'result' => true,
            'message' => translate('Cart status updated successfully')
        ]);
    }
}
