<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Booking\app\Models\Booking;

class BookingMessage extends Model
{
    protected $fillable = [
        'vendor_id', 'package_id', 'package_name', 'run_date', 'message'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
