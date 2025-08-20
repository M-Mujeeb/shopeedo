<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    { 
        $shipping_address = json_decode($this->shipping_address);
        $combinedOrder = $this->combinedOrder; // Access the related combined_order

        return [
            'order_code'        => $this->code,
            'total'             => format_price($combinedOrder ? $combinedOrder->grand_total : $this->grand_total + $this->platform_fee),
            'order_date'        => date('d-m-Y', strtotime($this->created_at)),
            'payment_status'    => $this->payment_status,
            'payment_type'      => ucwords(str_replace('_', ' ', $this->payment_type)),
            'delivery_status'   => $this->delivery_status,
            'shipping_type'     => $this->shipping_type,
            'payment_method'    => $this->payment_type,
            'shipping_address'  => $shipping_address,
            'shipping_cost'     => format_price($combinedOrder ? $combinedOrder->shipping_cost : $this->orderDetails->sum('shipping_cost')),
            'subtotal'          => format_price($this->orderDetails->sum('price')),
            'coupon_discount'   => format_price($this->coupon_discount),
            'tax'               => format_price($this->orderDetails->sum('tax')),
            'order_items'       => OrderItemResource::collection($this->orderDetails),
            'platform_fee'      => format_price($this->platform_fee)
        ];
    }
}