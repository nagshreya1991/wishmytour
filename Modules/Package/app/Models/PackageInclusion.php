<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;

class PackageInclusion extends Model
{
    protected $fillable = ['package_id', 'name'];
}
