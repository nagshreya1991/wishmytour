<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Booking\app\Models\BookingPassenger;
use Modules\Booking\app\Models\Booking;

class BookingRoom extends Model
{
    protected $fillable = [
        'package_id', 'room_no', 'adults', 'children'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function passengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }
}
