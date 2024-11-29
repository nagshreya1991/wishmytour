<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;

class PackageRate extends Model
{
    protected $fillable = [
        'package_id',
        'start_date',
        'end_date',
        'price',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
