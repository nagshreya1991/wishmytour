<?php namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class VendorPartners extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; 

    protected $table = 'vendor_partners';

    protected $fillable = [
        'vendor_id',
        'partner_name',
        'phone_number',
        'pan_number',
        'address',
        
    ];
    
    // Define relationships if needed
    public function vendor_details()
    {
    return $this->belongsTo(VendorDetails::class, 'vendor_id');
    }

   
}