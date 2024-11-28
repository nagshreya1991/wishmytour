<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = "cities";

    protected $fillable=[
        'city',
        'state_id',
        'packages'
    ];
    /**
     * Define the reverse relationship with the Package model.
     */
    // public function packages()
    // {
    //     return $this->hasMany(Package::class);
    // }
}