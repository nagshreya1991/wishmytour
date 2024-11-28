<?php namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class AgentDetails extends Model
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'agent_details';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'agent_code',
        'group_percentage',
        'group_name',
        'bank_person_name',
        'bank_acc_no',
        'ifsc_code',
        'bank_name',
        'branch_name',
        'cancelled_cheque',
       // 'authorization_letter',
        'bank_verified',
        'is_verified',
        'profile_img',
        'address',
        'pan_number',
        'pan_card_file',
      //  'authorization_file'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agent) {
            $agent->agent_code = self::generateTravelAgentCode($agent->id);
        });
    }

    private static function generateTravelAgentCode($agentId)
    {
        $prefix = 'WMTP'; // Fixed prefix for travel agent code
        $randomAlphanumeric = strtoupper(substr(md5(mt_rand()), 0, 5)); // 5 random alphanumeric characters
        return $prefix . $agentId . $randomAlphanumeric;
    }
}