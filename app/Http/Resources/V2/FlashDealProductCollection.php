<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FlashDealProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(callback: function($data) {
                return [
                    'id' => $data->product_id,
                    'name' => $data->product->name,
                    'slug' => $data->product->slug,
                    'rating' => $data->product->rating,
                    'image' => uploaded_asset($data->product->thumbnail_img), 
                    'discounted_price' => home_discounted_base_price($data->product),
                    'original_price' => home_base_price($data->product),
                    'discount_type'=> $data->product->discount_type,
                    'discount_value'=> $data->product->discount,
                    'links' => [
                        'details' => route('products.show', $data->product_id),
                    ]
                ];
            })
        ];
    }
}
