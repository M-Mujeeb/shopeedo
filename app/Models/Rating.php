<?php

// app/Models/Rating.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;


    protected $table = 'ratings';

    protected $fillable = [
        'delivery_boy_id',
        'user_id',
        'rating',
        'comment',
        'order_id'
    ];

    // Define the relationships to the DeliveryBoy and User models

    public function deliveryBoy()
    {
        return $this->belongsTo(DeliveryBoy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
