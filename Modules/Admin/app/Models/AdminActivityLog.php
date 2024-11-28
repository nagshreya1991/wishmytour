<?php

namespace Modules\Admin\app\Models;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $fillable = ['admin_id', 'action', 'description'];
}
