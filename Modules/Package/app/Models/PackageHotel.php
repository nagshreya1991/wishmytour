<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageHotel extends Model
{
    use HasFactory;

    protected $table = 'package_hotel';

    protected $fillable = [
        'package_id',
        'itinerary_id',
        'name',
        'rating',
        'is_other_place',
        'place_name',
        'distance_from_main_town',
        
    ];
    // Define relationships if needed
    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }
    public function hotel_gallery()
    {
        return $this->hasMany(PackageHotelGalleryImage::class, 'hotel_id', 'id');
    }
    
}
