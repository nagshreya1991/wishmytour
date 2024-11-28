<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    use HasFactory;

    protected $table = "religion";

    protected $fillable=[
        'name',
       
    ];
    /**
     * Define the reverse relationship with the Package model.
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
