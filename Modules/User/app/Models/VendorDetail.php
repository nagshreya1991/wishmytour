<?php
namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Model;

class VendorDetail extends Model
{

    protected $fillable = [
        'vendor_id',
        'name',
        'phone_number',
        'photo',
        'pan_number',
        'address',
    ];
}