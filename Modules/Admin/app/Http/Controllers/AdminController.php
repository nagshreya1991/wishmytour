<?php

namespace Modules\Admin\app\Http\Controllers;

use App\Http\Controllers\Controller;

use Couchbase\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\User\app\Models\User;
use Carbon\Carbon;
use DB;
use Modules\Package\app\Models\Package;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\User\app\Models\CustomerDetail;
use Modules\User\app\Models\VendorDetail;
use Modules\User\app\Models\Vendor;
use Modules\User\app\Models\CommissionGroup;
use App\Helpers\NotificationHelper;
use App\Helpers\Helper;
use Modules\Package\app\Models\City;
use Modules\Package\app\Models\PackageHotelGalleryImage;
use Modules\Admin\app\Http\Services\AdminService;
use Modules\Package\app\Models\Themes;
use App\Models\Notification;
use Modules\Booking\app\Models\Booking;
use Modules\Booking\app\Models\BookingCustomerDetails;
use Modules\Booking\app\Models\BookingRoom;
use Modules\Booking\app\Models\BookingDate;
use Modules\Booking\app\Models\BookingPassenger;
use Modules\Booking\app\Models\BookingPayment;
use Modules\Booking\app\Models\BookingCancellation;
use Modules\Admin\app\Models\Coupons;
use Modules\User\app\Models\AgentDetails;
use Modules\Package\app\Models\PackageMessage;
use Modules\Package\app\Models\PackageSightseeingGallery;
use Modules\Booking\app\Models\BookingCommission;


class AdminController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }


    public function index()
    {
        return view('backend.index');

    }

    public function dashboard()
    {
        $customerCount = User::where('user_type', 2)->count();
        $vendorCount = User::where('user_type', 3)->count();
        $packageCount = Package::where('status', 1)->count();
        $bookingCount = Booking::where('booking_status', 3)->count();
        return view('backend.dashboard', compact('customerCount', 'vendorCount', 'packageCount', 'bookingCount'));
    }

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        if ($user->user_type !== 1) {
            return back()->withErrors(['email' => 'Unauthorized'])->withInput();
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended('/admin/dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    //User list
    public function customers()
    {

        //$users = User::where('user_type', 2)->with('customerDetail')->get();
        $users = CustomerDetail::select(
            'customer_details.*',
            'customer_details.first_name as first_name',
            'customer_details.last_name as last_name',
          //  'users.name as user_id',  // Alias for the user's name
            'users.email',
            'users.mobile'
        )
            ->join('users', 'customer_details.user_id', '=', 'users.id')
            ->where('users.user_type', User::ROLE_CUSTOMER)
            ->get();

        return view('backend.users.index', compact('users'));
    }

    // Function to show the edit form
    public function customerEdit($id)
    {
        $user = User::findOrFail($id);
        return view('backend.users.edit', compact('user'));
    }

    public function customerUpdate(Request $request, $id)
    {

      
        $user = User::findOrFail($id);

        // $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->verified = $request->verified;
        //  $user->status = $request->status;


        // Save the user
        $user->save();

        // Check if customerDetail exists, if not, create a new one
        if (!$user->customerDetail) {
            $customerDetail = new CustomerDetail();
            $user->customerDetail()->save($customerDetail);
        } else {
            $customerDetail = $user->customerDetail;
        }

        // Update CustomerDetail data
        $customerDetail->first_name = $request->first_name;
        $customerDetail->last_name = $request->last_name;
        $customerDetail->gender = $request->gender;
        $customerDetail->address = $request->address;
        $customerDetail->zipcode = $request->zipcode;
        $customerDetail->id_type = $request->id_type;
        $customerDetail->id_number = $request->id_number;
        $customerDetail->id_verified = $request->id_verified;
        // $customerDetail->status = $request->status;

        $customerDetail->save();


        return redirect()->route('admin.customers')->with('success', 'User updated successfully');
    }
     /**
     * Display detailed information for a specific customer.
     *
     * @param int $id
     * @return View
     */
    public function customerShow($id)
    {
        $customer = CustomerDetail::select(
                'customer_details.*',
                'customer_details.first_name as first_name',
                'customer_details.last_name as last_name',
                'users.id as user_id',  // Alias for the user's name
                'users.email',
                'users.mobile',
                'cities.city as cities_name',
                'states.name as state_name',
            )
                ->join('users', 'customer_details.user_id', '=', 'users.id')
                ->leftJoin('cities', 'customer_details.city', '=', 'cities.id')
                ->leftJoin('states', 'customer_details.state', '=', 'states.id')
                ->where('customer_details.id',$id)
                ->first();

        if (!$customer) {
            return redirect()->back()->with('error', 'Customer not found');
        }
      
        $customer->joined_at = Carbon::parse($customer->created_at)->format('l, jS F Y h:i A');
        $bookingCount = Booking::where('customer_id', $customer->user_id)->count();
        $completeBookingCount = Booking::where('booking_status', 3)->where('customer_id', $customer->user_id)->count();
        $cancelBookingCount = Booking::where('is_cancelled', 1)->where('customer_id', $customer->user_id)->count();
        $confirmBooking = Booking::where('booking_status', 2)->where('customer_id', $customer->user_id)->count();
        return view('backend.users.show', compact('customer','bookingCount','completeBookingCount','cancelBookingCount','confirmBooking'));
    }

    public function customerBookings($id)
    {
    $bookings = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
        ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
        ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
        ->select(
            'bookings.id as bookings_id',
            'bookings.*',
            'packages.id as package_id',
            'packages.*',
            'vendors.name as vendor_fullname',
            DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
            DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
            DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
        )
        ->groupBy('bookings.id', 'vendors.name')
        ->orderBy('bookings.created_at', 'desc')
        ->where('customer_id', $id)
        ->get()
        ->each(function ($booking) {
            $booking->booking_status = $this->mapBookingStatus($booking->booking_status);
        });
    
         return view('backend.users.bookings', compact('bookings'));
    }

    private function mapBookingStatus($status)
    {
    switch ($status) {
        case 1:
            return 'In process';
        case 2:
            return 'Confirmed';
        case 3:
            return 'Completed';
        case 4:
            return 'Cancelled';
        case 5:
            return 'Modified';
        case 6:
            return 'On Request';
        case 7:
            return 'Disputed';
        default:
            return 'Unknown';
    }
    }

    public function customerCompleteBookings($id)
    {
      //  $bookings = Booking::where('customer_id', $id)->where('booking_status', 3)->get();
        $bookings = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
        ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
        ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
        ->select(
            'bookings.id as bookings_id',
            'bookings.*',
            'packages.id as package_id',
            'packages.*',
            'vendors.name as vendor_fullname',
            DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
            DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
            DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
        )
        ->groupBy('bookings.id', 'vendors.name')
        ->orderBy('bookings.created_at', 'desc')
        ->where('customer_id', $id)
        -> where('booking_status', 3)
        ->get()
        ->each(function ($booking) {
            $booking->booking_status = $this->mapBookingStatus($booking->booking_status);
        });
    
        return view('backend.users.complete-bookings', compact('bookings'));
    }

    public function customerCancelledBookings($id)
    {
       // $bookings = Booking::where('customer_id', $id)->where('is_cancelled', 1)->get();
        $bookings = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
        ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
        ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
        ->select(
            'bookings.id as bookings_id',
            'bookings.*',
            'packages.id as package_id',
            'packages.*',
            'vendors.name as vendor_fullname',
            DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
            DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
            DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
        )
        ->groupBy('bookings.id', 'vendors.name')
        ->orderBy('bookings.created_at', 'desc')
        ->where('customer_id', $id)
        ->where('is_cancelled', 1)
        ->get()
        ->each(function ($booking) {
            $booking->booking_status = $this->mapBookingStatus($booking->booking_status);
        });
        return view('backend.users.cancelled-bookings', compact('bookings'));
    }

    public function customerUpcomingBookings($id)
    {
       // $bookings = Booking::where('customer_id', $id)->where('booking_status', 2)->get();
        $bookings = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
        ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
        ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
        ->select(
            'bookings.id as bookings_id',
            'bookings.*',
            'packages.id as package_id',
            'packages.*',
            'vendors.name as vendor_fullname',
            DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
            DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
            DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
        )
        ->groupBy('bookings.id', 'vendors.name')
        ->orderBy('bookings.created_at', 'desc')
        ->where('customer_id', $id)
        ->where('booking_status', 2)
        ->get()
        ->each(function ($booking) {
            $booking->booking_status = $this->mapBookingStatus($booking->booking_status);
        });
        return view('backend.users.upcoming-bookings', compact('bookings'));
    }

    //Vendor list
    public function vendors()
    {
        $vendors = Vendor::select(
            'vendors.*',
            'vendors.name as vendor_name',
            'users.name as user_name',  // Alias for the user's name
            'users.email',
            'users.mobile'
        )
            ->join('users', 'vendors.user_id', '=', 'users.id')
            ->where('users.user_type', User::ROLE_VENDOR)
            ->get();

        return view('backend.vendors.index', compact('vendors'));
    }

    // Toggle vendor status
    public function toggleStatus(Request $request)
    {
        $vendor = Vendor::find($request->id);
        if ($vendor) {
            $vendor->status = $request->status;
            $vendor->save();

            // $packages = Package::where('user_id', $vendor->user_id)->get();
            // foreach ($packages as $package) {
            //     $package->status = 0;
            //     $package->save();
            // }

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    // Function to show the edit form
    public function vendorEdit($id)
    {
        $vendor = Vendor::select(
            'vendors.*',
            'vendors.name as vendor_name',
            'users.id as userId',
            'users.name as user_name',  // Alias for the user's name
            'users.email',
            'users.mobile'
        )
            ->join('users', 'vendors.user_id', '=', 'users.id')
            ->where('vendors.id', $id)
            ->first(); // Use first() instead of get() to fetch a single vendor

        if (!$vendor) {
            return redirect()->back()->with('error', 'Vendor not found');
        }

        $vendorDetails = VendorDetail::where('vendor_id', $id)->get();//dd($vendorDetails);

        return view('backend.vendors.edit', compact('vendor', 'vendorDetails'));
    }

    public function vendorUpdate(Request $request, $id)
    {

        // Find the vendor by its ID
        $vendor = Vendor::findOrFail($id);
        $user = $vendor->user;
        $vendor->bank_verified = $request->bank_verified ? 1 : 0;
        $vendor->is_verified = $request->is_verified ? 1 : 0;

        // Handle GST details if applicable
        if ($vendor->have_gst == 1) {
            $vendor->gst_verified = $request->gst_verified ? 1 : 0;
            // $vendor->gst_number = $request->gst_number;

            // Handle GST certificate file upload
            //   $this->handleFileUpload($request, $vendor, 'gst_certificate_file', 'gst_', $vendor->user_id);
        }


        // Save vendor details
        $vendor->save();
        // Prepare SMS message
        if ($request->is_verified == 1) {
            $smsMessage = "Dear {$vendor->name}, your registration as a tour operator on WishMyTour has been approved. You can now log in and start listing your tours. Welcome aboard! -From WishMyTour.";
            // Ensure that the user's phone number exists before sending SMS
            if ($user->mobile) {
                $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172070073863632');
            } else {
                throw new \Exception('User phone number not found.');
            }
        }


        // Send notification if bank verification status changed
        if ($request->bank_verified == 1 && $vendor->bank_verified == 0) {
            Helper::sendNotification($user->id, "Your bank details have been verified.");
        }

        // Send notification if GST and bank verification are complete
        if ($request->gst_verified == 1 && $request->bank_verified == 1 && $vendor->gst_verified == 0) {
            Helper::sendNotification($user->id, "Your registration as a tour operator has been approved. You can now log in and start listing your tours. Welcome aboard!");
        }

        return redirect()->route('admin.vendors')->with('success', 'Vendor details updated successfully');
    }

    public function vendorPackages($id)
    {
        $allpackages = Package::with([
            'religion', 'stayPlans'
        ])
            ->join('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftJoin('cities', 'packages.destination_city_id', '=', 'cities.id')
            // ->leftJoin('type_of_tour_packages', 'packages.type_of_tour_packages_id', '=', 'type_of_tour_packages.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->select(
                'packages.*',
                'states.name as state_name',
                'cities.city as cities_name',
                //  'type_of_tour_packages.name as type_name',
                'vendors.name as vendor_name',
            )
            ->where('packages.user_id', '=', $id)
            ->orderBy('packages.updated_at', 'desc')
            ->get();


        foreach ($allpackages as $package) {
            // Determine status
            if ($package->status == 0) {
                $status = "Inactive";
            } elseif ($package->status == 1) {
                $status = "Active";
            } elseif ($package->status == 2) {
                $status = "Approval Pending";
            } elseif ($package->status == 3) {
                $status = "Archive";
            } else {
                $status = "";
            }


            $originCityName = City::where('id', $package->origin_city_id)->value('city');


            $package->status_text = $status;
            $package->origin_city_name = $originCityName;
        }
        return view('backend.vendors.packages', compact('allpackages'));
    }

    //Vendor Package list



    public function packages()
    {

        $allpackages = Package::with([
            'religion',
            'stayPlans'
        ])
            ->join('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftJoin('cities', 'packages.destination_city_id', '=', 'cities.id')
            //->leftJoin('type_of_tour_packages', 'packages.type_of_tour_packages_id', '=', 'type_of_tour_packages.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->select(
                'packages.*',
                'states.name as state_name',
                'cities.city as cities_name',
                // 'type_of_tour_packages.name as type_name',
                'vendors.name as vendor_name',
            )
            ->where('packages.status', '!=', 3)
            ->orderBy('packages.updated_at', 'asc')
            ->get();


        foreach ($allpackages as $package) {
            // Determine status
            if ($package->status == 0) {
                $status = "Inactive";
            } elseif ($package->status == 1) {
                $status = "Active";
            } elseif ($package->status == 2) {
                $status = "Approval Pending";
                if ($package->vendor_verified == 0) {
                    $status = "Acceptance Pending";
                }
            } elseif ($package->status == 3) {
                $status = "Archive";
            } else {
                $status = "";
            }


            $originCityName = City::where('id', $package->origin_city_id)->value('city');


            $package->status_text = $status;
            $package->origin_city_name = $originCityName;
        }

        return view('backend.packages.index', compact('allpackages'));
    }

    //Package list

    public function packagesView($id)
    {
        //dd(auth()->id());
        // Fetch the package details
        $package = $this->adminService->getPackageDetails($id);

        // Convert to object if $package is an array
        if (is_array($package)) {
            $package = json_decode(json_encode($package));
        }

        // Check if the package exists
        if (!$package) {
            abort(404, 'Package not found');
        }

        // Initialize gallery images
        $galleryImages = [];
        // Transform the itinerary
        $itinerary = $this->transformItinerary($package->itinerary);

        // Fetch origin city name
        $originCityName = City::where('id', $package->origin_city_id)->value('city');

        // Set other package details
        $mediaLinks = $package->mediaLinks ?? [];
        $addons = $package->addons ?? collect([]);
        $transformedAddons = $addons->map(function ($addon) {
            return [
                'title' => $addon->title,
                'description' => $addon->description,
                'price' => $addon->price
            ];
        })->toArray();
        $seatAvailability = $package->seatAvailability ?? [];
        //  $inclusionList = $package->inclusions ?? [];
        //  $exclusionList = $package->exclusions ?? [];
        $stayPlan = $package->stay_plan;
        $totalNights = $package->total_days - 1; // Assuming total_days includes the first day as a night

        $transformedPackage = [
            'package_id' => $package->id,
            'vendor_name' => $package->vendor_name,
            'package_name' => $package->name,
            'total_days' => (int)$package->total_days,
            'total_days_count' => "{$package->total_days} days and $totalNights night" . ($totalNights > 1 ? 's' : ''),
            'total_nights_count' => (int)$package->trains()->count(),
            'starting_price' => isset($package->starting_price) ? round($package->starting_price) : null,
            'origin' => $originCityName,
            'destination_state_id' => $package->destination_state_id,
            'state_name' => $package->state_name,
            'destination_city_id' => $package->destination_city_id,
            'cities_name' => $package->cities_name,
            'keywords' => $package->keywords,
            'transportation_name' => $package->transportation->name ?? null,
            'hotel_star' => $package->hotel_star_id ?? null,
            'religion' => $package->religion->name ?? null,
            'tour_type' => $package->tour_type,
            'trip' => $package->tour_type == 1 ? 'Domestic' : 'International',
            'trip_type' => $package->trip_type,
            'type_of_tour_packages' => $package->trip_type == 1 ? 'Standard' : 'Weekend',
            'featured_image_path' => $package->featured_image_path,
            // 'inclusions_in_package' => $inclusions,
            'gallery_images' => $galleryImages,
            'admin_verified' => $package->admin_verified,
            'vendor_verified' => $package->vendor_verified,
            'itinerary' => $itinerary,
            'overview' => $package->overview,
            // 'inclusions_list' => $inclusionList,
            // 'exclusions_list' => $exclusionList,
            'terms_and_condition' => $package->terms_and_condition,
            'payment_policy' => $package->payment_policy,
            'cancellation_policy' => $package->cancellation_policy,
            'child_discount' => $package->child_discount,
            'single_occupancy_cost' => $package->single_occupancy_cost,
            //'offseason_from_date' => $package->offseason_from_date,
            // 'offseason_to_date' => $package->offseason_to_date,
            // 'offseason_price' => $package->offseason_price,
            //   'onseason_from_date' => $package->onseason_from_date,
            // 'onseason_to_date' => $package->onseason_to_date,
            //  'onseason_price' => $package->onseason_price,
            'total_seat' => $package->total_seat,
            'bulk_no_of_pax' => $package->bulk_no_of_pax,
            'pax_discount_percent' => $package->pax_discount_percent,
            'created_at' => $package->created_at,
            'stay_plan' => $stayPlan,
            'media_link' => $mediaLinks,
            'addons' => $transformedAddons,
            'seat_availability' => $seatAvailability,
            'tour_circuit' => $package->tour_circuit,
            'location' => $package->location,
            'status' => $package->status,
            'is_transport' => $package->is_transport,
            'is_flight' => $package->is_flight,
            'is_train' => $package->is_train,
            'is_hotel' => $package->is_hotel,
            'is_meal' => $package->is_meal,
            'is_sightseeing' => $package->is_sightseeing,
            'triple_sharing_discount' => $package->triple_sharing_discount,
        ];

        // Fetch messages count
        $messagesCount = PackageMessage::where('package_id', $transformedPackage['package_id'])
            ->where('receiver_id', auth()->id()) // Assuming receiver_id column existsreceiver_id

            ->where('is_read', '0')
            ->count();

        // Fetch messages
        $messages = PackageMessage::where('package_id', $transformedPackage['package_id'])->get();

        return view('backend.packages.view', compact('transformedPackage', 'messagesCount', 'messages'));
    }

    private function transformItinerary($itineraries)
    {
        return $itineraries->map(function ($itinerary) {
            // Initialize transformed data arrays
            $transformedFlights = [];
            $transformedTrains = [];
            $transformedHotels = [];
            $sightseeing = $itinerary->sightseeing ?? [];
            // Process hotels
            foreach ($itinerary->hotel ?? [] as $hotel) {
                // Get hotel gallery images for each hotel
                $hotelGalleryImages = PackageHotelGalleryImage::where('hotel_id', $hotel->id)
                    ->select('id', 'path')
                    ->pluck('path', 'id')
                    ->toArray();

                $galleryImages = [];
                foreach ($hotelGalleryImages as $id => $path) {
                    $galleryImages[] = [
                        'id' => $id,
                        'path' => $path,
                    ];
                }

                $transformedHotels[] = [
                    'hotel_id' => $hotel->id,
                    'hotel_name' => $hotel->name,
                    'star' => $hotel->rating,
                    'hotel_gallery' => $galleryImages,
                ];
            }


            // Transform sightseeing
            $transformedSightseeing = [];
            foreach ($sightseeing as $st) {
                $sightseeingGalleryImages = PackageSightseeingGallery::where('sightseeing_id', $st->id)
                    ->select('id', 'path')
                    ->pluck('path', 'id')
                    ->toArray();

                $galleryImages = [];
                foreach ($sightseeingGalleryImages as $id => $path) {
                    $galleryImages[] = [
                        'id' => $id,
                        'path' => $path,
                    ];
                }

                $transformedSightseeing[] = [
                    'sightseeing_id' => $st->id,
                    'morning' => $st->morning,
                    'afternoon' => $st->afternoon,
                    'evening' => $st->evening,
                    'night' => $st->night,
                    'sightseeing_gallery' => $galleryImages,

                ];
            }


            // Process flights
            foreach ($itinerary->flight ?? [] as $flight) {
                // Retrieve city names for depart and arrive destinations
                $departCity = City::find($flight['depart_destination']);
                $arriveCity = City::find($flight['arrive_destination']);

                $transformedFlights[] = [
                    'id' => $flight['id'],
                    'depart_destination' => [
                        'id' => $flight['depart_destination'],
                        'name' => $departCity ? $departCity->city : null,
                    ],
                    'arrive_destination' => [
                        'id' => $flight['arrive_destination'],
                        'name' => $arriveCity ? $arriveCity->city : null,
                    ],
                    'depart_datetime' => $flight['depart_datetime'] ?? null,
                    'arrive_datetime' => $flight['arrive_datetime'] ?? null,
                ];
            }

            // Process trains
            foreach ($itinerary->train ?? [] as $train) {
                // Retrieve city names for depart and arrive destinations
                $departCity = City::find($train['from_station']);
                $arriveCity = City::find($train['to_station']);

                $transformedTrains[] = [
                    'id' => $train['id'],
                    'depart_destination' => [
                        'id' => $train['from_station'],
                        'name' => $departCity ? $departCity->city : null,
                    ],
                    'arrive_destination' => [
                        'id' => $train['to_station'],
                        'name' => $arriveCity ? $arriveCity->city : null,
                    ],
                    'train_name' => $train['train_name'] ?? null,
                    'train_number' => $train['train_number'] ?? null,
                    'class' => $train['class'] ?? null,
                    'depart_datetime' => $train['depart_datetime'] ?? null,
                    'arrive_datetime' => $train['arrive_datetime'] ?? null,
                ];
            }

            // Retrieve city name for the place_name
            $placeNameCity = City::find($itinerary->place_name);
            $placeName = $placeNameCity ? $placeNameCity->city : null;

            // Return transformed itinerary data
            return [
                'itinerary_id' => $itinerary->id,
                'day' => $itinerary->day,
                'place_name' => $placeName,
                'itinerary_title' => $itinerary->itinerary_title,
                'itinerary_description' => $itinerary->itinerary_description,
                'meal' => $itinerary->meal,
                'flights' => $transformedFlights,
                'trains' => $transformedTrains,
                'local_transport' => $itinerary->local_transport ?? [],
                // 'sightseeing' => $itinerary->sightseeing ?? [],
                'hotels' => $transformedHotels,
                'sightseeing' => $transformedSightseeing,
            ];
        });
    }

    public function packagesUpdate(Request $request, $id)
    {
        $request->validate([
            'admin_verified' => 'required|boolean',
        ]);

        try {
            \DB::beginTransaction();

            $package = Package::findOrFail($id);
            $package->admin_verified = $request->admin_verified;
            $package->admin_verified_at = now();
            $package->save();

            $user = User::findOrFail($package->user_id);

            $vendor = Vendor::where('user_id', $package->user_id)->firstOrFail();

            Helper::sendNotification($package->user_id, "Your tour package `" . $package->name . "` has been approved. Please accept your tour package pricing with convenience fees to make it live and ready for booking.");

            // Prepare SMS message
            $smsMessage = "Dear {$vendor->name}, your tour package {$package->name} id {$package->package_code} on WishMyTour has been approved. Please accept your tour package pricing with convenience fees in your portal to make it live and ready for booking. -From Wishmytour.";

            // Ensure that the user's phone number exists before sending SMS
            if ($user->mobile) {
                $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172070106434967');
            } else {
                throw new \Exception('User phone number not found.');
            }

            \DB::commit();

            return redirect()->route('admin.packages')->with('success', 'Package updated successfully');
        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update package: ' . $e->getMessage());
        }
    }

    public function renderHeader()
    {

        $userId = auth()->id();

        $messages = Helper::getMessagesByUserId($userId);

        return view('header', ['messages' => $messages]);
    }

    public function notificationList()
    {
        $userId = auth()->id();
        $notifications = Helper::getMessagesByUserId($userId);
        Notification::markAllAsRead($userId);
        return view('backend.notification.index', compact('notifications'));
    }

    // public function notificationList()
    // {
    //     $userId = auth()->id();
    //     $notifications = NotificationHelper::getMessagesByUserId($userId);
    //     return view('backend.notifications', compact('notifications'));
    //   //  return view('backend.packages.view', compact('transformedPackage'));
    // }

    public function deleteNotification($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted successfully');

    }

    public function bulkDeleteNotifications(Request $request)
    {
        $notificationIds = $request->input('ids');

        try {
            Notification::whereIn('id', $notificationIds)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function markAllNotificationsAsRead()
    {
        try {
            $userId = auth()->id();
            // Mark all notifications as read for the logged-in user
            Notification::markAllAsRead($userId);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function clearAllNotifications()
    {
        try {
            $userId = auth()->id();
            // Mark all notifications as read for the logged-in user
            Notification::where('receiver_id', $userId)->update(['read' => true]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function removeAllNotifications()
    {
        try {
            $userId = auth()->id();
            // Delete all notifications for the logged-in user
            Notification::where('receiver_id', $userId)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function booking()//completed booking
    {
        $bookings = $this->adminService->getBookings();

        $resultBookings = $bookings->map(function ($booking) {
            $package = Package::find($booking->package_id);

            if ($package) {
                $totalDays = $package->total_days;
                $totalNights = $package->fetchStayPlans()->sum('total_nights');
                $totalDaysAndNights = "$totalDays days and $totalNights night" . ($totalNights > 1 ? 's' : '');
                $booking->total_days = $totalDaysAndNights;
            } else {
                $booking->total_days = "Package not found";
            }

            return $booking;
        });

        $confirmbookings = $this->adminService->getConfirmBookings();

        $confirmbookings = $confirmbookings->map(function ($confirmbooking) {
            $package = Package::find($confirmbooking->package_id);

            if ($package) {
                $totalDays = $package->total_days;
                $totalNights = $package->fetchStayPlans()->sum('total_nights');
                $totalDaysAndNights = "$totalDays days and $totalNights night" . ($totalNights > 1 ? 's' : '');
                $confirmbooking->total_days = $totalDaysAndNights;
            } else {
                $confirmbooking->total_days = "Package not found";
            }

            return $confirmbooking;
        });

        $cancelbookings = $this->adminService->getCancelBookings();

        $cancelbookings = $cancelbookings->map(function ($cancelbooking) {
            $package = Package::find($cancelbooking->package_id);

            if ($package) {
                $totalDays = $package->total_days;
                $totalNights = $package->fetchStayPlans()->sum('total_nights');
                $totalDaysAndNights = "$totalDays days and $totalNights night" . ($totalNights > 1 ? 's' : '');
                $cancelbooking->total_days = $totalDaysAndNights;
            } else {
                $cancelbooking->total_days = "Package not found";
            }

            return $cancelbooking;
        });

        $disputedbookings = $this->adminService->getDisputedBookings(); 

        $disputedbookings = $disputedbookings->map(function ($disputedbooking) {
            $package = Package::find($disputedbooking->package_id);

            if ($package) {
                $totalDays = $package->total_days;
                $totalNights = $package->fetchStayPlans()->sum('total_nights');
                $totalDaysAndNights = "$totalDays days and $totalNights night" . ($totalNights > 1 ? 's' : '');
                $disputedbooking->total_days = $totalDaysAndNights;
            } else {
                $disputedbooking->total_days = "Package not found";
            }

            return $disputedbooking;
        });

        // dd($resultBookings);
        return view('backend.booking.index', compact('resultBookings','confirmbookings' ,'cancelbookings','disputedbookings'));
    }


    //Booking list

    public function bookingView($id)
    {

        $bookingdetails = $this->adminService->getBookingDetails($id);

        if (!$bookingdetails) {
            abort(404, 'Booking not found');
        }

        $booking_passengers = BookingPassenger::where('booking_id', $id)->get();
        $booking_payments = BookingPayment::where('booking_id', $id)->get();
        $booking_cancellation = BookingCancellation::where('booking_id', $id)->get();

        //dd($booking_passengers);
        return view('backend.booking.view', compact('bookingdetails', 'booking_passengers','booking_payments','booking_cancellation'));
    }

    public function packagesMessage($id)
    {
        PackageMessage::where('package_id', $id)
            ->where('receiver_id', auth()->id())
            ->update(['is_read' => '1']);

        $allmsg = PackageMessage::where('package_id', $id)
            ->leftJoin('vendors', 'package_message.sender_id', '=', 'vendors.user_id')
            ->select('package_message.*', 'vendors.name as sender_name')
            ->orderBy('package_message.created_at', 'asc')
            ->get();


        return view('backend.packages.messages', [
            'allmsg' => $allmsg,
            'package_id' => $id
        ]);
    }

    //  public function coupons()
    //  {

    //          $coupons = Coupons::get();

    //     // dd($resultBookings);
    //      return view('backend.coupon.index', compact('coupons'));
    //  }

    public function storeMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $package = Package::findOrFail($id);

        PackageMessage::create([
            'package_id' => $id,
            'sender_id' => auth()->id(),
            'receiver_id' => $package->user_id, // Make sure user_id exists on the Package model
            'message' => $request->message,
            'package_name' => $package->name,
            //   'is_read' => 0,
        ]);

        return redirect()->back();
    }

    public function bulkPayout(Request $request)
    {
        // Validate incoming requests if needed
        $agentIds = $request->input('agent_ids', []);
        $month = $request->input('month');
        $year = $request->input('year');

        // Log incoming data for debugging
        Log::info('Bulk Payout Request:', ['agent_ids' => $agentIds, 'month' => $month, 'year' => $year]);

        // Convert agent IDs array to a string for raw SQL query
        $agentIdsString = implode(',', array_map('intval', $agentIds));

        // Build the base query
        $query = "UPDATE booking_commissions 
              SET claim_status = 2, payment_status = 2, payment_date = NOW()
              WHERE user_id IN ($agentIdsString) 
              AND payment_status = 1 
              AND claim_status = 1";

        // Add date filter if both month and year are provided
        if ($month && $year) {
            // Construct the date string for filtering
            $dateString = sprintf('%04d-%02d', $year, $month);

            // Log date string for debugging
            Log::info('Date String:', ['dateString' => $dateString]);

            // Add the date filter to the query
            $query .= " AND DATE_FORMAT(claimed_date, '%Y-%m') = '$dateString'";
        }

        // Log the final query for debugging
        Log::info('SQL Query:', ['query' => $query]);

        // Execute the raw query
        $updated = DB::statement($query);

        // Log the number of records updated
        Log::info('Records Updated:', ['updated' => $updated]);

        // Return the appropriate response
        if ($updated) {
            return redirect()->back()->with('success', 'Bulk payout successful.');
        } else {
            return redirect()->back()->with('error', 'No records updated.');
        }
    }

    private function handleFileUpload($request, $vendorDetails, $fieldName, $prefix, $userId)
    {
        if ($request->hasFile($fieldName)) {
            $file = $request->file($fieldName);

            if ($file->isValid()) {
                $filename = $prefix . uniqid() . '.' . $file->getClientOriginalExtension();
                $directory = 'public/vendors/' . $userId;
                $file->storeAs($directory, $filename);
                $vendorDetails->{$fieldName} = 'vendors/' . $userId . '/' . $filename;
            } else {
                return response()->json(['success' => false, 'msg' => 'Invalid ' . $fieldName . ' file'], 400);
            }
        }
    }
    public function getCompletedBookings()
    {
        $bookings = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
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
            ->where('bookings.booking_status', 3)
            ->get();

        return DataTables::of($bookings)
            ->addColumn('action', function($row){
                return view('backend.partials.booking_actions', compact('row'))->render();
            })
            ->make(true);
    }

    public function getConfirmedBookings()
    {
        $bookings = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
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
            ->where('bookings.booking_status', 2)
            ->get();

        return DataTables::of($bookings)
            ->addColumn('action', function($row){
                return view('backend.partials.booking_actions', compact('row'))->render();
            })
            ->make(true);
    }

    public function getCancelledBookings()
    {
        $bookings = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
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
            ->where('bookings.booking_status', 4)
            ->get();

        return DataTables::of($bookings)
            ->addColumn('action', function($row){
                return view('backend.partials.booking_actions', compact('row'))->render();
            })
            ->make(true);
    }
}