<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = "states";

    protected $fillable=[
        'name',
        'country_id'
       
    ];
    /**
     * Define the reverse relationship with the Package model.
     */
    // public function packages()
    // {
    //     return $this->hasMany(Package::class);
    // }
}
