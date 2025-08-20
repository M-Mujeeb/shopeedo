<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryBoyShiftHistory extends Model
{
    protected $table = 'delivery_boy_shift_histories';

    protected $fillable = [
        'user_id',
        'start_at',
        'end_at',
        'duration_seconds',
        'start_lat','start_lng','end_lat','end_lng','meta',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'meta'     => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
