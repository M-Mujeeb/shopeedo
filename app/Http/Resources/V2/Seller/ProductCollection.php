<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
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
            'data' => $this->collection->map(function ($data) {
                $qty = 0;
                foreach ($data->stocks as $key => $stock) {
                    $qty += $stock->qty;
                }
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'slug'=>$data->slug,
                    'thumbnail_img' => uploaded_asset($data->thumbnail_img),
                    'price' => format_price($data->unit_price),
                    'desc'=>$data->description,
                    'rating' => number_format((float) $data->rating, 1, '.', ''),
                    'current_stock' => $qty,
                    'status' => $data->published == 0 ? false : true,
                    'approved' => $data->approved == 0 ? false : true,
                    'category' => $data->main_category ? $data->main_category->getTranslation('name') : "",
                    'featured' => $data->seller_featured == 0 ? false : true,
                ];
            }),

        ];
    }
}
