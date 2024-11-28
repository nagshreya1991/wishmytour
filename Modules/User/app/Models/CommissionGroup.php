<?php

namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Database\Factories\CommissionGroupFactory;

class CommissionGroup extends Model
{
    protected $fillable = ['name','amount_threshold', 'commission'];
}
