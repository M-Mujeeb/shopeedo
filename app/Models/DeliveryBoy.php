<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class DeliveryBoy extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = [
        'lat', 'lng','rating', 'status',
        'rating_count',
        'comment',
        'bank_name',
        'bank_acc_name',
        'bank_acc_no'
    ];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function address()
{
    return $this->hasOne(Address::class, 'user_id', 'user_id');
}


    public function getCurrentLocationAttribute()
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isAssignedToOrder()
    {
        $isAssigned = Order::where('assign_delivery_boy', $this->user_id)
            ->whereNotIn('delivery_status', ['delivered', 'completed', 'cancelled'])
            ->exists();
        return $isAssigned;
    }

}
