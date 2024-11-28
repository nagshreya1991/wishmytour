<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageHotelGalleryImage extends Model
{
    use HasFactory;

    protected $table = 'package_hotel_gallery_images';

    protected $fillable = [
        'hotel_id ',
        'path',
        
    ];
    // Define relationships if needed
    public function hotel()
    {
        return $this->belongsTo(PackageHotel::class, 'hotel_id');
    }
}
