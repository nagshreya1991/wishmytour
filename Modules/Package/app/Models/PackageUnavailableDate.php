<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageUnavailableDate extends Model
{
    use HasFactory;

    protected $table = "package_unavailable_date";

    protected $fillable = [
        'package_id',
        'date',
        
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
   
}

