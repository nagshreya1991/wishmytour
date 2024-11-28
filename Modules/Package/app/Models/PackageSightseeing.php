<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageSightseeing extends Model
{
    use HasFactory;

    protected $table = 'package_sightseeing';

    protected $fillable = [
        'itinerary_id',
        'morning',
        'afternoon',
        'evening',
        'night',
    ];
    // Define relationships if needed
    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }
    public function sightseeing_gallery()
    {
        return $this->hasMany(PackageSightseeingGallery::class, 'sightseeing_id', 'id');
    }
}
