<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class OrderDetail extends Model
{
    use PreventDemoModeChanges;

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }

    public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}

    // public function seller()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function delivery_boy()
    // {
    //     return $this->belongsTo(User::class, 'assign_delivery_boy', 'id');
    // }

    public function pickup_point()
    {
        return $this->belongsTo(PickupPoint::class);
    }

    public function refund_request()
    {
        return $this->hasOne(RefundRequest::class);
    }

    public function affiliate_log()
    {
        return $this->hasMany(AffiliateLog::class);
    }
}
