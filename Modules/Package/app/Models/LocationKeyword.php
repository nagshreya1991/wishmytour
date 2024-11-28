<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;

class LocationKeyword extends Model
{
    protected $fillable = [
        'location_id',
        'name',
    ];

    // Define the relationship with the Location model
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
