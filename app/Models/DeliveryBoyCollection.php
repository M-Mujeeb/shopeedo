<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class DeliveryBoyCollection extends Model
{

    use HasFactory, PreventDemoModeChanges;

    protected $fillable = ['delivery_boy_id', 'payment_type', 'collection_amount'];

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
