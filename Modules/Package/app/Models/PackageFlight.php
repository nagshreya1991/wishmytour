<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageFlight extends Model
{
    use HasFactory;

    protected $table = 'package_flight';

    protected $fillable = [
        'itinerary_id',
        'depart_destination',
        'arrive_destination',
        'depart_datetime',
        'arrive_datetime',
        'number_of_nights',
    ];
    // Define relationships if needed
    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }
}
