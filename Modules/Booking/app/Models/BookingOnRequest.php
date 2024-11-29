<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;

class BookingOnRequest extends Model
{
    protected $fillable = [
    'package_id', 'customer_id', 'token', 'room_details','status','base_amount','add_on_id','price','start_date','end_date','addon_total_price','gst_percent','gst_price','tcs','final_price'
    
    ];

  
}