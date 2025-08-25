<?php

namespace App\Http\Resources\V2;

use App\Models\CombinedOrder;
use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Order;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DeliveryBoyPendingHistoryMiniCollection extends ResourceCollection
{
    private function formatShippingAddress($shipping_address)
    {
        if (!$shipping_address)
            return '';

        $addressArray = json_decode($shipping_address, true);

        if (is_array($addressArray)) {
            $formattedAddress = array_filter([
                $addressArray['address'] ?? null,
                $addressArray['city'] ?? null,
                $addressArray['state'] ?? null,
                $addressArray['country'] ?? null
            ]);

            return implode(' ', $formattedAddress);
        }

        return '';
    }

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $delivery_pickup_latitude = 90.99;
                $delivery_pickup_longitude = 180.99;
                $store_location_available = false;
                if ($data->shop && $data->shop->delivery_pickup_latitude) {
                    $store_location_available = true;
                    $delivery_pickup_latitude = floatval($data->shop->delivery_pickup_latitude);
                    $delivery_pickup_longitude = floatval($data->shop->delivery_pickup_longitude);
                }if (!$data->shop) {
                    $store_location_available = true;
                    if (get_setting('delivery_pickup_latitude') && get_setting('delivery_pickup_longitude')) {
                        $delivery_pickup_latitude = floatval(get_setting('delivery_pickup_latitude'));
                        $delivery_pickup_longitude = floatval(get_setting('delivery_pickup_longitude'));
                    }

                }
                $shipping_address = json_decode($data->shipping_address, true);
                $location_available = false;
                $lat = 90.99;
                $lang = 180.99;

                if (isset($shipping_address['lat_lang'])) {
                    $location_available = true;
                    $exploded_lat_lang = explode(',', $shipping_address['lat_lang']);
                    $lat = floatval($exploded_lat_lang[0]);
                    $lang = floatval($exploded_lat_lang[1]);
                }

                $order = Order::where('code', $data->code)->with([
                    'orderDetails.product.category' => function ($query) {
                        $query->select('id', 'is_quick');
                    }
                ])->first();

                $isQuick = 0;
                if ($order) {
                    $isQuickValues = $order->orderDetails->map(function ($detail) {
                        return optional($detail->product->category)->is_quick;
                    })->filter()
                        ->unique();

                    $isQuick = $isQuickValues->first();
                }

                $shop = '';

                if ($data->seller_id && $data->seller_id != null) {
                    $shop = Shop::where('user_id', $data->seller_id)->first();
                }

                $bestDistance = 0;
                if ($data->shop->delivery_pickup_latitude && $data->shop->delivery_pickup_longitude && $lat && $lang) {

                    $distance = getMultipleRoutes($data->shop->delivery_pickup_latitude, $data->shop->delivery_pickup_longitude, $lat, $lang);


                    if (!empty($distance)) {
                        $bestRoute = collect($distance)->sortBy('distance_km')->first();
                        $bestDistance = (float) $bestRoute['distance_km'];
                    } else {
                        $bestDistance = 0;
                    }
                }

                $delivery_charges = 0;
                if (get_setting('delivery_boy_payment_type') == 'commission') {
                    if (get_setting('delivery_boy_commission_type') == 'flat') {
                        $delivery_charges = get_setting('delivery_boy_commission');
                    } else if (get_setting('delivery_boy_commission_type') == 'percentage') {
                        $shipping_cost = optional(CombinedOrder::where('id', $data->combined_order_id)->first())->shipping_cost ?? 0;
                        $delivery_charges = ($shipping_cost * get_setting('delivery_boy_commission')) / 100;
                    }
                }

                return [
                    'id' => $data->id,
                    'code' => $data->code,
                    'user_id' => intval($data->user_id),
                    'is_quick' => (bool) $isQuick,
                    'payment_type' => ucwords(str_replace('_', ' ', translate($data->payment_type))),
                    'payment_status' => $data->payment_status,
                    'payment_status_string' => ucwords(str_replace('_', ' ', $data->payment_status)),
                    'delivery_status' => $data->delivery_status,
                    'delivery_status_string' => $data->delivery_status == 'pending' ? "Order Placed" : ucwords(str_replace('_', ' ', $data->delivery_status)),
                    'grand_total' => format_price($data->grand_total),
                    'date' => Carbon::createFromFormat('Y-m-d H:i:s', $data->delivery_history_date)->format('d-m-Y'),
                    'cancel_request' => $data->cancel_request == 1,
                    'delivery_history_date' => $data->delivery_history_date,
                    'location_available' => $location_available,
                    'shipping_address' => $this->formatShippingAddress($data->shipping_address),
                    'lat' => $lat,
                    'lang' => $lang,
                    'store_location_available' => $store_location_available,
                    'delivery_pickup_latitude' => $delivery_pickup_latitude,
                    'delivery_pickup_longitude' => $delivery_pickup_longitude,
                    'shop_address' => (is_object($shop) && !empty($shop->address)) ? $shop->address : '',
                    'distance' => number_format($bestDistance, 10, '.', ''),
                    'delivery_charges' => format_price($delivery_charges),
                    'links' => [
                        'details' => ""
                    ]
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
