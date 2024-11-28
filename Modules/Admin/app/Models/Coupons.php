<?php

namespace Modules\Admin\app\Models;

use Illuminate\Database\Eloquent\Model;

class Coupons extends Model
{
    protected $fillable = ['code','description','discount_type','discount_amount','start_date','end_date','user_id','status','show_status'];
   
}