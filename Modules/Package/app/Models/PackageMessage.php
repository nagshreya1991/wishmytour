<?php

namespace Modules\Package\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageMessage extends Model
{
    use HasFactory;

    protected $table = "package_message";

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'package_id',
        'package_name',
        'message',
        'is_read'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
   
}

