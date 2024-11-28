<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageLocalTransport extends Model
{
    use HasFactory;

    protected $table = 'package_local_transport';

    protected $fillable = [
        'itinerary_id',
        'car',
        'model',
        'capacity',
        'AC',
    ];
    // Define relationships if needed
    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }
}
