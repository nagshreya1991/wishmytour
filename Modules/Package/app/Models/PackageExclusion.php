<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;

class PackageExclusion extends Model
{
    protected $fillable = ['package_id', 'name'];
}
