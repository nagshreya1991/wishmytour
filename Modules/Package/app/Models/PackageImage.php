<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;

class PackageImage extends Model
{

    protected $fillable = [
        'package_id',
        'path',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}

