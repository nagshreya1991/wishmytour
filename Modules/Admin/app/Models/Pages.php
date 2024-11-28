<?php

namespace Modules\Admin\app\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    protected $fillable = ['title','slug','content','meta_title','meta_description','meta_keywords','is_published'];
   
}