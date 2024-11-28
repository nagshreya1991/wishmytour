<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;

class PackageBulkDiscount extends Model
{
    protected $fillable = ['min_pax', 'discount'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
