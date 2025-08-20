<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\PickupPointResource;
use App\Models\Address;
use App\Models\Cart;
use App\Models\PickupPoint;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
class ShippingController extends Controller
{
    public function pickup_list()
    {
        $pickup_point_list = PickupPoint::where('pick_up_status', '=', 1)->get();

        return PickupPointResource::collection($pickup_point_list);
    }

    public function shipping_cost(Request $request)
    {
        $userId        = $request->has('user_id') ? $request->user_id : null;
        $tempUserId    = $request->has('temp_user_id') ? $request->temp_user_id : null;
        $main_carts    = ($userId != null) ? Cart::where('user_id', $userId)->active()->get() : Cart::where('temp_user_id', $tempUserId)->active()->get();
        $shipping_info = null;
        foreach ($request->seller_list as $key => $seller) {
            $seller['shipping_cost'] = 0;

            $carts = $main_carts->toQuery()->where("owner_id", $seller['seller_id'])->get();

            // Logged In User shipping info
            if($userId != null){
                $address = Address::where('id', $carts[0]['address_id'])->first();
                $shipping_info['country_id'] = $address->country_id;
                $shipping_info['city_id'] = $address->city_id;
            }

            // Guest User Shipping info
            elseif($tempUserId != null){
                $shipping_info['country_id'] = $request->country_id;
                $shipping_info['city_id'] = $request->city_id;
            }

            foreach ($carts as $key => $cartItem) {
                $cartItem['shipping_cost'] = 0;

                if ($seller['shipping_type'] == 'pickup_point') {
                    $cartItem['shipping_type'] = 'pickup_point';
                    $cartItem['pickup_point'] = $seller['shipping_id'];
                } else if ($seller['shipping_type'] == 'home_delivery') {
                    $cartItem['shipping_type'] = 'home_delivery';
                    $cartItem['pickup_point'] = 0;

                    $cartItem['shipping_cost'] = getShippingCost($main_carts, $key, $shipping_info);
                } else if ($seller['shipping_type'] == 'carrier') {
                    $cartItem['shipping_type'] = 'carrier';
                    $cartItem['pickup_point'] = 0;
                    $cartItem['carrier_id'] = $seller['shipping_id'];
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info, $seller['shipping_id']);
                }

                $cartItem->save();
            }
        }

        //Total shipping cost $calculate_shipping
        $total_shipping_cost = $main_carts->fresh()->toQuery()->sum('shipping_cost');
        return response()->json(['result' => true, 'shipping_type' => get_setting('shipping_type'), 'value' => convert_price($total_shipping_cost), 'value_string' => format_price(convert_price($total_shipping_cost))], 200);
    }

    public function getDeliveryInfo(Request $request)
{
    $userId     = $request->has('user_id') ? $request->user_id : null;
    $tempUserId = $request->has('temp_user_id') ? $request->temp_user_id : null;

    // Fetch cart items
    $cartItems = ($userId != null)
        ? Cart::where('user_id', $userId)->active()->get()
        : Cart::where('temp_user_id', $tempUserId)->active()->get();

    $shipping_info = [];

    // Logged-in User shipping info
    if ($userId != null && $cartItems->isNotEmpty()) {
        $cart     = $cartItems->first();
        $address  = Address::where('id', $cart->address_id)->first();

        if ($address) {
            $shipping_info['country_id'] = $address->country_id;
            $shipping_info['city_id']    = $address->city_id;
        }
    }
    // Guest User shipping info
    elseif ($tempUserId != null) {
        $shipping_info['country_id'] = $request->country_id;
        $shipping_info['city_id']    = $request->city_id;
    }

    // Group cart items by owner_id
    $owner_ids = $cartItems->pluck('owner_id')->unique()->toArray();

    $shops = [];

    if (!empty($owner_ids)) {
        foreach ($owner_ids as $owner_id) {
            $shop = [];

            // Filter cart items for the current owner_id
            $shop_items_raw_data = $cartItems->where('owner_id', $owner_id);

            $shop_items_data = [];
            if ($shop_items_raw_data->isNotEmpty()) {
                foreach ($shop_items_raw_data as $cart_item) {
                    $product = Product::find($cart_item->product_id);

                    if ($product) {
                        $shop_items_data[] = [
                            "id"                      => (int) $cart_item->id,
                            "owner_id"                => (int) $cart_item->owner_id,
                            "user_id"                 => (int) $cart_item->user_id,
                            "temp_user_id"            => (int) $cart_item->temp_user_id,
                            "product_id"              => (int) $cart_item->product_id,
                            "product_name"            => $product->getTranslation('name'),
                            "product_thumbnail_image" => uploaded_asset($product->thumbnail_img),
                            "product_is_digital"      => $product->digital == 1,
                        ];
                    }
                }
            }

            // Fetch shop data
            $shop_data = Shop::where('user_id', $owner_id)->first();
            $shop['name']      = $shop_data ? $shop_data->name : "Inhouse";
            $shop['owner_id']  = (int) $owner_id;
            $shop['cart_items'] = $shop_items_data;

            // Carriers
            $shop['carriers'] = seller_base_carrier_list($owner_id, $userId, $tempUserId, $shipping_info);

            // Pickup Points
            $shop['pickup_points'] = [];
            if (get_setting('pickup_point') == 1) {
                $pickup_point_list = PickupPoint::where('pick_up_status', '=', 1)->get();
                $shop['pickup_points'] = PickupPointResource::collection($pickup_point_list);
            }

            $shops[] = $shop;
        }
    }

    return response()->json($shops);
}

}
