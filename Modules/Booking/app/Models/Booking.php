<?php

namespace Modules\Booking\app\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Booking\app\Models\BookingRoom;
use Modules\Booking\app\Models\BookingDate;
use Modules\Booking\app\Models\BookingCustomerDetails;
use Modules\Booking\app\Models\BookingReportGallery;
use Modules\User\app\Models\CommissionGroup;

class Booking extends Model
{
    protected $fillable = [
    'package_id', 'customer_id', 'booking_number', 'invoice_number' ,'agent_code', 'add_on_id', 'booking_status','is_cancelled','base_amount','coupon_id','addon_total_price','website_price','website_percent','gst_price','gst_percent','tcs','coupon_price','final_price','confirmed_date','complete_date','feedback','rating','report'
    ];

    public function rooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function dates()
    {
        return $this->hasMany(BookingDate::class);
    }

    public function customer()
    {
        return $this->hasOne(BookingCustomerDetails::class);
    }
    // Define the relationship to booking passengers
    public function passengers()
    {
        return $this->hasMany(BookingPassenger::class, 'booking_id');
    }
    public function reportGalleryImages()
    {
        return $this->hasMany(BookingReportGallery::class, 'booking_id');
    }

    public function payments()
    {
        return $this->hasMany(BookingPayment::class, 'booking_id');
    }

    public function cancellations()
    {
        return $this->hasMany(BookingCancellation::class, 'booking_id');
    }

    // Define the relationship to CommissionGroup
    public function commissionGroup()
    {
        return $this->hasOne(CommissionGroup::class, function ($query) {
            // No need to apply conditions here directly
        });
    }

    // Scope to filter bookings from the last 30 days
//    public function scopeLastThirtyDays($query)
//    {
//        return $query->where('created_at', '>=', Carbon::now()->subDays(30));
//    }
//
//    // Calculate total booking amount for the last 30 days for an agent
//    public static function getTotalAmountForLastThirtyDays($agentCode)
//    {
//        return static::where('agent_code', $agentCode)
//            ->lastThirtyDays()
//            ->sum('final_price');
//    }

    // Scope to filter bookings for the current month
    public function scopeCurrentMonth($query)
    {
        return $query->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
    }

    // Calculate total booking amount for the current month for an agent
    public static function getTotalAmountForCurrentMonth($agentCode)
    {
        return static::where('agent_code', $agentCode)
            ->currentMonth()
            ->selectRaw('SUM(base_amount + addon_total_price) as total_amount')
            ->value('total_amount');
    }

    // Fetch the corresponding commission group based on the total amount
    public static function fetchCommissionGroup($agentCode)
    {
        $totalAmount = static::getTotalAmountForCurrentMonth($agentCode);

        $commissionGroup = CommissionGroup::where('amount_threshold', '<=', $totalAmount)
            ->orderByDesc('amount_threshold')
            ->first();

        if ($commissionGroup) {
            return $commissionGroup;
        }

        // Return a default commission group with 0 commission if no matching group is found
        return (object) [
            'commission' => 0,
            'name' => 'No Commission Group',
            'commission_group_id' => null,
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_number = self::generateBookingNumber($booking->id);
        });
    }

    private static function generateBookingNumber($agentId)
    {
        $prefix = 'WMTB'; // Fixed prefix for booking number
        $randomNumbers = mt_rand(10000000, 99999999); // 8 random numbers
        $timestamp = date('ymd'); // Date in yyMMdd format
        return $prefix . $timestamp . $randomNumbers;
    }
}