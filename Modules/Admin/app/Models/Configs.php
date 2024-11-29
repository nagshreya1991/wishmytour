<?php

namespace Modules\Admin\app\Models;

use Illuminate\Database\Eloquent\Model;

class Configs extends Model
{
    protected $fillable = ['title','name','value','regex'];
   
}