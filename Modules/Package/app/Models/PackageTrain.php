<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageTrain extends Model
{
    use HasFactory;

    protected $table = 'package_train';

    protected $fillable = [
        'itinerary_id',
        'package_id',
        'train_name',
        'train_number',
        'class',
        'from_station',
        'to_station',
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
