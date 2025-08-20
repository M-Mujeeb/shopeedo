<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionReturnHistory extends Model
{
    use HasFactory;

    protected $fillable = ['delivery_boy_id', 'amount_returned', 'remarks'];

    public function deliveryBoy()
    {
        return $this->belongsTo(DeliveryBoy::class, 'delivery_boy_id');
    }


}
