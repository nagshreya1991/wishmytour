<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StayPlan extends Model
{
    use HasFactory;

    protected $table = "stay_plan";

    protected $fillable=[
        'package_id',
        'cities',
        'total_nights',
       
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'cities');
    }
    
}
