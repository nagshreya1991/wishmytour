<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageSightseeingGallery extends Model
{
    use HasFactory;

    protected $table = 'package_sightseeing_gallery';

    protected $fillable = [
        'sightseeing_id ',
        'path',
        
    ];
    // Define relationships if needed
    public function sightseeing()
    {
        return $this->belongsTo(PackageSightseeing::class, 'sightseeing_id');
    }
}
