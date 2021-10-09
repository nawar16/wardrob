<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = "bookings";
    protected $fillable = ['start_at', 'end_at', 'user_id', 'service_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    function service() {
        return $this->belongsTo('App\Models\Service', 'service_id');
    }
}
