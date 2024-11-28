<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{

    protected $table = 'wishlist';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['user_id','package_ids'];
}
