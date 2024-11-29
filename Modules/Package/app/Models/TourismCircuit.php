<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Package\Database\factories\TourismCircuitFactory;

class TourismCircuit extends Model
{
    protected $fillable = ['name'];
}
