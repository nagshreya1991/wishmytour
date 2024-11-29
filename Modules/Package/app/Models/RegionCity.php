<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;

class RegionCity extends Model
{
    protected $fillable = [
        'region_id',
        'city_id',
        'name',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
