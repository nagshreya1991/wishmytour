<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'transaction_number',
        'cancellation_date',
        'cancellation_reason',
        'cancellation_type',
        'booking_price',
        'cancellation_percent',
        'cancellation_charge',
        'website_percent',
        'website_charge',
        'gst_percent',
        'gst_charge',
        'refund_amount',
        'no_of_pax',
        'pax_charge'
    ];

    /**
     * Get the booking that owns the cancellation.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    
}
