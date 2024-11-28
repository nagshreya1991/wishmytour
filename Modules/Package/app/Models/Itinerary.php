<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
    'package_id',
    'day',
    'place_name',
    'itinerary_title',
    'itinerary_description',
    'meal', 
    'created_at',
    'updated_at',
    ];

    // Define relationships if needed
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function flight()
    {
        return $this->hasMany(PackageFlight::class, 'itinerary_id', 'id');
    }
    public function train()
    {
        return $this->hasMany(PackageTrain::class, 'itinerary_id', 'id');
    }
    public function local_transport()
    {
        return $this->hasMany(PackageLocalTransport::class, 'itinerary_id', 'id');
    }
    public function sightseeing()
    {
        return $this->hasMany(PackageSightseeing::class, 'itinerary_id', 'id');
    }
    public function hotel()
    {
        return $this->hasMany(PackageHotel::class, 'itinerary_id', 'id');
    }
}
