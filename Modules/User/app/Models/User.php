<?php namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\User\app\Models\AgentDetails;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    const ROLE_ADMIN = 1;
    const ROLE_CUSTOMER = 2;
    const ROLE_VENDOR = 3;
    const ROLE_AGENT = 4;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'otp',
        'user_type',
        'email_verified',
        'mobile_verified'
        
    ];

    public function generateOtp(): string
    {
        $otp = strval(random_int(100000, 999999)); // Generate a random 6-digit OTP
        $this->update(['otp' => $otp]);

        return $otp;
    }

    public function verifyOtp($otp): bool
    {
        return $this->otp === $otp;
    }

    public function customerDetail()
    {
        return $this->hasOne(CustomerDetail::class);
    }

    public function vendorDetails()
    {
        return $this->hasOne(Vendor::class);
    }

    public function agentDetails()
    {
        return $this->hasOne(AgentDetails::class, 'user_id');
    }

    public function bookingPayments()
    {
        return $this->hasMany(BookingPayment::class);
    }
}