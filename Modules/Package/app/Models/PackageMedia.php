<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageMedia extends Model
{
    use HasFactory;

    protected $table = "package_media";

    protected $fillable = [
        'package_id',
        'media_link',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
   
}

