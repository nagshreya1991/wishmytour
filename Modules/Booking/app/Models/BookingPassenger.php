<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Booking\app\Models\BookingRoom;

class BookingPassenger extends Model
{
    protected $fillable = [
        'booking_id', 'package_id', 'title', 'first_name', 'last_name', 'dob', 'gender', 'is_adult', 'price'
    ];

    public function room()
    {
        return $this->belongsTo(BookingRoom::class);
    }
}