<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Booking\app\Models\Booking;

class BookingDate extends Model
{
    protected $fillable = [
        'package_id', 'booking_date', 'cost'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
