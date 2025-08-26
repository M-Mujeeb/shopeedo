<?php

namespace App\Http\Resources\V2;

use Carbon\Carbon;
use App\Models\Shop;
use App\Models\Rating;
use App\Models\Address;
use App\Models\DeliveryBoy;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseHistoryDeliveryCollection extends ResourceCollection
{

    private function formatShippingAddress($shipping_address)
    {
        if (!$shipping_address) return '';

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
                $pickup_point = null;
                if ($data->shipping_type == 'pickup_point' && $data->pickup_point_id) {
                    $pickup_point = $data->pickup_point;
                }

                $is_quick = false;
                foreach ($data->orderDetails as $orderDetail) {
                    $product = $orderDetail->product;
                    if ($product && $product->category && $product->category->is_quick) {
                        $is_quick = true;
                        break;
                    }
                }

                $shipping_address = json_decode($data->shipping_address,true);
                $lat = 90.99;
                $lang = 180.99;

                if(isset($shipping_address['lat_lang'])){
                    $location_available = true;
                    $exploded_lat_lang = explode(',',$shipping_address['lat_lang']);
                    $lat = floatval($exploded_lat_lang[0]);
                    $lang = floatval($exploded_lat_lang[1]);
                }

                $seller = Shop::where('user_id', $data->seller_id)->first();
                $customer = Address::where('user_id', $data->user_id)->first();

                $bestDistance = 0;
                if($seller->delivery_pickup_latitude && $seller->delivery_pickup_longitude && $lat && $lang ){
                    $distance = getMultipleRoutes($seller->delivery_pickup_latitude,$seller->delivery_pickup_longitude,$lat, $lang );

                    if (!empty($distance)) {
                        $bestRoute = collect($distance)->sortBy('distance_km')->first();
                        $bestDistance = (float) $bestRoute['distance_km'];
                    } else {
                        $bestDistance = 0;
                    }
                }


                return [
                    'id' => $data->id,
                    'code' => $data->code,
                    'user_id' => (int) $data->user_id,
                    'drop_off' => $this->formatShippingAddress($data->shipping_address),
                    'shipping_address' => json_decode($data->shipping_address),
                    'payment_type' => ucwords(str_replace('_', ' ', translate($data->payment_type))),
                    'pickup_point' => $pickup_point,
                    'shipping_type' => $data->shipping_type,
                    'plateform_fee' => get_platform_fees(),
                    'special_instructions'=>$data->additional_info == null ? '' : $data->additional_info,
                    'is_quick' => $is_quick,
                    'shipping_type_string' => $data->shipping_type != null ? ucwords(str_replace('_', ' ', translate($data->shipping_type))) : "",
                    'payment_status' => $data->payment_status,
                    'payment_status_string' => ucwords(str_replace('_', ' ', translate($data->payment_status))),
                    'delivery_status' => $data->delivery_status,
                    'delivery_status_string' => $data->delivery_status == translate('pending') ? translate("Order Placed") : ucwords(str_replace('_', ' ', translate($data->delivery_status))),
                    'grand_total' => format_price(convert_price($data->grand_total)),
                    'plane_grand_total' => $data->grand_total,
                    'coupon_discount' => format_price(convert_price($data->coupon_discount)),
                    'shipping_cost' => format_price(convert_price($data->combinedOrder->shipping_cost)),
                    'subtotal' => format_price(convert_price($data->orderDetails->sum('price'))),
                    'tax' => format_price(convert_price($data->orderDetails->sum('tax'))),
                    'date' => Carbon::createFromTimestamp($data->date)->format('d-m-Y'),
                    'cancel_request' => $data->cancel_request == 1,
                    'manually_payable' => $data->manual_payment && $data->manual_payment_data == null,
                    'seller_lat' => $seller->delivery_pickup_latitude != null ? $seller->delivery_pickup_latitude : '',
                    'seller_lon' => $seller->delivery_pickup_longitude != null ? $seller->delivery_pickup_longitude : '',
                    'shop_address' => (is_object($seller) && !empty($seller->address)) ? $seller->address : '',
                    'customer_lat' => $customer->latitude != null ? $customer->latitude : '',
                    'customer_lon' => $customer->longitude != null ? $customer->longitude : '',
                    'distance' => number_format($bestDistance, 10, '.', ''),
                    'links' => [
                        'details' => ''
                    ],
                    'created_at' => $data->delivery_history_date,
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
