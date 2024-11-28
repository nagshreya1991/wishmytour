<?php namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class CustomerDetail extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; 

    protected $table = 'customer_details';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'state',
        'city',
        'zipcode',
        'gender',
        'id_type',
        'id_number',
        'id_verified',
        'address',
    ];
    // Define the relationship with State in the other package
    public function state()
    {
        return $this->belongsTo(\Modules\Package\app\Models\State::class, 'state', 'id');
    }

    // Define the relationship with City in the other package
    public function city()
    {
        return $this->belongsTo(\Modules\Package\app\Models\City::class, 'city', 'id');
    }

   
}