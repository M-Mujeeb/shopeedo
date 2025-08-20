<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class DeliveryHistory extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = [
        'order_id',
        'delivery_boy_id',
        'created_at',
        'updated_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
