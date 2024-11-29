<?php

namespace Modules\Admin\App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Modules\User\app\Models\User;
use Illuminate\Support\Facades\Validator;
use Modules\User\app\Models\CustomerDetail;
use Modules\User\app\Models\VendorDetails;
use Modules\User\app\Models\VendorPartners;
use Modules\User\app\Models\VendorDirectors;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Modules\User\app\Models\VendorBroadLocation;
use Carbon\Carbon;
use App\Helpers\Helper;
use Modules\Package\app\Models\Package;
use Illuminate\Support\Facades\Log;
use Modules\Booking\app\Models\Booking;
use Modules\Booking\app\Models\BookingCustomerDetails;
use Modules\Booking\app\Models\BookingRoom;
use Modules\Booking\app\Models\BookingDate;
use Modules\Booking\app\Models\BookingPassenger;
use Modules\Package\app\Models\PackageMessage;
use Modules\Booking\app\Models\BookingCommission;
use Modules\User\app\Models\AgentDetails;

class AdminService
{
    public function getPackageDetails($id)
    {
        try {
            $package = Package::with(['religion', 'cityStayPlans', 'inclusions', 'exclusions'])
                ->leftJoin('states', 'packages.destination_state_id', '=', 'states.id')
                ->leftJoin('cities', 'packages.destination_city_id', '=', 'cities.id')
                // ->leftJoin('type_of_tour_packages', 'packages.type_of_tour_packages_id', '=', 'type_of_tour_packages.id')
                ->leftJoin('users', 'packages.user_id', '=', 'users.id')
                ->leftJoin('stay_plan', 'packages.id', '=', 'stay_plan.package_id')
                ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
                ->where('packages.id', $id)
                ->select(
                    'packages.*',
                    'states.name as state_name',
                    'cities.city as cities_name',
                    //  'type_of_tour_packages.name as type_name',
                    'vendors.name as vendor_name'
                )
                ->groupBy('packages.id', 'vendors.name')
                ->first();

            if (!$package) {
                return response()->json(['res' => false, 'msg' => 'Package not found'], 404);
            }

            $package->cancellation_policy = explode(',', $package->cancellation_policy);
            $package->stay_plan = $package->getItineraries($id);
            // fetch stay plan data according itineraries
            // $package->stay_plan = $this->transformItinerary($id);

            return $package;
        } catch (\Exception $e) {
            Log::error('Error occurred while fetching package details: ' . $e->getMessage());
            return ['res' => false, 'msg' => $e->getMessage()];
        }
    }

    public function getBookings()//Completed booking list
    {
        return Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.*',
                'vendors.name as vendor_fullname',
                DB::raw('MAX(customer_details.first_name) as customer_first_name'),
                DB::raw('MAX(customer_details.last_name) as customer_last_name'),
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
            )
            ->groupBy('bookings.id', 'vendors.name')
            ->orderBy('first_booking_date', 'asc')
            ->where('bookings.booking_status', 3)
            ->get();
    }
    public function getConfirmBookings()//confirm booking list
    {
        return Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.*',
                'vendors.name as vendor_fullname',
                DB::raw('MAX(customer_details.first_name) as customer_first_name'),
                DB::raw('MAX(customer_details.last_name) as customer_last_name'),
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
            )
            ->groupBy('bookings.id', 'vendors.name')
            ->orderBy('first_booking_date', 'asc')
            ->where('bookings.booking_status', 2)
            ->get();
    }

    public function getCancelBookings()//cancel booking list
    {
        return Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.*',
                'vendors.name as vendor_fullname',
                DB::raw('MAX(customer_details.first_name) as customer_first_name'),
                DB::raw('MAX(customer_details.last_name) as customer_last_name'),
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
            )
            ->groupBy('bookings.id', 'vendors.name')
            ->orderBy('first_booking_date', 'asc')
            ->where('bookings.booking_status', 4)
            ->where('bookings.is_cancelled', 1)
            ->get();
    }

    public function getDisputedBookings()//Disputed booking list
    {
        return Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.*',
                'vendors.name as vendor_fullname',
                DB::raw('MAX(customer_details.first_name) as customer_first_name'),
                DB::raw('MAX(customer_details.last_name) as customer_last_name'),
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
            )
            ->groupBy('bookings.id', 'vendors.name')
            ->orderBy('first_booking_date', 'asc')
            ->where('bookings.booking_status', 7)
            ->get();
    }


    public function getBookingDetails($id)
    {
        try {
            $booking = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
                ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
                ->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
                ->leftJoin('booking_customer_details', 'bookings.id', '=', 'booking_customer_details.booking_id')
                ->leftJoin('cities', 'customer_details.city', '=', 'cities.id')
                ->leftJoin('states', 'customer_details.state', '=', 'states.id')
                ->leftJoin('users', 'bookings.customer_id', '=', 'users.id')
                ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
                ->select(
                    'bookings.*',
                    'bookings.id as bookings_id',
                    'booking_customer_details.*',
                    'vendors.*',
                    'customer_details.*',
                    'users.id as user_id',
                    'packages.*',
                    'vendors.name as vendor_fullname',
                    'customer_details.id as customer_id',
                    'customer_details.first_name as customer_first_name',
                    'customer_details.last_name as customer_last_name',
                    'booking_customer_details.name as booking_customer_name',
                    'booking_customer_details.address as booking_customer_address',
                    'booking_customer_details.email as booking_customer_email',
                    'booking_customer_details.phone_number as booking_customer_phone_number',
                    'booking_customer_details.pan_number as booking_customer_pan_number',
                    'cities.city as customer_city',
                    'states.name as customer_state',
                    DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                    DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date')
                )
                ->where('bookings.id', $id)
                ->first();

            if (!$booking) {
                return null;
            }

            return $booking;
        } catch (\Exception $e) {
            Log::error('Error occurred while fetching package details: ' . $e->getMessage());
            return null;
        }
    }




    private function mapPaymentStatus($status)
    {
        switch ($status) {
            case 0:
                return 'Open';
            case 1:
                return 'In Process';
            case 2:
                return 'Processed';
            default:
                return 'Unknown';
        }
    }

    private function mapClaimStatus($status)
    {
        switch ($status) {
            case 0:
                return 'Not Claimed';
            case 1:
                return 'Claimed';
            case 2:
                return 'Paid';
            case 3:
                return 'Cancelled';
            default:
                return 'Unknown';
        }
    }

}
