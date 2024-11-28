<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Booking\app\Models\Booking;

class BookingCommission extends Model
{
    protected $table = 'booking_commissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'booking_id',
        'base_price',
        'cancelled_amount',
        'invoice_number',
        'voucher_number',
        'commission',
        'group_commission',
        'commission_amount',
        'tds_amount',
        'paid_amount',
        'payment_status',
        'payment_date',
        'claim_status',
        'claimed_date'
    ];
}
