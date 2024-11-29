<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Booking\app\Models\Booking;

class BookingCustomerDetails extends Model
{
    protected $fillable = [
        'package_id', 'name', 'address', 'email', 'phone_number', 'state_id', 'pan_number', 'booking_for'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
