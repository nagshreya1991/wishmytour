<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'total_amount',
        'paid_amount',
        'payment_type',
        'payment_date',
        'payment_status',
        'next_payment_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

}
