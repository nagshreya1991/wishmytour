<?php

namespace Modules\Booking\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Booking\app\Models\BookingPassenger;
use Modules\Booking\app\Models\Booking;

class BookingReportGallery extends Model
{
   // Table associated with the model
   protected $table = 'report_gallery_images';

   // The attributes that are mass assignable
   protected $fillable = ['booking_id', 'path'];

   /**
    * Get the booking associated with the report gallery image.
    */
   public function booking()
   {
       return $this->belongsTo(Booking::class, 'booking_id');
   }
}
