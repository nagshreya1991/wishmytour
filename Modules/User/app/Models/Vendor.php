<?php

namespace Modules\User\app\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'have_gst',
        'gst_number',
        'pan_number',
        'gst_certificate_file',
        'pan_card_file',
        'photo',
        'authorization_file',
        'gst_rate',
        'tcs_rate',
        'organization_name',
        'organization_type',
        'address',
        'zip_code',
        'city',
        'state',
        'country',
        'organization_pan_number',
        'organization_pan_file',
        'status',
        'bank_account_number',
        'ifsc_code',
        'bank_name',
        'branch_name',
        'contact_person_name',
        'bank_verified',
        'is_verified',
        'gst_verified',


    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {
            $vendor->vendor_code = self::generateVendorCode($vendor->id);
        });
    }

    private static function generateVendorCode($vendorId)
    {
        $prefix = 'WMTB'; // Fixed prefix for vendor code
        $randomAlphanumeric = strtoupper(substr(md5(mt_rand()), 0, 5)); // 5 random alphanumeric characters
        return $prefix . $vendorId . $randomAlphanumeric;
    }
}
