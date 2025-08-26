<?php

namespace App\Http\Resources\V2;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PurchaseHistoryMiniCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id'                     => $data->id,
                    'code'                   => $data->code,
                    'user_id'                => (int) $data->user_id,
                    'payment_type'           => ucwords(str_replace('_', ' ', $data->payment_type)),
                    'payment_status'         => translate($data->payment_status),
                    'payment_status_string'  => ucwords(str_replace('_', ' ', translate($data->payment_status))),
                    'delivery_status'        => translate($data->delivery_status),
                    'delivery_status_string' => $data->delivery_status == translate('pending')
                        ? translate("Order Placed")
                        : ucwords(str_replace('_', ' ', translate($data->delivery_status))),
                    'grand_total'            => format_price(convert_price($data->grand_total)),
                    'date'                   => Carbon::createFromTimestamp($data->date)->format('Y-m-d H:i:s'),

                    // NEW: delivery charges from combined_order
                    'delivery_charges'       => format_price(convert_price(
                        optional($data->combinedOrder)->shipping_cost
                        ?? optional($data->combinedOrder)->shipping_cost
                        ?? 0
                    )),

                    'items' => $data->orderDetails->map(function ($d) {
                        $name = $d->product->name ?? ($d->product_name ?? '');
                        if (!empty($d->variation ?? null)) {
                            $name .= ' - ' . $d->variation;
                        }
                        return [
                            'name' => $name,
                            'qty'  => (int) $d->quantity,
                            'price' => format_price(convert_price(
                        $d->price
                        ?? 0
                    ))
                        ];
                    })->values(),

                    'links' => [
                        'details' => ''
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
