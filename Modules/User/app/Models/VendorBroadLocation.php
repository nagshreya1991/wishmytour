<?php namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class VendorBroadLocation extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; 

    protected $table = 'vendor_broad_location';

    protected $fillable = [
        'vendor_id',
        'state',
        'city',
        
    ];
       // Define relationships if needed
       public function vendor_details()
       {
       return $this->belongsTo(VendorDetails::class, 'vendor_id');
       }

   
}