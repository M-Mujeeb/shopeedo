<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\FlashDealCollection;
use App\Http\Resources\V2\ProductCollection;
use App\Http\Resources\V2\ProductMiniCollection;
use App\Models\FlashDeal;
use App\Models\Product;
use Carbon\Carbon;

class FlashDealController extends Controller
{
    public function index()
    {
        $flash_deals = FlashDeal::where('status', 1)
            ->where('start_date', '<=', strtotime(date('d-m-Y')))
            ->where('end_date', '>=', strtotime(date('d-m-Y')))
            ->get();
        return new FlashDealCollection($flash_deals);
    }
    public function info($slug)
    {
        $flash_deals = FlashDeal::where('slug', $slug)->where('status', 1)
            ->where('start_date', '<=', strtotime(date('d-m-Y')))
            ->where('end_date', '>=', strtotime(date('d-m-Y')))
            ->get();

        return new FlashDealCollection($flash_deals);
    }


    public function products()
    {
        $today = Carbon::today()->timestamp;

        $deals = FlashDeal::with('flash_deal_products.product')
            ->where('status', 1)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        $products = collect();
        foreach ($deals as $deal) {
            foreach ($deal->flash_deal_products as $fdp) {
                if ($fdp->product) {
                    $products->push($fdp->product);
                }
            }
        }

        $products = $products->unique('id')->values();

        return new ProductMiniCollection($products);
    }
}
