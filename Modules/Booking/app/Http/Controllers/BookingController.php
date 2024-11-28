<?php

namespace Modules\Booking\app\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Booking\app\Http\Services\BookingService;
use Modules\Booking\app\Http\Requests\AddBookingRequest;
use Modules\Booking\app\Http\Requests\AddOnrequestBookingRequest;
use Modules\User\app\Models\User;
use Modules\Package\app\Models\Package;
use Modules\Booking\app\Models\BookingPassenger;
use Modules\Booking\app\Models\BookingMessage;
use Modules\Booking\app\Models\BookingRoom;
use Modules\Booking\app\Models\BookingPayment;
use Modules\Booking\app\Models\BookingCancellation;
use Modules\Package\app\Models\Itinerary;
use Modules\Package\app\Models\PackageTrain;
use Modules\Package\app\Models\PackageFlight;
use Modules\Booking\app\Models\Booking;
use Modules\Package\app\Models\PackageLocalTransport;
use Modules\Package\app\Models\PackageAddon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Modules\Package\app\Models\PackageHotel;
use Modules\Package\app\Models\PackageHotelGalleryImage;
use Modules\Admin\app\Models\Coupons;
use Carbon\Carbon;
use Modules\Package\app\Models\State;
use Modules\Package\app\Models\City;
use Modules\Booking\app\Models\BookingDate;
use Modules\Booking\app\Models\BookingCustomerDetails;
use Modules\User\app\Models\Vendor;
use Modules\User\app\Models\CustomerDetail;
use App\Models\Config;
use Modules\Package\app\Models\PackageInclusion;
use Modules\Package\app\Models\PackageExclusion;
use Modules\Booking\app\Models\BookingCommission;
use Modules\Booking\app\Models\BookingReportGallery;
use Modules\Booking\app\Models\BookingOnRequest;
use Illuminate\Support\Facades\Validator;
use Modules\User\app\Models\AgentDetails;
use Illuminate\Support\Str;
use DateTime;
use DateInterval;
use Mail;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Dompdf\Dompdf;
use Dompdf\Options;

use Modules\User\app\Models\CommissionGroup;
use Illuminate\Support\Facades\File;


class BookingController extends Controller
{
    private $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
     /**
     * Add Booking
     * 
     * This function handles the addition of a new booking. It checks the authenticated user type (customer or agent),
     * and processes the booking request accordingly. For customers, it directly adds the booking.
     * For agents, it verifies the agent's details and handles customer registration if needed, 
     * before proceeding with the booking.
     *
     * @param AddBookingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

     public function addBooking(AddBookingRequest $request)
     {
        if (auth()->check()) {
            $user = auth()->user();
          //  Log::info('User methods: ', get_class_methods($user));

            $requestData = $request->validated();


            if ($user->user_type === User::ROLE_CUSTOMER) {
                $requestData['customer_id'] = $user->id;
                $result = $this->bookingService->addBooking($requestData);
                return response()->json($result);
            } elseif ($user->user_type === User::ROLE_AGENT) {
                $agentDetails = DB::table('agent_details')->where('user_id', $user->id)->first();
                Log::info('Agent Details: ', ['agentDetails' => $agentDetails]);

                if ($agentDetails && $agentDetails->is_verified == 1) {
                    $requestData['agent_code'] = $agentDetails->agent_code;

                    // Check if customer email and phone number exist in users table
                    $existingUser = DB::table('users')
                        ->where('email', $requestData['customer_email'])
                        ->orWhere('mobile', $requestData['customer_phone_number'])
                        ->where('user_type', User::ROLE_CUSTOMER)
                        ->first();

                    if ($existingUser) {
                        // If user exists, fetch details
                        $customerId = $existingUser->id;
                        $customerDetails = CustomerDetail::where('user_id', $customerId)->first();
                        // No need to save if customer details already exist

                        // Include the customer_id in the requestData
                        $requestData['customer_id'] = $customerId;
                    } else {
                        // If user does not exist, register the user
                        $customerId = DB::table('users')->insertGetId([
                            'name' => $requestData['customer_name'],
                            'email' => $requestData['customer_email'],
                            'mobile' => $requestData['customer_phone_number'],
                            'password' => bcrypt('defaultpassword'), // Use a proper default password or generate one
                            'user_type' => User::ROLE_CUSTOMER,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Split customer_name into first_name and last_name
                        $nameParts = explode(' ', $requestData['customer_name']);
                        $firstName = $nameParts[0];
                        $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                        // Save customer details in the customer_details table
                        $customerDetails = new CustomerDetail([
                            'user_id' => $customerId,
                            'first_name' => $firstName,
                            'last_name' => $lastName,
                            'state' => $requestData['customer_state_id'] ?? null,
                            'address' => $requestData['customer_address'] ?? null,
                            'pan_number' => $requestData['customer_pan_number'] ?? null,
                        ]);
                        $customerDetails->save();

                        // Include the customer_id in the requestData
                        $requestData['customer_id'] = $customerId;
                    }

                   // $agentMobile = $user->mobile ;dd( $agentMobile);

                    $result = $this->bookingService->addBooking($requestData);
                     //SMS ,Booking Confirmation With Amount & ID - Agent

                    return response()->json($result);
                } else {
                    return response()->json(['res' => false, 'msg' => 'Agent not verified'], 200);
                }
            } else {
                return response()->json(['res' => false, 'msg' => 'User type not authorized to add bookings'], 200);
            }
        } else {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }
    }

     /**
     * Payment Transaction
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentTransaction(Request $request)
    {
        $request->validate([
            'transaction_number' => 'required|string|exists:booking_payments,transaction_number',
            'payment_status' => 'required|in:1,2', // 1 for SUCCESS, 2 for FAILURE
        ]);

        try {
            // Use BookingService to update the payment status
            $bookingPayment = $this->bookingService->updatePaymentStatus(
                $request->transaction_number,
                $request->payment_status
            );

            return response()->json([
                'res' => true,
                'msg' => 'Payment status updated successfully',
                'data' => $bookingPayment,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage(),
            ], 500);
        }
    }
     /**
     * Upcoming Booking
     * 
     * This function retrieves the upcoming bookings for the authenticated user. 
     * @return \Illuminate\Http\JsonResponse
     */

    public function upcomingBooking()
    {
     try {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }

        $upcomingBookings = $this->bookingService->getUpcomingBookingsForUser($userId);
        $resultBookings = $upcomingBookings->map(function ($booking) {
            $totalDays = Package::where('id', $booking->package_id)->value('total_days');
            $totalNights = $totalDays - 1;
            $booking->total_days = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";
            $totalPayments = BookingPayment::where('booking_id', $booking->bookings_id)->sum('paid_amount');
            $totalCancellations = BookingCancellation::where('booking_id', $booking->bookings_id)->sum('refund_amount');
            $booking->payable_amount = ($booking->final_price - $totalCancellations) - $totalPayments;
            $booking->provisionalConfirmationPdf = url('storage/app/public/bookingPdf/provisional_confirmation_' . $booking->bookings_id . '.pdf');
            // Add Invoice PDF URL if invoice_number is not null
            if ($booking->invoice_number) {
                $booking->invoice_pdf = url('storage/app/public/bookingPdf/' . $booking->invoice_number . '.pdf');
            }

            return $booking;
        });

        return response()->json(['res' => true, 'msg' => 'Upcoming Bookings retrieved successfully', 'data' => $resultBookings], 200);

     } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
     }
    }

     /**
     * Completed Booking
     * 
     * This function retrieves the upcoming bookings for the authenticated user. 
     * @return \Illuminate\Http\JsonResponse
     */

    public function completedBooking()
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $completedBookings = $this->bookingService->getCompletedBookingsForUser($userId);

            $resultBookings = $completedBookings->map(function ($booking) {
                $totalDays = Package::where('id', $booking->package_id)->value('total_days');
                $totalNights = $totalDays - 1;
                $booking->total_days = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";
                $booking->provisionalConfirmationPdf = url('storage/app/public/bookingPdf/provisional_confirmation_' . $booking->bookings_id . '.pdf');
                // Add Invoice PDF URL if invoice_number is not null
                if ($booking->invoice_number) {
                    $booking->invoice_pdf = url('storage/app/public/bookingPdf/' . $booking->invoice_number . '.pdf');
                }

                return $booking;
            });

            return response()->json(['res' => true, 'msg' => 'Completed Bookings retrieved successfully', 'data' => $resultBookings], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

     /**
     * Fetch Taxes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchTaxes(Request $request)
    {
        $package = Package::select('user_id', 'platform_charges')->find($request->package_id);

        if ($package) {
            // Extract user_id from the package
            $userId = $package->user_id;
            $platformCharges = $package->platform_charges;

            // Fetch only gst_rate and tcs_rate from vendor details by user_id
            $vendorDetails = Vendor::select('gst_rate', 'organization_type')
                ->where('user_id', $userId)
                ->first();

            if ($vendorDetails) {
                if (in_array($vendorDetails->organization_type, ['1', '2'])) {
                    $tdsRate = Config::where('name', 'tds_propieters_partners')->value('value');
                } else {
                    $tdsRate = Config::where('name', 'tds_directors')->value('value');
                }
                $gstRate = $vendorDetails->gst_rate;
            } else {
                // Set gstRate and tcsRate to 0 if vendor details for the user_id are not found
                $gstRate = 0;
                $tdsRate = 0;
            }
        } else {
            // Handle case where package with the provided ID is not found
            $gstRate = 0;
            $tdsRate = 0;
            $platformCharges = 0;
        }
        $gstDetails = (object)['title' => 'GST', 'value' => $gstRate];
        $tdsDetails = (object)['title' => 'TDS', 'value' => $tdsRate];
        $gstTcsRate = Config::where('name', 'gst_tcs')->value('value');
        $gstTcsDetails = (object)['title' => 'GST TCS', 'value' => $gstTcsRate];
        $platformChargesDetails = (object)['title' => 'Platform Charges', 'value' => $platformCharges];

        $data = [
            "gst" => $gstDetails,
            "gst_tcs" => $gstTcsDetails,
            "tds" => $tdsDetails,
            "platform_charges" => $platformChargesDetails,
        ];

        return response()->json(['res' => true, 'msg' => '', 'data' => $data], 200);
    }

    /**
     * Vendor Booking list with Filter
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function vendorBookingList(Request $request)  ///Vendor
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            $filters = $request->all();
            $vendorBookingList = $this->bookingService->getBookingsByVendor($userId, $filters);
            $currentDate = now()->toDateString();

            $hasCancelledOrModified = 0; // Flag to indicate if booking_status 4 or 5 exists

            $filteredBookings = $vendorBookingList->map(function ($booking) use ($currentDate, &$hasCancelledOrModified) {
                if ($booking->booking_status == 1) {
                    $booking->booking_status = 'In process';
                } elseif ($booking->booking_status == 2) {
                    $booking->booking_status = 'Confirmed';
                } elseif ($booking->booking_status == 3) {
                    $booking->booking_status = 'Completed';
                } elseif ($booking->booking_status == 4) {
                    $booking->booking_status = 'Cancelled';
                    $hasCancelledOrModified = 1; // Set flag to true if booking_status is 4
                } elseif ($booking->booking_status == 5) {
                    $booking->booking_status = 'Modified';
                    $hasCancelledOrModified = 1; // Set flag to true if booking_status is 5
                } elseif ($booking->booking_status == 6) {
                    $booking->booking_status = 'On Request';

                }elseif ($booking->booking_status == 7) {
                    $booking->booking_status = 'Disputed';
                }
                return $booking;
            });

            return response()->json([
                'res' => true,
                'msg' => 'Bookings retrieved successfully',
                'data' => $filteredBookings,
                'hasCancelledOrModified' => $hasCancelledOrModified // Include the flag in the response
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
     /**
     * Vendor Booking View
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */

     public function vendorBookingView($id)
     {
         $userId = auth()->id();
         if (!$userId) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
     
         // Get booking details
         $bookingDetails = $this->bookingService->getVendorBookingDetails($id);
     
         if (!$bookingDetails) {
             return response()->json(['error' => 'Booking not found'], 404);
         }
     
         if ($bookingDetails) {
             if ($bookingDetails->booking_status == 1) {
                 $bookingDetails->booking_status = 'In process';
             } elseif ($bookingDetails->booking_status == 2) {
                 $bookingDetails->booking_status = 'Confirmed';
             } elseif ($bookingDetails->booking_status == 3) {
                 $bookingDetails->booking_status = 'Completed';
             } elseif ($bookingDetails->booking_status == 4) {
                 $bookingDetails->booking_status = 'Cancelled';
             } elseif ($bookingDetails->booking_status == 5) {
                 $bookingDetails->booking_status = 'Modified';
             } elseif ($bookingDetails->booking_status == 7) {
                 $bookingDetails->booking_status = 'Disputed';
             }
         }
     
         // Update booking status
         $currentDate = now()->toDateString();
         $booking = $this->updateBookingStatus($bookingDetails, $currentDate);
     
         $finalPrice = Booking::where('id', $id)->value('final_price'); // Retrieve the float value directly
         $finalPriceFloat = (float)$finalPrice; // Convert to float if needed
         $finalPriceInWords = Helper::AmountInWords($finalPriceFloat); // Convert the float value to words
     
         // Get booking related data
         $booking_passengers = BookingPassenger::where('booking_id', $id)->get();
         $booking_rooms = BookingRoom::where('booking_id', $id)->get();
         $packageId = Booking::where('id', $id)->value('package_id');
     
         $booking_train = PackageTrain::where('package_id', $packageId)->get();
         $booking_flight = PackageFlight::where('package_id', $packageId)->get();
         $booking_LocalTransport = PackageLocalTransport::where('package_id', $packageId)->get();
         $bookingDates = BookingDate::where('booking_id', $id)->get();
         $bookingCustomer = BookingCustomerDetails::where('booking_id', $id)->first();
         $package = Package::with(['inclusions', 'exclusions'])->find($packageId);
         $packageName = $package ? $package->name : '';
         $vendorDetails = Vendor::where('user_id', $package->user_id)->first();
     
         $packageAddons = [];
         $totalAddonPrice = 0.00;
         if (isset($requestData['add_on_id'])) {
             $packageAddons = PackageAddon::whereIn('id', explode(',', $requestData['add_on_id']))->get();
             $totalAddonPrice = $packageAddons->sum('price');
         }
     
         $logoUrl = 'https://abwebx.com/updates/wishmytour/backend/public/images/logo.jpg';
     
         $refundAmountSum = BookingCancellation::where('booking_id', $id)
             ->where('refund_amount', '>', 0)
             ->sum('refund_amount');
         $bookingCancellation = BookingCancellation::where('booking_id', $id)->get();
     
         $itineraries = Itinerary::where('package_id', $packageId)->get();
         $totalDays = $package->total_days;
         $totalNights = $totalDays - 1;
         $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";
     
         $inclusions = $package->inclusions->pluck('name');
         $exclusions = $package->exclusions->pluck('name');
         $paymentPolicies = [$package->payment_policy];
         $cancelPolicies = explode(',', $package->cancellation_policy);
         $formattedPolicies = [];
         foreach ($cancelPolicies as $index => $policy) {
             $policyDetails = explode('-', $policy);
             if ($index === 0) {
                 $formattedPolicies[] = "{$policyDetails[0]} or more days before departure: {$policyDetails[2]}%";
             } else {
                 $formattedPolicies[] = "Between {$policyDetails[0]} to {$policyDetails[1]} days before departure: {$policyDetails[2]}%";
             }
         }
         $cancellationPolicies = $formattedPolicies;
         $terms = Config::where('name', 'terms_and_conditions')->first();
         $defaultTerms = $terms->value;
         $packageTerms = $package->terms_and_conditions;
     
         $stayPlan = $package->getItineraries($package->id);
     
         $pdfContent = $this->bookingService->generateBookingPDFVendor(
             $booking,
             $booking_rooms,
             $booking_passengers,
             $bookingDates,
             $bookingCustomer,
             $packageName,
             $vendorDetails,
             $packageAddons,
             $totalAddonPrice,
             $finalPriceInWords,
             $logoUrl,
             $itineraries,
             $inclusions,
             $exclusions,
             $paymentPolicies,
             $cancellationPolicies,
             $packageTerms,
             $defaultTerms,
             $totalDaysAndNights,
             $stayPlan
         );
     
         // Generate a unique filename with timestamp
         $pdfFileName = 'vendor_invoice_' . time() . '.pdf';
     
         // Construct the file path relative to the public disk's bookingPdf directory
         $pdfFilePath = 'bookingPdf/' . $pdfFileName;
     
         // Manually create the directory if it doesn't exist
         File::makeDirectory(storage_path('app/public/' . dirname($pdfFilePath)), 0777, true, true);
     
         // Store the PDF content in the specified storage path
         Storage::put($pdfFilePath, $pdfContent);
     
         // Move the PDF file to the desired directory
         File::move(
             storage_path('app/' . $pdfFilePath), // Source path
             storage_path('app/public/' . $pdfFilePath) // Destination path
         );
     
         // Get the full URL for accessing the PDF file through the web server
         $pdfUrl = url('storage/app/public/' . $pdfFilePath); // Use url() instead of asset()
     
         // Get the report gallery images
         $reportGallery = BookingReportGallery::where('booking_id', $id)->get();
     
         // Return response
         return response()->json([
             'res' => true,
             'msg' => 'Booking retrieved successfully',
             'data' => [
                 'booking' => $bookingDetails,
                 'booking_passengers' => $booking_passengers,
                 'booking_rooms' => $booking_rooms,
                 'booking_train' => $booking_train,
                 'booking_flight' => $booking_flight,
                 'booking_LocalTransport' => $booking_LocalTransport,
                 'packageAddon' => $packageAddons,
                 'pdfFilePath' => $pdfUrl,
                 'refundAmountSum' => $refundAmountSum,
                 'bookingCancellation' => $bookingCancellation,
                 'reportGallery' => $reportGallery, // Add the report gallery to the response
             ],
         ], 200);
     }
    // Helper method to update booking status
    private function updateBookingStatus($bookingDetails, $currentDate)
    {
        // Check if $bookingDetails is not null
        if ($bookingDetails) {
            // Update booking status directly
            if ($bookingDetails->booking_status == 1) {
                $bookingDetails->booking_status = 'In process';
            } elseif ($bookingDetails->booking_status == 2) {
                $bookingDetails->booking_status = 'Confirmed';
                // if ($bookingDetails->first_booking_date <= $currentDate && $bookingDetails->last_booking_date >= $currentDate) {
                //     $bookingDetails->booking_status = 'Ongoing';
                // } elseif ($bookingDetails->first_booking_date > $currentDate) {
                //     $bookingDetails->booking_status = 'Upcoming';
                // } elseif ($bookingDetails->updated_at > $bookingDetails->created_at) {
                //     $bookingDetails->booking_status = 'Modified';
                // } else {
                //     $bookingDetails->booking_status = 'Completed';
                // }
            } elseif ($bookingDetails->booking_status == 3) {
                $bookingDetails->booking_status = 'Completed';
            } elseif ($bookingDetails->booking_status == 4) {
                $bookingDetails->booking_status = 'Cancelled';
            }
        }

        return $bookingDetails; // Return updated booking details
    }

    // Helper method to get package addons
      /**
     * Agent Booking list with Filter
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function agentBookingList(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user->id) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            $agentCode = AgentDetails::where('user_id', $user->id)->value('agent_code');
    
            $filters = $request->all();
            $agentBookingList = $this->bookingService->agentBookingList($agentCode, $filters);
    
            $currentDate = now()->toDateString();
            $filteredBookings = $agentBookingList->map(function ($booking) use ($currentDate) {
    
                if ($booking->booking_status == 1) {
                    $booking->booking_status = 'In process';
                } elseif ($booking->booking_status == 2) {
    
                    if ($booking->first_booking_date <= $currentDate && $booking->last_booking_date >= $currentDate) {
                        $booking->booking_status = 'Ongoing';
                    } elseif ($booking->first_booking_date > $currentDate) {
                        $booking->booking_status = 'Upcoming';
                    } elseif ($booking->updated_at > $booking->created_at) {
                        $booking->booking_status = 'Modified';
                    } else {
                        $booking->booking_status = 'Completed';
                    }
                } elseif ($booking->booking_status == 3) {
                    $booking->booking_status = 'Completed';
                } elseif ($booking->booking_status == 4) {
                    $booking->booking_status = 'Cancelled';
                }
    
                $totalPayments = $booking->payments()->sum('paid_amount');
                if ($totalPayments == $booking->final_price) {
                    $booking->payment_status = 'Paid';
                    $booking->can_claim = true;
                } else {
                    $booking->payment_status = 'Partially Paid';
                    $booking->can_claim = false;
                }
                $booking->total_paid = $totalPayments;
    
                return $booking;
            });
    
            return response()->json(['res' => true, 'msg' => 'Bookings retrieved successfully', 'data' => $filteredBookings], 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function bookingDetails($id)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $bookingDetails = $this->bookingService->getCustomerBookingDetails($id);

        if (!$bookingDetails) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        if($userId != $bookingDetails->first()['user_id']){
            return response()->json(['error' => 'Booking not found'], 404);
        }
        // Get the sum of refund_amount from booking_cancellations
        $refundAmountSum = BookingCancellation::where('booking_id', $id)
            ->where('refund_amount', '>', 0)
            ->sum('refund_amount');

        $currentDate = now()->toDateString();
        $filteredBookings = $bookingDetails->map(function ($booking) use ($currentDate) {


            if ($booking->booking_status == 1) {
                $booking->booking_status = 'In process';
            } elseif ($booking->booking_status == 2) {
                $booking->booking_status = 'Confirmed';
                // if ($booking->first_booking_date <= $currentDate && $booking->last_booking_date >= $currentDate) {
                //     $booking->booking_status = 'Ongoing';
                // } elseif ($booking->first_booking_date > $currentDate) {
                //     $booking->booking_status = 'Upcoming';
                // }elseif ($booking->updated_at > $booking->created_at) {
                //     $booking->booking_status = 'Modified';
                // }else {
                //     $booking->booking_status = 'Completed';
                //  }
            } elseif ($booking->booking_status == 3) {
                $booking->booking_status = 'Completed';
            } elseif ($booking->booking_status == 4) {
                $booking->booking_status = 'Cancelled';
            }

            // Check for Modified status
            $booking->provisionalConfirmationPdf = url('storage/app/public/bookingPdf/provisional_confirmation_' . $booking->bookings_id . '.pdf');
            $booking->invoicepdfUrl = $booking->invoice_number != null ? url('storage/app/public/invoiceAgent/' . $booking->invoice_number.'.pdf') : '';
            return $booking;
        });


        $booking_passengers = BookingPassenger::where('booking_id', $id)->get();
        $booking_rooms = BookingRoom::where('booking_id', $id)->get();
        //$itineraries_id = Itinerary::where('package_id', $id)->get();
        $packageId = Booking::where('id', $id)->pluck('package_id');
        $booking_train = PackageTrain::where('package_id', $packageId)->get();
        $booking_flight = PackageFlight::where('package_id', $packageId)->get();
        $booking_LocalTransport = PackageLocalTransport::where('package_id', $packageId)->get();

        $bookingAddonIds = Booking::where('id', $id)->pluck('add_on_id')->first();

        $addonIdsArray = explode(',', $bookingAddonIds);

        $packageAddons = PackageAddon::whereIn('id', $addonIdsArray)->get();
        $bookingCancellation = BookingCancellation::where('booking_id', $id)->get();
        $addonDetails = $packageAddons->map(function ($addon) {
            return [
                'title' => $addon->title,
                'description' => $addon->description,
                'price' => $addon->price,
            ];
        });
        $reportGalleryImages = BookingReportGallery::where('booking_id', $id)->get();
        $package = $this->bookingService->getPackageDetails($packageId);

        $galleryImages = [];
        foreach ($package->gallery_images as $image) {
            $galleryImages[] = [
                'id' => $image['id'],
                'path' => $image['path'],
            ];
        }

        $itinerary = $package->itinerary ?? [];
        $inclusionList = $package->inclusions ?? [];
        $exclusionList = $package->exclusions ?? [];
        $totalDays = $package->total_days;
        $totalNights = $totalDays - 1;
        $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";
        $totalTrainNights = $package->trains()->where('number_of_nights', 1)->count();


        $inclusionIds = explode(',', $package->inclusions_in_package);


        $inclusions = DB::table('package_inclusions')
            ->whereIn('id', $inclusionIds)
            ->select(['name'])
            ->get();


        $themesIds = explode(',', $package->themes_id);


        $themes = DB::table('themes')
            ->whereIn('id', $themesIds)
            ->select(['id', 'name', 'image', 'icon'])
            ->get();

        $stayPlan = $package->stay_plan;

        // Apply platform charges
        $packagePrice = $package->starting_price;
        $platformCharges = $package->starting_price * ($package->platform_charges / 100);
        $packagePrice += $platformCharges;

        // Fetch existing booking payments for the booking
        $existingPayments = BookingPayment::where('booking_id', $id)->get();
        $existingPaymentsCount = $existingPayments->count();
        $paymentPolicy = $package->payment_policy;

        // Remove payment policies according to the number of existing payments
        for ($i = 0; $i < $existingPaymentsCount; $i++) {
            array_shift($paymentPolicy);
        }
        $package->payment_policy = $paymentPolicy;

        $transformedPackage = [
            'package_id' => $package->id,
            'vendor_name' => $package->vendor_name,
            'package_name' => $package->name,
            'total_days' => $totalDaysAndNights,
            'total_days_count' => (int)$totalDays,
            'total_nights_count' => (int)$totalNights,
            'total_train_nights_count' => (int)$package->trains()->where('number_of_nights', 1)->count(),
            'starting_price' => $package->starting_price,
            'child_price' => $package->child_price,
            'infant_price' => $package->infant_price,
            'single_occupancy_price' => $package->single_occupancy_price,
            'triple_occupancy_price' => $package->triple_occupancy_price,
            'platform_charges' => $package->platform_charges,
            'website_price' => $packagePrice,
            'origin' => $package->origin_city_id,
            'destination_state_id' => $package->destination_state_id,
            'state_name' => $package->state_name,
            'destination_city_id' => $package->destination_city_id,
            'cities_name' => $package->cities_name,
            'keywords' => $package->keywords,
            'transportation_name' => isset($package->transportation->name) ? ($package->transportation->name) : null,
            'hotel_star' => isset($package->hotel_star_id) ? ($package->hotel_star_id) : null,
            'tour_type' => $package->tour_type,
            'trip' => $package->tour_type == 1 ? 'Domestic' : 'International',
            'trip_type' => $package->trip_type,
            'type_of_tour_packages' => $package->trip_type == 1 ? 'Standard' : 'Weekend',
            'themes' => $themes,
            'featured_image_path' => $package->first_gallery_image,
            'inclusions_in_package' => $inclusions,
            'inclusions_list' => $inclusionList,
            'exclusions_list' => $exclusionList,
            'cancellation_policy' => $package->cancellation_policy,
            'payment_policy' => $package->payment_policy,
            'stay_plan' => $stayPlan,
            'bulk_discounts' => $package->bulkDiscounts,
            'gallery_images' => $galleryImages,
            'tour_circuit' => $package->tour_circuit,
            'region_id' => $package->region_id,
            'status' => $package->status,
            'is_transport' => $package->is_transport,
            'is_flight' => $package->is_flight,
            'is_train' => $package->is_train,
            'is_hotel' => $package->is_hotel,
            'is_meal' => $package->is_meal,
            'is_sightseeing' => $package->is_sightseeing,
            'itinerary' => $this->transformItinerary($package->itinerary),
        ];

        $uniqueId = strtoupper(uniqid());
        $transactionNumber = 'TXN' . preg_replace('/[^A-Z0-9_-]/', '', $uniqueId);

        // Ensure the length is less than 35 characters
        if (strlen($transactionNumber) >= 35) {
            $transactionNumber = substr($transactionNumber, 0, 34);
        }

       

        return response()->json(['res' => true, 'msg' => 'Booking retrieved successfully', 'data' => $filteredBookings, 'booking_passengers' => $booking_passengers, 'booking_rooms' => $booking_rooms, 'booking_train' => $booking_train, 'booking_flight' => $booking_flight, 'booking_LocalTransport' => $booking_LocalTransport, 'packageAddon' => $addonDetails, 'package' => $transformedPackage, 'refund_amount_sum' => $refundAmountSum, 'bookingCancellation' => $bookingCancellation, 'reportGalleryImages' => $reportGalleryImages,'transactionNumber' => $transactionNumber], 200);
    }

    //Customer Booking details

    private function transformItinerary($itineraries)
    {

        return $itineraries->map(function ($itinerary) {
            $flights = $itinerary->flight ?? [];
            $trains = $itinerary->train ?? [];
            $local_transport = $itinerary->local_transport ?? [];
            $sightseeing = $itinerary->sightseeing ?? [];
            $hotels = $itinerary->hotel ?? [];
            $transformedHotels = [];
            foreach ($hotels as $hotel) {
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


            $transformedFlights = [];
            foreach ($flights as $flight) {
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
            // Transform trains
            $transformedTrains = [];
            foreach ($trains as $train) {
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
            return [
                'itinerary_id' => $itinerary->id,
                'day' => $itinerary->day,
                'place_name' => $itinerary->place_name,
                'itinerary_title' => $itinerary->itinerary_title,
                'itinerary_description' => $itinerary->itinerary_description,
                'meal' => $itinerary->meal,
                'flights' => $transformedFlights,
                //'trains' => $trains,
                'trains' => $transformedTrains,
                'local_transport' => $local_transport,
                'sightseeing' => $sightseeing,
                'hotels' => $transformedHotels,
            ];
        });
    }

    public function coupons(Request $request)
    {
        try {
            // Get the current authenticated user's ID
            $userId = $request->user()->id; 
   
            // Get today's date
            $today = now(); 
            
            // Fetch coupons that match all conditions
            $coupons = Coupons::where(function($query) use ($userId) {
                $query->whereRaw("FIND_IN_SET(?, user_id) > 0", [$userId]);
            })
            ->where('status', 1)
            ->where('end_date', '>=', $today)
            ->where('show_status', 1)
            ->get();
    
            return response()->json([
                'res' => true, 
                'msg' => 'All coupons retrieved successfully', 
                'data' => $coupons
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkCoupon(Request $request)
    {
     try {
        // Get the coupon code from the request
        $code = $request->input('code');
        // Get the current authenticated user's ID
        $userId = $request->user()->id;
        // Get today's date
        $today = now();

        // Query to find the coupon with the given code
        $coupon = Coupons::whereRaw("BINARY code = ?", [$code])
                         ->where('show_status', 0) // Assuming you want to check for active coupons
                         ->where(function($query) use ($userId) {
                             $query->whereRaw("FIND_IN_SET(?, user_id) > 0", [$userId]);
                         })
                         ->where('status', 1)
                         ->where('end_date', '>=', $today)
                         ->first();

         if ($coupon) {
            return response()->json([
                'res' => true,
                'msg' => 'Coupon exists',
                'exists' => true,
                'coupon' => $coupon
            ], 200);
         } else {
            return response()->json([
                'res' => true,
                'msg' => 'Coupon does not exist',
                'exists' => false
            ], 200);
         }
     } catch (\Exception $e) {
         return response()->json(['error' => $e->getMessage()], 500);
     }
    }

    public function addMessage(Request $request)
    {
        try {
            $data = $request->validate([
                'vendor_id' => 'required|integer',
                'package_id' => 'required|integer',
                'package_name' => 'nullable|string',
                'run_date' => 'nullable|date',
                'message' => 'nullable|string',
            ]);
            $message = $this->bookingService->addMessage($data);

            return response()->json(['res' => true, 'msg' => 'Message added successfully', 'message' => $message], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getVendorCities()
    {
        try {
            $cities = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
                ->leftJoin('cities', 'packages.origin_city_id', '=', 'cities.id')
                ->select('cities.city', 'cities.id')
                ->distinct()
                ->get();

            return response()->json(['res' => true, 'msg' => 'Cities retrieved successfully', 'data' => $cities], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancelBooking(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'cancellation_reason' => 'required|string',
            'cancellation_type' => 'required|in:partial,full',
            'cancellation_percent' => 'nullable|numeric',
        ]);

        if (auth()->check()) {
            $user = auth()->user();
            Log::info('User methods: ', get_class_methods($user));

            $result = $this->bookingService->cancelBooking($request->all(), $user);

            if ($result['res']) {
                return response()->json(['res' => true, 'msg' => 'Booking cancelled successfully', 'data' => $result['data']]);
            } else {
                return response()->json(['res' => false, 'msg' => $result['msg']], $result['status']);
            }
        } else {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }
    }

    public function confirmBooking(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        if (auth()->check()) {
            $user = auth()->user();
            $result = $this->bookingService->confirmBooking($request->all(), $user);

            if ($result['res']) {
                return response()->json(['res' => true, 'msg' => 'Booking confirmed successfully', 'data' => []]);
            } else {
                return response()->json(['res' => false, 'msg' => $result['msg']], $result['status']);
            }
        } else {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }
    }

    public function cancelApprove(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        if (auth()->check()) {
            $user = auth()->user();

            $result = $this->bookingService->cancelApprove($request->all(), $user);

            if ($result['res']) {
                return response()->json(['res' => true, 'msg' => 'Booking cancelled successfully', 'data' => $result['data']]);
            } else {
                return response()->json(['res' => false, 'msg' => $result['msg']], $result['status']);
            }
        } else {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }
    }

    public function cancelBookingList(Request $request)
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $canceledBookings = $this->bookingService->getCanceledBookingsForUser($userId);

            $resultBookings = $canceledBookings->map(function ($booking) {
                $totalDays = Package::where('id', $booking->package_id)->value('total_days');
                $totalNights = $totalDays - 1;
                $booking->total_days = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";
                //  $booking->pdfUrl = $booking->transaction_number != null ? url('storage/app/public/invoiceAgent/' . $booking->invoice_number.'.pdf') : '';
                $booking->creditUrl =  url('storage/app/public/creditNote/' . $booking->transaction_number.'.pdf') ;
                return $booking;
            });

            return response()->json(['res' => true, 'msg' => 'Canceled Bookings retrieved successfully', 'data' => $resultBookings], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateCompletedBookings()
    {
        try {
            $updatedBookings = $this->bookingService->updateCompletedBookingsStatus();

            return response()->json(['res' => true, 'msg' => 'Bookings status updated successfully', 'data' => $updatedBookings], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addFeedback(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();
            Log::info('User methods: ', get_class_methods($user));

            if ($user->user_type === User::ROLE_CUSTOMER) {
                $result = $this->bookingService->addFeedback($request->all(), $user->id);
                return response()->json($result);
            } elseif ($user->user_type === User::ROLE_AGENT) {
                $agentDetails = DB::table('agent_details')->where('user_id', $user->id)->first();
                Log::info('Agent Details: ', ['agentDetails' => $agentDetails]);

                if ($agentDetails && $agentDetails->is_verified == 1) {
                    $result = $this->bookingService->addFeedback($request->all(), $user->id);
                    return response()->json($result);
                } else {
                    return response()->json(['res' => false, 'msg' => 'Agent not verified'], 200);
                }
            } else {
                return response()->json(['res' => false, 'msg' => 'User type not authorized to add report'], 200);
            }
        } else {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }
    }

    public function addReport(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();
            Log::info('User methods: ', get_class_methods($user));

            if ($user->user_type === User::ROLE_CUSTOMER) {
                $result = $this->bookingService->addReport($request, $user->id);
                return response()->json($result);
            } elseif ($user->user_type === User::ROLE_AGENT) {
                $agentDetails = DB::table('agent_details')->where('user_id', $user->id)->first();
                Log::info('Agent Details: ', ['agentDetails' => $agentDetails]);

                if ($agentDetails && $agentDetails->is_verified == 1) {
                    $result = $this->bookingService->addReport($request, $user->id);
                    return response()->json($result);
                } else {
                    return response()->json(['res' => false, 'msg' => 'Agent not verified'], 200);
                }
            } else {
                return response()->json(['res' => false, 'msg' => 'User type not authorized to add report'], 200);
            }
        } else {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }
    }

    public function sendBookingOnRequest(Request $request)
    {   ///Customer

        $rules = [
            'package_id' => 'required|integer|exists:packages,id',
            'rooms' => 'required',
            'rooms.*.room_no' => 'required|string',
            'rooms.*.adults' => 'required|integer|min:1',
            'rooms.*.children' => 'nullable|integer|min:0',
            'status' => 'required|integer',
            'add_on_id' => 'nullable|integer|exists:package_addons,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'base_amount' => 'nullable|numeric',
            'price' => 'required|numeric',
            'addon_total_price' => 'nullable|numeric',
            'gst_percent' => 'nullable|numeric',
            'gst_price' => 'nullable|numeric',
            'tcs' => 'nullable|numeric',
            'final_price' => 'nullable|numeric',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $validatedData['token'] = Str::random(8);
        $siteUrl = env('SITE_URL');
        if (Auth::check()) {
            $user = Auth::user();
            Log::info('User methods: ', get_class_methods($user));

            if ($user->user_type === User::ROLE_CUSTOMER) {
                $result = $this->bookingService->sendBookingOnRequest($validatedData);

                $vendorData = DB::table('packages')
                    ->join('users', 'packages.user_id', '=', 'users.id')
                    ->join('vendors', 'vendors.user_id', '=', 'users.id') // Assuming vendors.user_id relates to users.id
                    ->where('packages.id', $validatedData['package_id'])
                    ->select('users.email', 'vendors.name as vendorName', 'packages.name as tourName')
                    ->first();

                if ($vendorData) {
                    $data = [
                        'booking_id' => $result['data']['booking']->id,
                        'customer_id' => auth()->id(),
                        'package_id' => $validatedData['package_id'],
                        'token' => $validatedData['token'],
                        'start_date' => $validatedData['start_date'],
                        'end_date' => $validatedData['end_date'],
                        'vendorName' => $vendorData->vendorName,
                        'tourName' => $vendorData->tourName,
                        'requestedDate' => now()->toDateString(),
                        //'approvalLink' => 'https://wishmytour.in/dev/vendor-quotation-detail/' . $result['data']['booking']->id,
                        'approvalLink' => $siteUrl . 'vendor-quotation-detail/' . $result['data']['booking']->id,
                        'siteUrl' => config('app.url'),
                        'siteName' => config('app.name'),
                        'appUrl' => config('app.url')
                    ];

                    $mailSent = Helper::sendMail($vendorData->email, $data, 'mail.customerSentOnReq', 'On Request Booking');//$vendorData->email;
                }

                return response()->json($result);
            } elseif ($user->user_type === User::ROLE_AGENT) {
                $agentDetails = DB::table('agent_details')->where('user_id', $user->id)->first();

                Log::info('Agent Details: ', ['agentDetails' => $agentDetails]);

                if ($agentDetails && $agentDetails->is_verified == 1) {
                    $result = $this->bookingService->sendBookingOnRequest($validatedData);

                    $vendorData = DB::table('packages')
                        ->join('users', 'packages.user_id', '=', 'users.id')
                        ->join('vendors', 'vendors.user_id', '=', 'users.id') // Assuming vendors.user_id relates to users.id
                        ->where('packages.id', $validatedData['package_id'])
                        ->select('users.email', 'vendors.name as vendorName', 'packages.name as tourName')
                        ->first();

                    if ($vendorData) {
                        $data = [
                            'booking_id' => $result['data']['booking']->id,
                            'customer_id' => auth()->id(),
                            'package_id' => $validatedData['package_id'],
                            'token' => $validatedData['token'],
                            'start_date' => $validatedData['start_date'],
                            'end_date' => $validatedData['end_date'],
                            'vendorName' => $vendorData->vendorName,
                            'tourName' => $vendorData->tourName,
                            'requestedDate' => now()->toDateString(),
                            //'approvalLink' => 'https://wishmytour.in/dev/vendor-quotation-detail/' . $result['data']['booking']->id,
                            'approvalLink' => $siteUrl . 'vendor-quotation-detail/' . $result['data']['booking']->id,
                            'siteUrl' => config('app.url'),
                            'siteName' => config('app.name'),
                            'appUrl' => config('app.url')
                        ];

                        $mailSent = Helper::sendMail($vendorData->email, $data, 'mail.customerSentOnReq', 'On Request Booking');//$vendorData->email;
                    }


                    return response()->json($result);
                } else {
                    return response()->json(['res' => false, 'msg' => 'Agent not verified'], 200);
                }
            } else {
                return response()->json(['res' => false, 'msg' => 'User type not authorized to add bookings'], 200);
            }
        } else {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }
    }


    ////**************************ON REQUEST BOOKING************************////

    public function bookingOnRequestList()
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['msg' => 'Unauthorized'], 401);
            }

            $upcomingBookings = $this->bookingService->bookingOnRequestList($userId);

            $resultBookings = $upcomingBookings->map(function ($booking) {
                // Decode room details
                $roomDetails = json_decode($booking->room_details, true);
                $totalAdults = 0;
                $totalChildren = 0;

                if (is_array($roomDetails)) {
                    foreach ($roomDetails as $room) {
                        $totalAdults += isset($room['adults']) ? (int)$room['adults'] : 0;
                        $totalChildren += isset($room['children']) ? (int)$room['children'] : 0;
                    }
                }

                // Calculate pax
                $booking->pax = $totalAdults + $totalChildren;

                // Calculate total days and nights
                $totalDays = Package::where('id', $booking->package_id)->value('total_days');
                $totalNights = $totalDays - 1;
                $booking->total_days = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";

                return $booking;
            });

            return response()->json(['res' => true, 'msg' => 'Upcoming Onrequest Bookings retrieved successfully', 'data' => $resultBookings], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function vendorBookingOnReqList(Request $request)
    {
        try {
            $userId = Auth::id();
            $vendorBookingList = $this->bookingService->getOnrequestBooking($userId);

            // Calculate pax and duration for each booking
            foreach ($vendorBookingList as &$booking) {
                // Calculate duration
                $startDate = new DateTime($booking->start_date);
                $endDate = new DateTime($booking->end_date);

                // Calculate difference in days
                $diff = $startDate->diff($endDate);
                $days = $diff->days;

                // Calculate nights (assuming 1 night = 1 day for this calculation)
                $nights = floor($days / 1); // 1 day = 1 night

                // Format duration
                $duration = '';
                if ($nights > 0) {
                    $duration .= $nights . ' N';
                    if ($days > 0) {
                        $duration .= ' & ';
                    }
                }
                if ($days > 0) {
                    $duration .= ($days + 1) . ' D'; // Adding 1 to days because end_date is inclusive
                }

                // Calculate pax
                $roomDetails = json_decode($booking->room_details, true);
                $totalAdults = 0;
                $totalChildren = 0;

                if (is_array($roomDetails)) {
                    foreach ($roomDetails as $room) {
                        $totalAdults += isset($room['adults']) ? (int)$room['adults'] : 0;
                        $totalChildren += isset($room['children']) ? (int)$room['children'] : 0;
                    }
                }

                $booking->pax = $totalAdults + $totalChildren;
                $booking->duration = $duration;
            }

            return response()->json([
                'res' => true,
                'msg' => 'Bookings retrieved successfully',
                'data' => $vendorBookingList,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function bookingOnReqDetails($id) ///Vendor
    {
        try {
            // Find the booking record
            $booking = BookingOnRequest::findOrFail($id);

            // Decode the room_details JSON string
            $booking->room_details = json_decode($booking->room_details, true);

            // Get the customer details including email and mobile from users table
            $customer = DB::table('customer_details')
                ->join('states', 'customer_details.state', '=', 'states.id')
                ->join('users', 'customer_details.user_id', '=', 'users.id')
                ->where('customer_details.user_id', $booking->customer_id)
                ->select(
                    'customer_details.*',
                    'states.name as state_name',
                    'users.email',
                    'users.mobile'
                )
                ->first();

            $packageId = BookingOnRequest::where('id', $id)->pluck('package_id');
            $booking_train = PackageTrain::where('package_id', $packageId)->get();
            $booking_flight = PackageFlight::where('package_id', $packageId)->get();
            $booking_LocalTransport = PackageLocalTransport::where('package_id', $packageId)->get();

            $bookingAddonIds = Booking::where('id', $id)->pluck('add_on_id')->first();

            $addonIdsArray = explode(',', $bookingAddonIds);

            $packageAddons = PackageAddon::whereIn('id', $addonIdsArray)->get();
            $bookingCancellation = BookingCancellation::where('booking_id', $id)->get();
            $addonDetails = $packageAddons->map(function ($addon) {
                return [
                    'title' => $addon->title,
                    'description' => $addon->description,
                    'price' => $addon->price,
                ];
            });
            $reportGalleryImages = BookingReportGallery::where('booking_id', $id)->get();
            $package = $this->bookingService->getPackageDetails($packageId);

            $galleryImages = [];
            foreach ($package->gallery_images as $image) {
                $galleryImages[] = [
                    'id' => $image['id'],
                    'path' => $image['path'],
                ];
            }

            $itinerary = $package->itinerary ?? [];
            $inclusionList = $package->inclusions ?? [];
            $exclusionList = $package->exclusions ?? [];
            $totalDays = $package->total_days;
            $totalNights = $totalDays - 1;
            $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";


            $inclusionIds = explode(',', $package->inclusions_in_package);


            $inclusions = DB::table('package_inclusions')
                ->whereIn('id', $inclusionIds)
                ->select(['name'])
                ->get();


            $themesIds = explode(',', $package->themes_id);


            $themes = DB::table('themes')
                ->whereIn('id', $themesIds)
                ->select(['id', 'name', 'image', 'icon'])
                ->get();

            $stayPlan = $package->stay_plan;

            // Apply platform charges
            $packagePrice = $package->starting_price;
            $platformCharges = $package->starting_price * ($package->platform_charges / 100);
            $packagePrice += $platformCharges;


            return response()->json([
                'res' => true,
                'msg' => 'Booking details retrieved successfully',
                'data' => [
                    'booking' => $booking,
                    'customer' => $customer,
                    'package_id' => $package->id,
                    'vendor_name' => $package->vendor_name,
                    'package_name' => $package->name,
                    'total_days' => $totalDaysAndNights,
                    'total_days_count' => (int)$totalDays,
                    'total_nights_count' => (int)$totalNights,
                    'starting_price' => $package->starting_price,
                    'child_price' => $package->child_price,
                    'infant_price' => $package->infant_price,
                    'single_occupancy_price' => $package->single_occupancy_price,
                    'triple_occupancy_price' => $package->triple_occupancy_price,
                    'platform_charges' => $package->platform_charges,
                    'website_price' => $packagePrice,
                    'origin' => $package->origin_city_id,
                    'destination_state_id' => $package->destination_state_id,
                    'state_name' => $package->state_name,
                    'destination_city_id' => $package->destination_city_id,
                    'cities_name' => $package->cities_name,
                    'keywords' => $package->keywords,
                    'transportation_name' => isset($package->transportation->name) ? ($package->transportation->name) : null,
                    'hotel_star' => isset($package->hotel_star_id) ? ($package->hotel_star_id) : null,
                    'tour_type' => $package->tour_type,
                    'trip' => $package->tour_type == 1 ? 'Domestic' : 'International',
                    'trip_type' => $package->trip_type,
                    'type_of_tour_packages' => $package->trip_type == 1 ? 'Standard' : 'Weekend',
                    'themes' => $themes,
                    'featured_image_path' => $package->first_gallery_image,
                    'inclusions_in_package' => $inclusions,
                    'inclusions_list' => $inclusionList,
                    'exclusions_list' => $exclusionList,
                    'cancellation_policy' => $package->cancellation_policy,
                    'stay_plan' => $stayPlan,
                    'bulk_discounts' => $package->bulkDiscounts,
                    'gallery_images' => $galleryImages,
                    'tour_circuit' => $package->tour_circuit,
                    'region_id' => $package->region_id,
                    'status' => $package->status,
                    'is_transport' => $package->is_transport,
                    'is_flight' => $package->is_flight,
                    'is_train' => $package->is_train,
                    'is_hotel' => $package->is_hotel,
                    'is_meal' => $package->is_meal,
                    'is_sightseeing' => $package->is_sightseeing,
                    'itinerary' => $this->transformItinerary($package->itinerary),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
    }

    public function bookingOnRequestDetails($id)  //Customer
    {
        try {
            $userId = Auth::id();
            // Find the booking record
            $booking = BookingOnRequest::findOrFail($id);

            // Decode the room_details JSON string
            $booking->room_details = json_decode($booking->room_details, true);

            // Get the customer details including email and mobile from users table
            $customer = DB::table('customer_details')
                ->join('states', 'customer_details.state', '=', 'states.id')
                ->join('users', 'customer_details.user_id', '=', 'users.id')
                ->where('customer_details.user_id', $userId)
                ->select(
                    'customer_details.*',
                    'states.name as state_name',
                    'users.email',
                    'users.mobile'
                )
                ->first();

            $packageId = BookingOnRequest::where('id', $id)->pluck('package_id');
            $booking_train = PackageTrain::where('package_id', $packageId)->get();
            $booking_flight = PackageFlight::where('package_id', $packageId)->get();
            $booking_LocalTransport = PackageLocalTransport::where('package_id', $packageId)->get();

            $bookingAddonIds = Booking::where('id', $id)->pluck('add_on_id')->first();

            $addonIdsArray = explode(',', $bookingAddonIds);

            $packageAddons = PackageAddon::whereIn('id', $addonIdsArray)->get();
            $bookingCancellation = BookingCancellation::where('booking_id', $id)->get();
            $addonDetails = $packageAddons->map(function ($addon) {
                return [
                    'title' => $addon->title,
                    'description' => $addon->description,
                    'price' => $addon->price,
                ];
            });
            $reportGalleryImages = BookingReportGallery::where('booking_id', $id)->get();
            $package = $this->bookingService->getPackageDetails($packageId);

            $galleryImages = [];
            foreach ($package->gallery_images as $image) {
                $galleryImages[] = [
                    'id' => $image['id'],
                    'path' => $image['path'],
                ];
            }

            $itinerary = $package->itinerary ?? [];
            $inclusionList = $package->inclusions ?? [];
            $exclusionList = $package->exclusions ?? [];
            $totalDays = $package->total_days;
            $totalNights = $totalDays - 1;
            $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";


            $inclusionIds = explode(',', $package->inclusions_in_package);


            $inclusions = DB::table('package_inclusions')
                ->whereIn('id', $inclusionIds)
                ->select(['name'])
                ->get();


            $themesIds = explode(',', $package->themes_id);


            $themes = DB::table('themes')
                ->whereIn('id', $themesIds)
                ->select(['id', 'name', 'image', 'icon'])
                ->get();

            $stayPlan = $package->stay_plan;

            // Apply platform charges
            $packagePrice = $package->starting_price;
            $platformCharges = $package->starting_price * ($package->platform_charges / 100);
            $packagePrice += $platformCharges;


            return response()->json([
                'res' => true,
                'msg' => 'Booking details retrieved successfully',
                'data' => [
                    'booking' => $booking,
                    'customer' => $customer,
                    'package_id' => $package->id,
                    'vendor_name' => $package->vendor_name,
                    'package_name' => $package->name,
                    'total_days' => $totalDaysAndNights,
                    'total_days_count' => (int)$totalDays,
                    'total_nights_count' => (int)$totalNights,
                    'starting_price' => $package->starting_price,
                    'child_price' => $package->child_price,
                    'infant_price' => $package->infant_price,
                    'single_occupancy_price' => $package->single_occupancy_price,
                    'triple_occupancy_price' => $package->triple_occupancy_price,
                    'platform_charges' => $package->platform_charges,
                    'website_price' => $packagePrice,
                    'origin' => $package->origin_city_id,
                    'destination_state_id' => $package->destination_state_id,
                    'state_name' => $package->state_name,
                    'destination_city_id' => $package->destination_city_id,
                    'cities_name' => $package->cities_name,
                    'keywords' => $package->keywords,
                    'transportation_name' => isset($package->transportation->name) ? ($package->transportation->name) : null,
                    'hotel_star' => isset($package->hotel_star_id) ? ($package->hotel_star_id) : null,
                    'tour_type' => $package->tour_type,
                    'trip' => $package->tour_type == 1 ? 'Domestic' : 'International',
                    'trip_type' => $package->trip_type,
                    'type_of_tour_packages' => $package->trip_type == 1 ? 'Standard' : 'Weekend',
                    'themes' => $themes,
                    'featured_image_path' => $package->first_gallery_image,
                    'inclusions_in_package' => $inclusions,
                    'inclusions_list' => $inclusionList,
                    'exclusions_list' => $exclusionList,
                    'cancellation_policy' => $package->cancellation_policy,
                    'stay_plan' => $stayPlan,
                    'bulk_discounts' => $package->bulkDiscounts,
                    'gallery_images' => $galleryImages,
                    'tour_circuit' => $package->tour_circuit,
                    'region_id' => $package->region_id,
                    'status' => $package->status,
                    'is_transport' => $package->is_transport,
                    'is_flight' => $package->is_flight,
                    'is_train' => $package->is_train,
                    'is_hotel' => $package->is_hotel,
                    'is_meal' => $package->is_meal,
                    'is_sightseeing' => $package->is_sightseeing,
                    'itinerary' => $this->transformItinerary($package->itinerary),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
    }

    public function bookingOnReqApprove(Request $request)
    {

        $id = $request->input('id');
        $siteUrl = env('SITE_URL');

        // Find the BookingOnRequest record
        $bookingOnRequest = BookingOnRequest::findOrFail($id);

        // Update status to approved (assuming 'approved' status is 1)
        $bookingOnRequest->status = 1;
        $bookingOnRequest->save();

        // Retrieve customer details for sending email
        $customer = User::findOrFail($bookingOnRequest->customer_id);

        // Retrieve customer's first and last name from CustomerDetail
        $customerDetail = $customer->customerDetail;
        $customerName = $customerDetail ? $customerDetail->first_name . ' ' . $customerDetail->last_name : 'Customer';

        // Fetch package name from packages table
        $package = DB::table('packages')
            ->where('id', $bookingOnRequest->package_id)
            ->select('name')
            ->first();

        // Check if package exists
        if ($package) {
            // Prepare data for the email template
            $data = [
                'customerName' => $customerName,
                'tourName' => $package->name,
                'requestedDate' => $bookingOnRequest->created_at->toDateString(),
                'bookingLink' => $siteUrl . 'package/' . $bookingOnRequest->package_id . '/review?token=' . $bookingOnRequest->token,
                'siteUrl' => config('app.url'),
                'siteName' => config('app.name'),
                'appUrl' => config('app.url')
            ];

            // Send approval email to the customer
            $mailSent = Helper::sendMail($customer->email, $data, 'mail.vendorOnReqApprove', 'Booking Approved');//$customer->email

            if ($mailSent) {
                return response()->json(['res' => true, 'msg' => 'Booking request approved and email sent successfully']);
            } else {
                return response()->json(['res' => false, 'msg' => 'Booking request approved but failed to send email'], 500);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'Package not found for the booking request'], 404);
        }
    }

    public function bookingOnReqDeclined(Request $request)
    {

        $id = $request->input('id');
        $siteUrl = env('SITE_URL');

        // Find the BookingOnRequest record
        $bookingOnRequest = BookingOnRequest::findOrFail($id);

        // Update status to approved (assuming 'approved' status is 1)
        $bookingOnRequest->status = 2;
        $bookingOnRequest->save();

        // Retrieve customer details for sending email
        $customer = User::findOrFail($bookingOnRequest->customer_id);

        // Retrieve customer's first and last name from CustomerDetail
        $customerDetail = $customer->customerDetail;
        $customerName = $customerDetail ? $customerDetail->first_name . ' ' . $customerDetail->last_name : 'Customer';

        // Fetch package name from packages table
        $package = DB::table('packages')
            ->where('id', $bookingOnRequest->package_id)
            ->select('name')
            ->first();

        // Check if package exists
        if ($package) {
            // Prepare data for the email template
            $data = [
                'customerName' => $customerName,
                'tourName' => $package->name,
                'requestedDate' => $bookingOnRequest->created_at->toDateString(),
                'siteUrl' => config('app.url'),
                'siteName' => config('app.name'),
                'appUrl' => config('app.url')
            ];

            // Send approval email to the customer
            $mailSent = Helper::sendMail($customer->email, $data, 'mail.vendorOnReqDeclined', 'Booking Declined');//$customer->email

            if ($mailSent) {
                return response()->json(['res' => true, 'msg' => 'Booking request approved and email sent successfully']);
            } else {
                return response()->json(['res' => false, 'msg' => 'Booking request approved but failed to send email'], 500);
            }
        } else {
            return response()->json(['res' => false, 'msg' => 'Package not found for the booking request'], 404);
        }
    }

    public function bookingOnReqTokenVerified(Request $request)
    {
        try {
            $token = $request->input('token');

            // Retrieve the BookingOnRequest record where token matches
            $bookingOnRequest = BookingOnRequest::where('token', $token)->first();

            // Check if the booking record exists
            if ($bookingOnRequest) {
                return response()->json([
                    'res' => true,
                    'msg' => 'Booking request found',
                    'data' => $bookingOnRequest
                ], 200);
            } else {
                return response()->json([
                    'res' => false,
                    'msg' => 'Booking request not found',
                    'data' => []
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => 'An error occurred while verifying the token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function agentCommissionList(Request $request)
    {
    try {
        $user = Auth::user();
        if (!$user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $agentCode = AgentDetails::where('user_id', $user->id)->value('agent_code');

        $filters = $request->all(); // Get all request filters
        $agentBookingList = $this->bookingService->agentCommissionList($agentCode, $filters);
        $currentDate = now()->toDateString();

        $filteredBookings = $agentBookingList->map(function ($booking) use ($currentDate) {
            // Map booking status
            $booking->booking_status = $this->mapBookingStatus($booking->booking_status);

            // Map payment status
            $booking->payment_status = $this->mapPaymentStatus($booking->payment_status);

            // Map claim status
            $booking->claim_status = $this->mapClaimStatus($booking->claim_status);

            // Fetch Booking model for detailed calculations
            $bookingDetails = Booking::with(['payments', 'cancellations'])
                ->find($booking->booking_id); // Assuming booking_id is the primary key of Booking

            // Calculate total payments
            $totalPayments = $bookingDetails->payments->sum('paid_amount');

            // Calculate total cancellations
            $totalCancellations = $bookingDetails->cancellations->sum('refund_amount');

            // Assign calculated values with absolute values and rounding
            $booking->paid_amount = number_format(abs($totalPayments), 2, '.', '');
            $booking->cancelled_amount = number_format(abs($totalCancellations), 2, '.', '');
            $booking->net_amount = number_format(abs($totalPayments - $totalCancellations), 2, '.', '');
            $booking->commission = number_format((($booking->base_price * $booking->commission) / 100), 2, '.', '');
            $booking->incentive = number_format((($booking->base_price * $booking->group_commission) / 100), 2, '.', '');
            $booking->total_commission = number_format($booking->commission_amount, 2, '.', '');

            
            $booking->vpdfUrl = $booking->voucher_number != null ? url('storage/app/public/vouchers/' . $booking->voucher_number.'.pdf') : '';
           

            $booking->pdfUrl = $booking->invoice_number != null ? url('storage/app/public/invoiceAgent/' . $booking->invoice_number.'.pdf') : '';

            return $booking;
        });

        return response()->json(['res' => true, 'msg' => 'Bookings retrieved successfully', 'data' => $filteredBookings], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
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

    private function mapPaymentStatus($status)
    {
        switch ($status) {
            case 0:
                return 'Open';
            case 1:
                return 'In Process';
            case 2:
                return 'Paid';
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
                return 'Withdrawn';
            case 3:
                return 'Cancelled';
            default:
                return 'Unknown';
        }
    }

    public function addBookingPayment(Request $request)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $booking = Booking::find($request->booking_id);
            if (!$booking) {
                return response()->json(['res' => false, 'msg' => 'No booking found!', 'data' => ''], 400);
            }
            $totalPayments = $booking->payments()->sum('paid_amount');

            if ($totalPayments >= $booking->final_price) {
                return response()->json(['res' => false, 'msg' => 'Total amount already paid!', 'data' => ''], 400);
            }


            $bookingPayment = new BookingPayment();
            $bookingPayment->user_id = $user->id;
            $bookingPayment->booking_id = $request->booking_id;
            $bookingPayment->total_amount = $booking->final_price;
            $bookingPayment->paid_amount = $request->paid_amount;
            $bookingPayment->transaction_number = $request->transaction_number;
            $bookingPayment->payment_date = now();



            // Determine payment type based on whether it covers the full amount or not
            if ($totalPayments + $request->paid_amount == $booking->final_price) {
                $bookingPayment->payment_type = 'final';
            } else {
                $bookingPayment->payment_type = 'partial';
            }

            $nextPaymentDate = null;
            if ($bookingPayment->payment_type == 'partial') {
                $packageDetails = Package::find($booking->package_id);
                $paymentPolicy = explode(',', $packageDetails->payment_policy);
                $bookingDate = BookingDate::where('booking_id', $booking->id)->orderBy('id', 'asc')->value('booking_date');

                // Fetch existing booking payments for the booking
                $existingPayments = BookingPayment::where('booking_id', $booking->id)->get();
                $existingPaymentsCount = $existingPayments->count();
                $totalPaidSoFar = $existingPayments->sum('paid_amount');

                // Remove payment policies according to the number of existing payments
                for ($i = 0; $i < $existingPaymentsCount; $i++) {
                    array_shift($paymentPolicy);
                }

                // Calculate the next payment date dynamically
                $totalPaidPercentage = (($totalPaidSoFar + $request->paid_amount) / $booking->final_price) * 100;
                foreach ($paymentPolicy as $index => $policy) {
                    list($startDays, $endDays, $percent) = explode('-', $policy);
                    if ($totalPaidPercentage == $percent) {
                        // Check if there's a next policy
                        if (isset($paymentPolicy[$index + 1])) {
                            list($nextStartDays, $nextEndDays, $nextPercent) = explode('-', $paymentPolicy[$index + 1]);
                            $nextPaymentDate = Carbon::parse($bookingDate)->subDays($nextEndDays)->format('Y-m-d');
                        } else {
                            // No more policies, so no further payments are required
                            $nextPaymentDate = null;
                        }
                        break;
                    }
                }
            }

            $bookingPayment->next_payment_date = $nextPaymentDate;

            $bookingPayment->save();

            $result = [
                'res' => true,
                'msg' => 'Payment successfully done',
                'data' => $nextPaymentDate,
            ];

            return response()->json($result);
        } else {
            return response()->json(['msg' => 'Unauthorized'], 401);
        }
    }

    public function claimBooking($id, Request $request)
    {
        try {
            Log::info('Claiming booking with ID: ' . $id);

            $booking = BookingCommission::where('booking_id', $id)->firstOrFail();
            if ($booking->claim_status != 0) { // assuming 0 means not claimed
                return response()->json([
                    'res' => false,
                    'msg' => 'Booking has already been claimed or cannot be claimed.'
                ], 400);
            }

            $booking->claim_status = 1; // 1 means claimed
            $booking->payment_status = 1;
            $booking->claimed_date = now();
            $booking->save();

            $userId = auth()->id();
            $agentDetail = AgentDetails::where('user_id', $userId)->first();
            $agentFullName = $agentDetail->first_name . ' ' . $agentDetail->last_name;
            $notificationMessage = "$agentFullName claimed commission for Booking Id: #$booking->booking_number";

            Helper::sendNotification(34, $notificationMessage);

            return response()->json([
                'res' => true,
                'msg' => 'Booking claimed successfully.',
                'booking' => $booking
            ], 200);
        } catch (ModelNotFoundException $e) {
            Log::error('Booking not found for ID: ' . $id);
            return response()->json([
                'res' => false,
                'msg' => 'Booking not found.'
            ], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error claiming booking: ' . $e->getMessage());
            return response()->json([
                'res' => false,
                'msg' => 'An error occurred while claiming the booking.',
                'error' => $e->getMessage() // Include error message for more context
            ], 500);
        }
    }

    public function agentPaymentHistory(Request $request)
    {
    try {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $bookingList = $this->bookingService->agentPaymentHistory($userId);
        return response()->json(['res' => true, 'msg' => 'Bookings retrieved successfully', 'data' => $bookingList], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    } 

    public function paymentHistory(Request $request)
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $paymentList = $this->bookingService->paymentHistory($userId, $startDate, $endDate);

            // Prepare the response with PDF URL included
            $response = [
                'res' => true,
                'msg' => 'Payment history retrieved successfully',
                'data' => $paymentList
            ];

            return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function refundHistory(Request $request)
    {
        try {
            $userId = auth()->id();
            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
    
            $refundList = $this->bookingService->refundHistory($userId, $startDate, $endDate);
    
            // Prepare the response with PDF URL included
            $response = [
                'res' => true,
                'msg' => 'Refund history retrieved successfully',
                'data' => $refundList
            ];
    
            return response()->json($response, 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getPackageAddons($id)
    {
        $bookingAddonIds = Booking::where('id', $id)->pluck('add_on_id')->first();
        $addonIdsArray = explode(',', $bookingAddonIds);
        return PackageAddon::whereIn('id', $addonIdsArray)->get()->map(function ($addon) {
            return [
                'title' => $addon->title,
                'description' => $addon->description,
                'price' => $addon->price,
            ];
        });
    }

    public function generateCommissionVouchers(Request $request)
    {
        try {
            $result = $this->bookingService->generateCommissionVouchers();
            return response()->json(['res' => true, 'msg' => 'Invoices generated successfully', 'data' => $result], 200);
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function commissionInvoice(Request $request)
    {
        try {
            $result = $this->bookingService->commissionInvoice();
            return response()->json(['res' => true, 'msg' => 'Invoices generated successfully', 'data' => $result], 200);
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
    public function customerInvoice(Request $request)
    {
        try {
            $result = $this->bookingService->customerInvoice();
            
            return response()->json(['res' => true, 'msg' => 'Invoices generated successfully', 'data' => $result], 200);
        } catch (\Exception $e) {
            return response()->json(['res' => false, 'msg' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
    public function commissionDetails($id)
    {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $bookingDetails = $this->bookingService->getCommissionDetails($id);

        if (!$bookingDetails) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
       // Check if the user is authorized to view the booking using the agent code
        $agentCode = $bookingDetails->first()['agent_code'];
        if (!$agentCode || $userId != AgentDetails::where('agent_code', $agentCode)->value('user_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $refundAmountSum = BookingCancellation::where('booking_id', $id)
            ->where('refund_amount', '>', 0)
            ->sum('refund_amount');

        $currentDate = now()->toDateString();
        $filteredBookings = $bookingDetails->map(function ($booking) use ($currentDate) {


            if ($booking->booking_status == 1) {
                $booking->booking_status = 'In process';
            } elseif ($booking->booking_status == 2) {
                $booking->booking_status = 'Confirmed';
          
            } elseif ($booking->booking_status == 3) {
                $booking->booking_status = 'Completed';
            } elseif ($booking->booking_status == 4) {
                $booking->booking_status = 'Cancelled';
            }

           return $booking;
        });


        $booking_passengers = BookingPassenger::where('booking_id', $id)->get();
        $booking_rooms = BookingRoom::where('booking_id', $id)->get();
        $packageId = Booking::where('id', $id)->pluck('package_id');
        $booking_train = PackageTrain::where('package_id', $packageId)->get();
        $booking_flight = PackageFlight::where('package_id', $packageId)->get();
        $booking_LocalTransport = PackageLocalTransport::where('package_id', $packageId)->get();

        $bookingAddonIds = Booking::where('id', $id)->pluck('add_on_id')->first();

        $addonIdsArray = explode(',', $bookingAddonIds);

        $packageAddons = PackageAddon::whereIn('id', $addonIdsArray)->get();
        $bookingCancellation = BookingCancellation::where('booking_id', $id)->get();
        $addonDetails = $packageAddons->map(function ($addon) {
            return [
                'title' => $addon->title,
                'description' => $addon->description,
                'price' => $addon->price,
            ];
        });
        $reportGalleryImages = BookingReportGallery::where('booking_id', $id)->get();
        $package = $this->bookingService->getPackageDetails($packageId);

        $galleryImages = [];
        foreach ($package->gallery_images as $image) {
            $galleryImages[] = [
                'id' => $image['id'],
                'path' => $image['path'],
            ];
        }

        $itinerary = $package->itinerary ?? [];
        $inclusionList = $package->inclusions ?? [];
        $exclusionList = $package->exclusions ?? [];
        $totalDays = $package->total_days;
        $totalNights = $totalDays - 1;
        $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";
        $totalTrainNights = $package->trains()->where('number_of_nights', 1)->count();
        $inclusionIds = explode(',', $package->inclusions_in_package);
        $inclusions = DB::table('package_inclusions')
            ->whereIn('id', $inclusionIds)
            ->select(['name'])
            ->get();


        $themesIds = explode(',', $package->themes_id);
        $themes = DB::table('themes')
            ->whereIn('id', $themesIds)
            ->select(['id', 'name', 'image', 'icon'])
            ->get();

        $stayPlan = $package->stay_plan;
        $packagePrice = $package->starting_price;
        $platformCharges = $package->starting_price * ($package->platform_charges / 100);
        $packagePrice += $platformCharges;

        $transformedPackage = [
            'package_id' => $package->id,
            'vendor_name' => $package->vendor_name,
            'package_name' => $package->name,
            'total_days' => $totalDaysAndNights,
            'total_days_count' => (int)$totalDays,
            'total_nights_count' => (int)$totalNights,
            'total_train_nights_count' => (int)$package->trains()->where('number_of_nights', 1)->count(),
            'starting_price' => $package->starting_price,
            'child_price' => $package->child_price,
            'infant_price' => $package->infant_price,
            'single_occupancy_price' => $package->single_occupancy_price,
            'triple_occupancy_price' => $package->triple_occupancy_price,
            'platform_charges' => $package->platform_charges,
            'website_price' => $packagePrice,
            'origin' => $package->origin_city_id,
            'destination_state_id' => $package->destination_state_id,
            'state_name' => $package->state_name,
            'destination_city_id' => $package->destination_city_id,
            'cities_name' => $package->cities_name,
            'keywords' => $package->keywords,
            'transportation_name' => isset($package->transportation->name) ? ($package->transportation->name) : null,
            'hotel_star' => isset($package->hotel_star_id) ? ($package->hotel_star_id) : null,
            'tour_type' => $package->tour_type,
            'trip' => $package->tour_type == 1 ? 'Domestic' : 'International',
            'trip_type' => $package->trip_type,
            'type_of_tour_packages' => $package->trip_type == 1 ? 'Standard' : 'Weekend',
            'themes' => $themes,
            'featured_image_path' => $package->first_gallery_image,
            'inclusions_in_package' => $inclusions,
            'inclusions_list' => $inclusionList,
            'exclusions_list' => $exclusionList,
            'cancellation_policy' => $package->cancellation_policy,
            'payment_policy' => $package->payment_policy,
            'stay_plan' => $stayPlan,
            'bulk_discounts' => $package->bulkDiscounts,
            'gallery_images' => $galleryImages,
            'tour_circuit' => $package->tour_circuit,
            'region_id' => $package->region_id,
            'status' => $package->status,
            'is_transport' => $package->is_transport,
            'is_flight' => $package->is_flight,
            'is_train' => $package->is_train,
            'is_hotel' => $package->is_hotel,
            'is_meal' => $package->is_meal,
            'is_sightseeing' => $package->is_sightseeing,
            'itinerary' => $this->transformItinerary($package->itinerary),
        ];

        return response()->json(['res' => true, 'msg' => 'Booking retrieved successfully', 'data' => $filteredBookings, 'booking_passengers' => $booking_passengers, 'booking_rooms' => $booking_rooms, 'booking_train' => $booking_train, 'booking_flight' => $booking_flight, 'booking_LocalTransport' => $booking_LocalTransport, 'packageAddon' => $addonDetails, 'package' => $transformedPackage, 'refund_amount_sum' => $refundAmountSum, 'bookingCancellation' => $bookingCancellation, 'reportGalleryImages' => $reportGalleryImages], 200);
    }
    public function agentPaymentByInvoice(Request $request)
   {
    try {
        $user = Auth::user();
        if (!$user->id) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $agentCode = AgentDetails::where('user_id', $user->id)->value('agent_code');
        $filters = $request->all(); // Get all request filters
        $agentBookingList = $this->bookingService->agentCommissionListByInvoice($agentCode, $filters);
        $currentDate = now()->toDateString();
        $filteredBookings = $agentBookingList->map(function ($booking) use ($currentDate) {
            // Map booking status
            $booking->booking_status = $this->mapBookingStatus($booking->booking_status);

            // Map payment status
            $booking->payment_status = $this->mapPaymentStatus($booking->payment_status);

            // Map claim status
            $booking->claim_status = $this->mapClaimStatus($booking->claim_status);

            // Fetch Booking model for detailed calculations
            $bookingDetails = Booking::with(['payments', 'cancellations'])
                ->find($booking->booking_id); // Assuming booking_id is the primary key of Booking

            // Calculate total payments
            $totalPayments = $bookingDetails->payments->sum('paid_amount');

            // Calculate total cancellations
            $totalCancellations = $bookingDetails->cancellations->sum('refund_amount');

            // Assign calculated values with absolute values and rounding
            $booking->paid_amount = number_format(abs($totalPayments), 2, '.', '');
            $booking->cancelled_amount = number_format(abs($totalCancellations), 2, '.', '');
            $booking->net_amount = number_format(abs($totalPayments - $totalCancellations), 2, '.', '');
            $booking->commission = number_format((($booking->base_price * $booking->commission) / 100), 2, '.', '');
            $booking->incentive = number_format((($booking->base_price * $booking->group_commission) / 100), 2, '.', '');
            $booking->total_commission = number_format($booking->commission_amount, 2, '.', '');

            return $booking;
        });

        return response()->json(['res' => true, 'msg' => 'Bookings retrieved successfully', 'data' => $filteredBookings], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }

    public function commissionLedger(Request $request)
    {
    $user = Auth::user();
    if (!$user || !$user->id) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Get the start and end dates from the request
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // If dates are not provided, use the current month's first date and today's date
    if (!$startDate) {
        $startDate = Carbon::now()->firstOfMonth()->toDateString();
    }
    if (!$endDate) {
        $endDate = Carbon::now()->toDateString();
    }

    try {
        // Get agent ledger
        $ledger = $this->bookingService->getAgentLedger($user->id, $startDate, $endDate);
        
        // Fetch agent details
        $agentDetails = AgentDetails::where('user_id', $user->id)->first();
        $agMobile = $user->mobile;
        $agEmail = $user->email;

        // Calculate total debits and credits
        $totalDebit = array_reduce($ledger, function($carry, $item) {
            return $carry + ($item['type'] === 'debit' || $item['type'] === 'opening-balance' ? $item['amount'] : 0);
        }, 0);

        $totalCredit = array_reduce($ledger, function($carry, $item) {
            return $carry + ($item['type'] === 'credit' ? $item['amount'] : 0);
        }, 0);

        $finalBalance = $totalDebit - $totalCredit;

        $closingBalanceEntry = array_filter($ledger, function($item) {
            return $item['type'] === 'closing-balance';
        });

        // Use reset to get the first element of the array
        $closingBalanceEntry = reset($closingBalanceEntry);
        $closingBalanceDate = $closingBalanceEntry ? $closingBalanceEntry['date'] : '';
        $companyName = DB::table('configs')->where('name', 'company_name')->value('value');
        $companyAddress = DB::table('configs')->where('name', 'company_address')->value('value');

        // Generate PDF URL
        $pdfUrl = $this->bookingService->generateLedger($companyName ,  $companyAddress , $agentDetails, $ledger, $agMobile, $totalDebit, $totalCredit, $finalBalance, $closingBalanceDate);

        return response()->json([
            'res' => true,
            'msg' => 'Ledger fetched successfully',
            'data' => $ledger,
            'companyName' => $companyName,
            'companyAddress' => $companyAddress,
            'agentDetails' => $agentDetails,
            'agMobile' => $agMobile,
            'agEmail'=> $agEmail,
            'pdfUrl' => $pdfUrl,
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['res' => false, 'msg' => 'An error occurred', 'error' => $e->getMessage()], 500);
    }
    }


    public function reminderSms() // waiting for template
    {
        $today = Carbon::now();
        $reminderDays = [7, 5, 3, 1];
    
        foreach ($reminderDays as $daysBefore) {
            $reminderDate = $today->copy()->addDays($daysBefore);
    
            $bookingPayments = BookingPayment::where('payment_type', 'partial')
                ->whereDate('next_payment_date', '=', $reminderDate)
                ->get();
    
            foreach ($bookingPayments as $payment) {
                $user = User::findOrFail($payment->user_id);
                $booking = Booking::find($payment->booking_id);
                if (!$booking) {
                    return response()->json(['res' => false, 'msg' => 'No booking found!', 'data' => ''], 400);
                }
                if (!empty($user->mobile)) {
                    $dueDate = Carbon::parse($payment->next_payment_date)->format('d-m-Y');
                    $smsMessage = "Dear Customer, your payment of {$payment->total_amount} is due on {$dueDate} for booking {$booking->booking_number}. Please make payment within due date by logging in your account in wishmytour.in to avoid cancellation. Ignore, if paid.";

                    $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172197679933379');
    
                    if ($smsSent) {
                        // Log successful SMS send
                        \Log::info("SMS sent successfully to {$user->mobile} for booking payment ID {$smsMessage}");
                    } else {
                        // Log failure to send SMS
                        \Log::error("Failed to send SMS to {$user->mobile} for booking payment ID {$payment->id}");
                    }
                }
    
                if (!empty($user->email)) {
                    $data = [
                        'amount' => $payment->total_amount,
                        'dueDate' => $payment->next_payment_date,
                    ];
    
                    try {
                        $mailSent = Helper::sendMail($user->email, $data, 'mail.reminder', 'Reminder Booking');
                        \Log::info("Email sent successfully to {$user->email} for booking payment ID {$payment->id}");
                    } catch (\Exception $e) {
                        \Log::error("Failed to send email to {$user->email} for booking payment ID {$payment->id}. Error: {$e->getMessage()}");
                    }
                }
            }
        }
    }

    /**
     * Vendor Booking History
     * 
     * This function retrieves the booking history for a vendor based on the provided filters. It processes the booking status
     * and returns the filtered booking list along with the counts of all bookings, cancelled bookings, modified bookings, 
     * and completed bookings.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookingHistory(Request $request)
    {
    try {
        $userId = auth()->id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $filters = $request->all();
        $bookingHistory = $this->bookingService->bookingHistory($userId, $filters);
        $currentDate = now()->toDateString();

        $totalBookingsCount = $bookingHistory->count();
        $cancelledBookingsCount = 0;
        $modifiedBookingsCount = 0;
        $completedBookingsCount = 0;

        $filteredBookings = $bookingHistory->map(function ($booking) use ($currentDate, &$cancelledBookingsCount, &$modifiedBookingsCount, &$completedBookingsCount) {
            if ($booking->booking_status == 1) {
                $booking->booking_status = 'In process';
            } elseif ($booking->booking_status == 2) {
                $booking->booking_status = 'Confirmed';
            } elseif ($booking->booking_status == 3) {
                $booking->booking_status = 'Completed';
                $completedBookingsCount++;
            } elseif ($booking->booking_status == 4) {
                $booking->booking_status = 'Cancelled';
                $cancelledBookingsCount++;
            } elseif ($booking->booking_status == 5) {
                $booking->booking_status = 'Modified';
                $modifiedBookingsCount++;
            } elseif ($booking->booking_status == 6) {
                $booking->booking_status = 'On Request';
            }
            return $booking;
        });

        return response()->json([
            'res' => true,
            'msg' => 'Bookings retrieved successfully',
            'data' => $filteredBookings,
            'totalBookingsCount' => $totalBookingsCount,
            'cancelledBookingsCount' => $cancelledBookingsCount,
            'modifiedBookingsCount' => $modifiedBookingsCount,
            'completedBookingsCount' => $completedBookingsCount
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
    }

}