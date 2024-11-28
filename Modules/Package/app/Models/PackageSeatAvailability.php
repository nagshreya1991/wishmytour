<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageSeatAvailability extends Model
{
    use HasFactory;

    protected $table = "package_seat_availability";

    protected $fillable = [
        'package_id',
        'date',
        'seat',
        'cost',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
   
}

