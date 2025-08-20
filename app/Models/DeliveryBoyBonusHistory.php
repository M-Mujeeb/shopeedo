<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryBoyBonusHistory extends Model
{
    use HasFactory;

    protected $table = 'delivery_boy_bonus_histories';
    protected $fillable = [
        'user_id',
        'bonus_type',
        'bonus_amount',
        'delivery_count',
        'start_date',
        'end_date',
        'status',
        'remarks',
        'is_paid'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryBoy()
    {
        return $this->belongsTo(DeliveryBoy::class, 'user_id', 'user_id');
    }
}
