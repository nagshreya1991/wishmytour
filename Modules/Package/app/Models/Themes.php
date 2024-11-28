<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Themes extends Model
{
    use HasFactory;

    protected $table = "themes";

    protected $fillable=[
        'name',
        'image',
        'icon'
    ];
    /**
     * Define the reverse relationship with the Package model.
     */
    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
