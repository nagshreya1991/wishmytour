<?php

namespace Modules\Booking\App\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\app\Models\Booking;
use Modules\Booking\app\Models\BookingCustomerDetails;
use Modules\Booking\app\Models\BookingRoom;
use Modules\Booking\app\Models\BookingDate;
use Modules\Booking\app\Models\BookingPassenger;
use Modules\Package\app\Models\Package;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Modules\Package\app\Models\PackageAddon;
use Modules\Booking\app\Models\BookingMessage;
use Modules\User\app\Models\Vendor;
use App\Helpers\Helper;
use App\Models\Config;
use Modules\Package\app\Models\Itinerary;
use Carbon\Carbon;
use Modules\Package\app\Models\State;
use Modules\Package\app\Models\City;
use Modules\Package\app\Models\Wishlist;
use Modules\Booking\app\Models\BookingCancellation;
use Modules\Booking\app\Models\BookingReportGallery;
use Illuminate\Support\Facades\Validator;
use Modules\Booking\app\Models\BookingOnRequest;
use Modules\Booking\app\Models\BookingCommission;
use Modules\Booking\app\Models\BookingPayment;
use Illuminate\Support\Str;
use Modules\User\app\Models\User;
use Modules\User\app\Models\AgentDetails;
use Modules\User\app\Models\CommissionGroup;

class BookingService
{
    /**
     * Add a new booking.
     *
     * @param array $requestData
     * @return array
     */
    public function addBooking(array $requestData): array
{
    try {
        $baseAmount = $requestData['base_amount'] ?? 0.00;
        $booking = Booking::create([
            'booking_number' => $requestData['package_id'],
            'package_id' => $requestData['package_id'],
            'customer_id' => $requestData['customer_id'],
            'add_on_id' => $requestData['add_on_id'] ?? null,
            'base_amount' => $baseAmount,
            'booking_status' => 2, // 2=>confirmed
            'coupon_id' => $requestData['coupon_id'] ?? null,
            'addon_total_price' => $requestData['addon_total_price'] ?? 0,
            'website_price' => $requestData['website_price'] ?? 0,
            'website_percent' => $requestData['website_percent'] ?? 0,
            'gst_price' => $requestData['gst_price'] ?? 0,
            'gst_percent' => $requestData['gst_percent'] ?? 0,
            'tcs' => $requestData['tcs'] ?? 0,
            'coupon_price' => $requestData['coupon_price'] ?? 0,
            'final_price' => $requestData['final_price'] ?? 0,
            'agent_code' => $requestData['agent_code'] ?? null,
        ]);

        $finalPriceInWords = Helper::AmountInWords($requestData['final_price']);
        $packageDetails = Package::find($requestData['package_id']);

        $bookingRooms = [];
        $bookingPassengers = [];
        foreach ($requestData['rooms'] as $room) {
            $bookingRoom = new BookingRoom([
                'package_id' => $requestData['package_id'],
                'room_no' => $room['room_no'] ?? null,
                'adults' => $room['adults'] ?? null,
                'children' => $room['children'] ?? null,
            ]);
            $booking->rooms()->save($bookingRoom);

            if (isset($room['passengers'])) {
                foreach ($room['passengers'] as $passenger) {
                    $bookingPassenger = new BookingPassenger([
                        'booking_id' => $booking->id,
                        'package_id' => $requestData['package_id'],
                        'title' => $passenger['title'],
                        'first_name' => $passenger['first_name'] ?? null,
                        'last_name' => $passenger['last_name'] ?? null,
                        'dob' => $passenger['dob'] ?? null,
                        'gender' => $passenger['gender'] ?? null,
                        'is_adult' => $passenger['is_adult'] ?? 0,
                        'price' => $passenger['price'] ?? 0,
                    ]);
                    $bookingRoom->passengers()->save($bookingPassenger);
                    $bookingPassengers[] = $bookingPassenger;
                }
            }
            $bookingRooms[] = $bookingRoom;
        }

        // Handle Booking Seat Availability data
        $bookingDates = [];
        if (isset($requestData['seats'])) {
            foreach ($requestData['seats'] as $seat) {
                $bookingDates[] = new BookingDate([
                    'package_id' => $requestData['package_id'],
                    'booking_date' => $seat['booking_date'] ?? null,
                    'cost' => $seat['cost'] ?? null,
                ]);
            }
            $booking->dates()->saveMany($bookingDates);
        }

        // Handle Customer Details
        $bookingCustomer = new BookingCustomerDetails([
            'package_id' => $requestData['package_id'],
            'name' => $requestData['customer_name'] ?? null,
            'address' => $requestData['customer_address'] ?? null,
            'email' => $requestData['customer_email'] ?? null,
            'phone_number' => $requestData['customer_phone_number'] ?? null,
            'state_id' => $requestData['customer_state_id'] ?? null,
            'pan_number' => $requestData['customer_pan_number'] ?? null,
            'booking_for' => $requestData['customer_booking_for'] ?? null,
        ]);
        $booking->customer()->save($bookingCustomer);

        $bookingPayment = new BookingPayment();
        $bookingPayment->user_id = auth()->id();
        $bookingPayment->booking_id = $booking->id;
        $bookingPayment->total_amount = $requestData['final_price'];
        $bookingPayment->paid_amount = $requestData['paid_amount'];
        $bookingPayment->payment_date = now();
        $uniqueId = strtoupper(uniqid());
        $transactionNumber = 'TXN' . preg_replace('/[^A-Z0-9_-]/', '', $uniqueId);

        // Ensure the length is less than 35 characters
        if (strlen($transactionNumber) >= 35) {
            $transactionNumber = substr($transactionNumber, 0, 34);
        }

        $bookingPayment->transaction_number = $transactionNumber;
        $bookingPayment->payment_type = $requestData['paid_amount'] == 0 ? 'initial' : ($requestData['paid_amount'] == $requestData['final_price'] ? 'final' : 'partial');

        $nextPaymentDate = null;
        if ($bookingPayment->payment_type == 'partial') {
            $bookingDate = $requestData['seats'][0]['booking_date'];
            $paymentPolicy = explode(',', $packageDetails->payment_policy);
            $totalPaidPercentage = ($requestData['paid_amount'] / $requestData['final_price']) * 100;
            foreach ($paymentPolicy as $policy) {
                list($startDays, $endDays, $percent) = explode('-', $policy);
                if ($totalPaidPercentage < $percent) {
                    $nextPaymentDate = Carbon::parse($bookingDate)->subDays($endDays)->format('Y-m-d');
                    break;
                }
            }
        }

        $bookingPayment->next_payment_date = $nextPaymentDate;
        $bookingPayment->save();

        // Handle seat availability
        $package = Package::find($requestData['package_id']);
        $totalPassengers = array_reduce($requestData['rooms'], function($carry, $room) {
            return $carry + ($room['adults'] ?? 0) + ($room['children'] ?? 0);
        }, 0);

        // Debugging: Log total passengers and current total seats
        Log::info('Total Passengers:', ['total_passengers' => $totalPassengers]);
        Log::info('Current Total Seats:', ['total_seat' => $package->total_seat]);

        $updatedSeats = false; // Flag to track if seats have been updated

        foreach ($bookingDates as $bookingDate) {
            // Check if the booking date exists in package_seat_availability
            $availability = DB::table('package_seat_availability')
                ->where('package_id', $requestData['package_id'])
                ->where('date', $bookingDate->booking_date)
                ->first();

            if ($availability) {
                // If the date exists, decrement the seat count by total passengers
                Log::info('Updating seat availability for date:', ['date' => $bookingDate->booking_date, 'current_seat' => $availability->seat]);

                DB::table('package_seat_availability')
                    ->where('package_id', $requestData['package_id'])
                    ->where('date', $bookingDate->booking_date)
                    ->decrement('seat', $totalPassengers);

                // Log updated seat availability
                $updatedAvailability = DB::table('package_seat_availability')
                    ->where('package_id', $requestData['package_id'])
                    ->where('date', $bookingDate->booking_date)
                    ->first();
                Log::info('Updated Seat Availability:', ['availability' => $updatedAvailability]);

                $updatedSeats = true; // Flag indicating that seats have been updated in availability
            }
        }

        // If no seat updates were made in availability, decrement from total seats of the package
        if (!$updatedSeats) {
            Log::info('Decrementing package total seats:', ['current_seat' => $package->total_seat]);
            $package->total_seat -= $totalPassengers;
            $package->save();

            // Log package seat update
            Log::info('Updated Package Total Seats:', ['total_seat' => $package->total_seat]);
        }

        // Ensure to log the final state
        Log::info('Final Package Total Seats:', ['total_seat' => $package->total_seat]);

        Helper::sendNotification(auth()->id(), "New Tour Added, here is your booking Id :" . $booking->booking_number);

        return [
            'res' => true,
            'msg' => 'Booking added successfully',
            'data' => [
                'booking' => $booking,
                'transaction_no' => $bookingPayment->transaction_number,
                'rooms' => $bookingRooms,
                'passengers' => $bookingPassengers,
                'dates' => $bookingDates,
                'customer' => $bookingCustomer,
            ],
        ];
    } catch (\Exception $e) {
        return ['res' => false, 'msg' => $e->getMessage()];
    }
}


    public function generateBookingPDF($booking, $bookingRooms, $bookingPassengers, $bookingDates, $bookingCustomer, $packageName, $vendorDetails, $packageAddons, $totalAddonPrice, $finalPriceInWords, $logoUrl, $itineraries, $inclusions, $exclusions, $paymentPolicies, $cancellationPolicies, $packageTerms, $defaultTerms, $totalDaysAndNights, $stayPlan,$bookingPayment)
    {
        $pdf = new Dompdf();
        $pdf->loadHtml(view('booking-pdf', compact('booking', 'bookingRooms', 'bookingPassengers', 'bookingDates', 'bookingCustomer', 'vendorDetails', 'packageName', 'packageName', 'packageAddons', 'totalAddonPrice', 'finalPriceInWords', 'logoUrl', 'itineraries', 'inclusions', 'exclusions', 'paymentPolicies', 'cancellationPolicies', 'packageTerms', 'defaultTerms', 'totalDaysAndNights', 'stayPlan','bookingPayment')));

        // (Optional) Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Render PDF
        $pdf->render();

        // Get PDF content
        return $pdf->output();
    }

    public function updatePaymentStatus($transactionNumber, $paymentStatus)
    {
        // Find the booking payment by transaction number
        $bookingPayment = BookingPayment::where('transaction_number', $transactionNumber)->firstOrFail();
    
        // Update the payment status
        $bookingPayment->payment_status = $paymentStatus;
        $bookingPayment->save();
    
        if ($paymentStatus == 1) { // 1 for SUCCESS
            // Fetch related booking details
            $booking = Booking::where('id', $bookingPayment->booking_id)->first();
            $bookingRooms = BookingRoom::where('booking_id', $bookingPayment->booking_id)->get();
            $bookingPassengers = BookingPassenger::where('booking_id', $bookingPayment->booking_id)->get();
            $bookingDates = BookingDate::where('booking_id', $bookingPayment->booking_id)->get();
            $bookingCustomer = BookingCustomerDetails::where('booking_id', $bookingPayment->booking_id)->get();
            $package = Package::with(['inclusions', 'exclusions'])->find($booking->package_id);
    
            $packageName = $package ? $package->name : '';
            $totalDays = $package->total_days;
            $totalNights = $totalDays - 1;
            $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";
            $vendorDetails = Vendor::where('user_id', $package->user_id)->first();
    
            $packageAddons = [];
            $totalAddonPrice = 0.00;
            if (isset($booking->add_on_id)) {
                $packageAddons = PackageAddon::whereIn('id', explode(',', $booking->add_on_id))->get();
                $totalAddonPrice = $packageAddons->sum('price');
            }
    
            $finalPriceInWords = Helper::AmountInWords($booking->final_price);
            $logoUrl = 'https://wishmytour.in//backend/public/images/logo.jpg';
            $itineraries = Itinerary::where('package_id', $package->id)->get();
    
            // Convert collections to arrays
            $inclusions = $package->inclusions->pluck('name')->toArray();
            $exclusions = $package->exclusions->pluck('name')->toArray();
    
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
    
            $paymentPolicies = explode(',', $package->payment_policy);
            $formattedPaymentPolicies = [];
            foreach ($paymentPolicies as $index => $policy) {
                $policyDetails = explode('-', $policy);
                if ($index === 0) {
                    $formattedPaymentPolicies[] = "{$policyDetails[0]} or more days before departure: {$policyDetails[2]}%";
                } else {
                    $formattedPaymentPolicies[] = "Between {$policyDetails[0]} to {$policyDetails[1]} days before departure: {$policyDetails[2]}%";
                }
            }
            $paymentPolicies = $formattedPaymentPolicies;
    
            $terms = Config::where('name', 'terms_and_conditions')->first();
            $defaultTerms = $terms ? $terms->value : '';
            $packageTerms = $package->terms_and_conditions;
            $stayPlan = $package->getItineraries($package->id);
    
            // Generate PDF
            $pdfContent = $this->generateBookingPDF(
                $booking,
                $bookingRooms,
                $bookingPassengers,
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
                $stayPlan,
                $bookingPayment
            );
    
            // Generate a unique filename with timestamp
            $pdfFileName = 'provisional_confirmation_' . $booking->id . '.pdf';
    
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
    





              //************************SMS ,Booking Confirmation With Amount & ID - Agent***********************************//
             $agent_code = $booking->agent_code;
              try {
                $agentID = AgentDetails::where('agent_code', $agent_code)->value('user_id'); //agent_code
                $agUser = User::find($agentID);

                if ($agUser) {
                $smsMessage = "A new booking {$booking->booking_number} Amount {$booking->final_price} has been completed using your Agent Code: {$agent_code}. Thank you for being a valuable part of our team! -From WishMyTour";
                $smsSent = Helper::sendSMS($agUser->mobile, $smsMessage, '1707172179867076404');

                if ($smsSent) {
                // Log successful SMS send
                \Log::info("SMS sent successfully to {$agUser->mobile} for booking payment ID {$smsMessage}");
                } else {
                // Log failure to send SMS
                \Log::error("Failed to send SMS to {$agUser->mobile} for booking payment ID {$booking->id}");
                }
                    //***********************MAIL************************ */
                    $data = [
                        'booking_number' => $booking->booking_number,
                        'amount' => $booking->final_price,
                        'agent_code' => $agent_code,
                    ];
    
                    try {
                        $mailSent = Helper::sendMail($agUser->email, $data, 'mail.bookingConfirmationAgent', 'New Booking');
                        if ($mailSent) {
                            \Log::info("Mail sent successfully to {$agUser->email} for booking ID {$booking->id}");
                        } else {
                            \Log::error("Failed to send mail to {$agUser->email} for booking ID {$booking->id}");
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error while sending mail to {$agUser->email} for booking ID {$booking->id}: " . $e->getMessage());
                    }

                
                } else {
                \Log::warning("Agent with code {$agent_code} not found for booking ID {$booking->id}");
                }
                } catch (\Exception $e) {
                \Log::error("Error while sending SMS for booking payment ID {$booking->id}: " . $e->getMessage());
                }


                //*****************************************SMS ,Booking Confirm - Customer******************************//
                try {
                   
                    $cusUser = User::find($booking->customer_id);
    
                    if ($cusUser) {
                     //   $formattedDate = Carbon::parse($booking->created_at)->format('d-m-Y');
                        $firstBookingDate = isset($bookingDates[0]) ? Carbon::parse($bookingDates[0]->booking_date)->format('d-m-Y') : null;

                    $cusSmsMessage = "Tour package booking {$booking->booking_number} is confirmed for {$packageName} on {$firstBookingDate} -From Wishmytour ";
                    $smsSent = Helper::sendSMS($cusUser->mobile, $cusSmsMessage, '1707172008295441129');
    
                    if ($smsSent) {
                    // Log successful SMS send
                    \Log::info("SMS sent successfully to {$cusUser->mobile} for booking payment ID {$cusSmsMessage}");
                    } else {
                    // Log failure to send SMS
                    \Log::error("Failed to send SMS to {$cusUser->mobile} for booking payment ID {$booking->id}");
                    }


                     //***********************MAIL************************ */
                     $data = [
                        'booking_number' => $booking->booking_number,
                        'packageName' => $packageName,
                        'firstBookingDate' => $firstBookingDate,
                    ];
    
                    try {
                        $mailSent = Helper::sendMail($cusUser->email, $data, 'mail.bookingConfirmationCustomer', 'New Booking');
                        if ($mailSent) {
                            \Log::info("Mail sent successfully to {$cusUser->email} for booking ID {$booking->id}");
                        } else {
                            \Log::error("Failed to send mail to {$cusUser->email} for booking ID {$booking->id}");
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error while sending mail to {$cusUser->email} for booking ID {$booking->id}: " . $e->getMessage());
                    }


                    } 
                    } catch (\Exception $e) {
                    \Log::error("Error while sending SMS for booking payment ID {$booking->id}: " . $e->getMessage());
                    }


                      //**************************************SMS ,Booking Confirm - Vendor**************************/
                try {
                   
                    $venUser = User::find($vendorDetails->user_id);
    
                    if ($venUser) {
                     //   $formattedDate = Carbon::parse($booking->created_at)->format('d-m-Y');
                      //  $firstBookingDate = isset($bookingDates[0]) ? Carbon::parse($bookingDates[0]->booking_date)->format('d-m-Y') : null;

                    $venSmsMessage = "Hi {$vendorDetails->name}, A new booking has been made! Booking ID: {$booking->booking_number} Customer Name: {$bookingCustomer->name} Package: {$packageName} Date: {$firstBookingDate}. Please confirm at your earliest convenience -From Wishmytour";
                    $smsSent = Helper::sendSMS($venUser->mobile, $venSmsMessage, '1707172025351137805');
    
                    if ($smsSent) {
                    // Log successful SMS send
                    \Log::info("SMS sent successfully to {$venUser->mobile} for booking payment ID {$venSmsMessage}");
                    } else {
                    // Log failure to send SMS
                    \Log::error("Failed to send SMS to {$venUser->mobile} for booking payment ID {$booking->id}");
                    }
                    //***********************MAIL************************ */
                    $data = [
                        'vendorname' => $vendorDetails->name,
                        'booking_number' => $booking->booking_number,
                        'customerName' => $bookingCustomer->name,
                        'firstBookingDate' => $firstBookingDate,
                        'packageName' =>$packageName
                    ];
    
                    try {
                        $mailSent = Helper::sendMail($venUser->email, $data, 'mail.bookingConfirmationVendor', 'New Booking');
                        if ($mailSent) {
                            \Log::info("Mail sent successfully to {$venUser->email} for booking ID {$booking->id}");
                        } else {
                            \Log::error("Failed to send mail to {$venUser->email} for booking ID {$booking->id}");
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error while sending mail to {$venUser->email} for booking ID {$booking->id}: " . $e->getMessage());
                    }


                    } 
                    } catch (\Exception $e) {
                    \Log::error("Error while sending SMS for booking payment ID {$booking->id}: " . $e->getMessage());
                    }
        } else {
            $pdfUrl = null;
        }
    
        return [
            'res' => true,
            'msg' => '',
            'data' => [
                'bookingPayment' => $bookingPayment,
                'pdfFilePath' => $pdfUrl,
            ],
        ];
    }
    
    public function getUpcomingBookingsForUser(int $userId)
    {
        $currentDate = now()->toDateString();

        return Booking::where('customer_id', $userId)
            ->whereNotIn('booking_status', [4, 6])
            ->leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'bookings.created_at as booking_date',
                'packages.id as package_id',
                'packages.*',
                'vendors.name as vendor_fullname',
                DB::raw('(SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date ASC LIMIT 1) as first_booking_date'),
                DB::raw('(SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date DESC LIMIT 1) as last_booking_date')
            )
            ->addSelect(DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'))
            ->where(function ($query) use ($currentDate) {
                $query->whereRaw('date((SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date ASC LIMIT 1)) >= ?', [$currentDate]);
            })
            ->groupBy('bookings.id', 'vendors.name')
            ->orderBy('booking_date', 'desc') // Order by ascending first_booking_date
            ->get();
    }


    public function getCompletedBookingsForUser(int $userId)
    {
        $currentDate = now()->toDateString();
        return Booking::where('customer_id', $userId)
            ->leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.*',
                'vendors.name as vendor_fullname',
                DB::raw('(SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date ASC LIMIT 1) as first_booking_date'),
                DB::raw('(SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date DESC LIMIT 1) as last_booking_date')
            )
            ->addSelect(DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'))
            ->where(function ($query) use ($currentDate) {
                $query->whereRaw('date((SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date ASC LIMIT 1)) < ?', [$currentDate]);
            })
            ->groupBy('bookings.id', 'vendors.name')
            ->orderBy('last_booking_date', 'desc') // Order by ascending first_booking_date
            ->get();
    }



    public function getBookingDetailsPDF($id)
    {
        try {
            $booking = Booking::with(['rooms', 'passengers', 'dates', 'customer'])
                ->leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
                ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
                ->leftJoin('booking_customer_details', 'bookings.id', '=', 'booking_customer_details.booking_id')
                ->leftJoin('states', 'booking_customer_details.state_id', '=', 'states.id')
                ->leftJoin('users', 'bookings.customer_id', '=', 'users.id')
                ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
                ->select(
                    'bookings.*',
                    'bookings.id as bookings_id',
                    'booking_customer_details.*',
                    'vendors.*',
                    'users.id as user_id',
                    'packages.*',
                    'vendors.name as vendor_fullname',
                    'booking_customer_details.name as booking_customer_name',
                    'booking_customer_details.address as booking_customer_address',
                    'booking_customer_details.email as booking_customer_email',
                    'booking_customer_details.phone_number as booking_customer_phone_number',
                    'booking_customer_details.pan_number as booking_customer_pan_number',
                    'states.name as booking_customer_state',
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
            Log::error('Error occurred while fetching booking details: ' . $e->getMessage());
            return null;
        }
    }

    public function getVendorBookingDetails($id)
    {
        $booking = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
            ->leftJoin('states', 'customer_details.state', '=', 'states.id')
            ->leftJoin('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->with('payments')
            ->select(
                'bookings.*',
                'bookings.id as bookings_id',
                'bookings.booking_status as bookings_status_id',
                'vendors.name as vendor_fullname',
                'users.id as user_id',
                'packages.id as package_id',
                'packages.name as package_name',
                DB::raw("CONCAT(customer_details.first_name, ' ', customer_details.last_name) as booking_customer_name"),
                'customer_details.address as booking_customer_address',
                'users.email as booking_customer_email',
                'users.mobile as booking_customer_phone_number',
                'customer_details.pan_number as booking_customer_pan_number',
                'states.name as booking_customer_state',
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
            )
            ->where('bookings.id', $id)
            ->first();

        return $booking; // Return a single booking object
    }


    public function getCustomerBookingDetails($id)
    {

        $booking = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            //->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
            ->leftJoin('booking_customer_details', 'bookings.id', '=', 'booking_customer_details.booking_id')
            // ->leftJoin('cities', 'customer_details.city', '=', 'cities.id')
            ->leftJoin('states', 'booking_customer_details.state_id', '=', 'states.id')
            ->leftJoin('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->with('payments')
            ->select(

                'bookings.*',
                'bookings.id as bookings_id',
                'bookings.booking_status as bookings_status_id',
                //'booking_customer_details.*',
                //  'customer_details.*',
                'users.id as user_id',
                'packages.id as package_id',
                'packages.name as package_name',
                'vendors.name as vendor_fullname',
                // 'customer_details.id as customer_id',
                // 'customer_details.first_name as customer_first_name',
                // 'customer_details.last_name as customer_last_name',
                'booking_customer_details.name as booking_customer_name',

                'booking_customer_details.address as booking_customer_address',
                'booking_customer_details.email as booking_customer_email',
                'booking_customer_details.phone_number as booking_customer_phone_number',
                'booking_customer_details.pan_number as booking_customer_pan_number',
                //'cities.city as customer_city',
                'states.name as booking_customer_state',
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),

            )
            ->where('bookings.id', $id)
            ->first();


        if (!$booking) {
            return collect();
        }

        return collect([$booking]);

    }

    public function getPackageDetails($id)
    {
        $packageDetails = Package::with(['religion', 'themes', 'cityStayPlans', 'inclusions', 'exclusions', 'bulkDiscounts'])
            ->leftJoin('states', 'packages.destination_state_id', '=', 'states.id')
            ->leftJoin('cities', 'packages.destination_city_id', '=', 'cities.id')
            ->leftJoin('users', 'packages.user_id', '=', 'users.id')
            ->leftJoin('stay_plan', 'packages.id', '=', 'stay_plan.package_id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->where('packages.id', $id)
            ->select(
                'packages.*',
                'states.name as state_name',
                'cities.city as cities_name',
                'vendors.name as vendor_fullname',
            )
            ->groupBy('packages.id', 'vendors.name')
            ->first();
        $packageDetails->cancellation_policy = explode(',', $packageDetails->cancellation_policy);
        $packageDetails->payment_policy = explode(',', $packageDetails->payment_policy);
        $packageDetails->stay_plan = $packageDetails->getItineraries($id);
        return $packageDetails;
    }

    public function addMessage(array $data)
    {
        $message = BookingMessage::create([
            'vendor_id' => $data['vendor_id'],
            'package_id' => $data['package_id'],
            'package_name' => $data['package_name'] ?? null,
            'run_date' => $data['run_date'] ?? null,
            'message' => $data['message'] ?? null,
        ]);

        return $message;
    }


    public function getBookingsByVendor(int $userId, array $filters)
    {


        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date'])->toDateString() : null;
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date'])->toDateString() : null;

        $query = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('booking_customer_details', 'bookings.id', '=', 'booking_customer_details.booking_id')
            ->whereNotIn('booking_status', [4,5,6])
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.name as package_name',
                'booking_customer_details.*',
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as booking_cost'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
            )
            ->where('packages.user_id', $userId)
            ->groupBy('bookings.id', 'booking_customer_details.id')
            ->orderBy('bookings.created_at', 'desc');


        if ($startDate && $endDate) {
            $query->whereDate('bookings.created_at', '>=', $startDate)
                ->whereDate('bookings.created_at', '<=', $endDate);
        }
        // Additional filters
        if (isset($filters['booking_id'])) {
            $query->where('bookings.id', $filters['booking_id']);
        }

        if (isset($filters['phone_number'])) {
            $query->where('booking_customer_details.phone_number', $filters['phone_number']);
        }

        if (isset($filters['name'])) {
            $query->where('booking_customer_details.name', 'like', '%' . $filters['name'] . '%');
        }
        if (isset($filters['origin_city_id'])) {
            $query->where('packages.origin_city_id', $filters['origin_city_id']);
        }
        if (isset($filters['booking_status'])) {
            $query->where('bookings.booking_status', $filters['booking_status']);
        }
        if (isset($filters['flag_status'])) {
            $flagStatus = is_array($filters['flag_status']) ? $filters['flag_status'] : explode(',', $filters['flag_status']);
            $query->whereIn('bookings.booking_status', $flagStatus);
        }


        return $query->get();
    }


    public function generateBookingPDFVendor($booking, $bookingRooms, $bookingPassengers, $bookingDates, $bookingCustomer, $packageName, $vendorDetails, $packageAddons, $totalAddonPrice, $finalPriceInWords, $logoUrl, $itineraries, $inclusions, $exclusions, $paymentPolicies, $cancellationPolicies, $packageTerms, $defaultTerms, $totalDaysAndNights, $stayPlan)
    {
        $pdf = new Dompdf();
        $pdf->loadHtml(view('vendor-booking-pdf', compact('booking', 'bookingRooms', 'bookingPassengers', 'bookingDates', 'bookingCustomer', 'vendorDetails', 'packageName', 'packageName', 'packageAddons', 'totalAddonPrice', 'finalPriceInWords', 'logoUrl', 'itineraries', 'inclusions', 'exclusions', 'paymentPolicies', 'cancellationPolicies', 'packageTerms', 'defaultTerms', 'totalDaysAndNights', 'stayPlan')));

        // (Optional) Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Render PDF
        $pdf->render();

        // Get PDF content
        return $pdf->output();
    }

    public function cancelBooking(array $requestData, $user)
    {
        $booking = Booking::find($requestData['booking_id']);
        if (!$booking) {
            return ['res' => false, 'msg' => 'Booking not found', 'status' => 404];
        }
        $uniqueId = strtoupper(uniqid());
        $transactionNumber = 'CN' . preg_replace('/[^A-Z0-9_-]/', '', $uniqueId);

            // Ensure the length is less than 35 characters
            if (strlen($transactionNumber) >= 35) {
                $transactionNumber = substr($transactionNumber, 0, 34);
            }

           

        $cancellation = new BookingCancellation([
            'booking_id' => $booking->id,
            'customer_id' => $user->id,
            // 'cancellation_date' => now(),
            'cancellation_reason' => $requestData['cancellation_reason'],
            'cancellation_type' => $requestData['cancellation_type'],
            'booking_price' => $requestData['booking_price'] ?? 0,
            'cancellation_percent' => $requestData['cancellation_percent'] ?? 0,
            'cancellation_charge' => $requestData['cancellation_charge'] ?? 0,
            'website_percent' => $requestData['website_percent'] ?? 0,
            'website_charge' => $requestData['website_charge'] ?? 0,
            'gst_percent' => $requestData['gst_percent'] ?? 0,
            'gst_charge' => $requestData['gst_charge'] ?? 0,
            'refund_amount' => $requestData['refund_amount'] ?? 0,
            'no_of_pax' => $requestData['no_of_pax'] ?? null,
            'pax_charge' => $requestData['pax_charge'] ?? null,
            'transaction_number' => $transactionNumber ,

        ]);

        $cancellation->save();

        $bookingCommission = BookingCommission::where('booking_id', $booking->id)->first();

        if ($bookingCommission) {
            $baseAmount = $bookingCommission->base_price;
            $adjustedBaseAmount = $baseAmount - $requestData['refund_amount'];
            $commissionAmount = ($adjustedBaseAmount * $bookingCommission->commission) / 100;
            $groupCommissionAmount = ($adjustedBaseAmount * $bookingCommission->group_commission) / 100;
            $totalCommissionAmount = $commissionAmount + $groupCommissionAmount;
            $bookingCommission->update([
                'base_price' => $adjustedBaseAmount,
                'commission_amount' => $totalCommissionAmount
            ]);
        }


        $passengerIds = is_array($requestData['passenger_id']) ? $requestData['passenger_id'] : explode(',', $requestData['passenger_id']);

        $bookingPassengers = BookingPassenger::whereIn('id', $passengerIds)->get();

        foreach ($bookingPassengers as $bookingPassenger) {
            $bookingPassenger->status = 0;
            $bookingPassenger->save();
        }

        $bookingRoomIds = $bookingPassengers->pluck('booking_room_id')->unique()->toArray();

        foreach ($bookingRoomIds as $bookingRoomId) {
            $bookingRoom = BookingRoom::find($bookingRoomId);
            if ($bookingRoom) {
                // Calculate the count of adult passengers
                $adultPassengerCount = $bookingPassengers->where('booking_room_id', $bookingRoomId)
                    ->where('is_adult', 1)
                    ->count();

                // Calculate the count of child passengers
                $childPassengerCount = $bookingPassengers->where('booking_room_id', $bookingRoomId)
                    ->where('is_adult', 0)
                    ->count();

                // Update adults and children counts
                $bookingRoom->adults -= $adultPassengerCount;
                $bookingRoom->children -= $childPassengerCount;
                $bookingRoom->status = 2;
                $bookingRoom->save();
            }
        }
        $packageId = $booking->package_id;
        $package = Package::find($packageId);

        if ($requestData['cancellation_type'] == 'partial') {
            $booking->booking_status = 5; // Partially cancelled
            // Helper::sendNotification( $user->id, "Your booking is partially cancelled. Booking ID is #".$booking->id);
            Helper::sendNotification($package->user_id, "#" . $booking->booking_number . " Partially cancelled");
        } else {
            $booking->booking_status = 4; // Fully cancelled
            $booking->is_cancelled = 1;
            // Helper::sendNotification( $user->id, "Your booking is cancelled. Booking ID is #".$booking->id);
            Helper::sendNotification($package->user_id, "Customer Cancelled a booking . Booking ID is #" . $booking->booking_number);

             //SMS ,Booking Cancellation without commission - Agent

             try {
                $agentID = AgentDetails::where('agent_code', $booking->agent_code)->value('user_id');
                $agentUser = User::find($agentID);
                $totalPayments = $booking->payments()->sum('paid_amount');
                if ($agentUser) {
                    $smsMessage = "We regret to inform you that the booking {$booking->booking_number} associated with your Agent Code {$booking->agent_code} has been cancelled for Amount {$totalPayments}. If you have any questions, please contact us. -From WishMyTour";
                    $smsSent = Helper::sendSMS($agentUser->mobile, $smsMessage, '1707172179833201460');
    
                    if ($smsSent) {
                        // Log successful SMS send
                        \Log::info("SMS sent successfully to {$agentUser->mobile} for booking payment ID {$booking->id}");
                    } else {
                        // Log failure to send SMS
                        \Log::error("Failed to send SMS to {$agentUser->mobile} for booking payment ID {$booking->id}");
                    }

                    //********************MAIL***************************/

                    $data = [
                        'booking_number' => $booking->booking_number,
                        'agent_code' => $booking->agent_code,
                        'totalPayments' => $totalPayments,
                       
                    ];
    
                    try {
                        $mailSent = Helper::sendMail($agentUser->email, $data, 'mail.bookingCancelAgent', 'Booking Cancellation');
                        if ($mailSent) {
                            \Log::info("Mail sent successfully to {$agentUser->email} for booking ID {$booking->id}");
                        } else {
                            \Log::error("Failed to send mail to {$agentUser->email} for booking ID {$booking->id}");
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error while sending mail to {$agentUser->email} for booking ID {$booking->id}: " . $e->getMessage());
                    }


                } else {
                    \Log::warning("Agent with code {$booking->agent_code} not found for booking ID {$booking->id}");
                }
            } catch (\Exception $e) {
                \Log::error("Error while sending SMS for booking payment ID {$booking->id}: " . $e->getMessage());
            }

            // SMS to Customer
            try {
                $smsMessage = "Booking for {$package->name} ID: {$booking->booking_number} on {$booking->created_at->format('d-m-Y')} at has been successfully cancelled. If you need further assistance or wish to reschedule, please contact us- From Wishmytour";
               
                $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172010312552713');

                if ($smsSent) {
                    \Log::info("SMS sent successfully to customer {$user->mobile} for booking ID {$booking->id}");
                } else {
                    \Log::error("Failed to send SMS to customer {$user->mobile} for booking ID {$booking->id}");
                }
            } catch (\Exception $e) {
                \Log::error("Error while sending SMS to customer {$user->mobile} for booking ID {$booking->id}: " . $e->getMessage());
            }
             //********************MAIL to Customer***************************/

             $data = [
                'booking_number' => $booking->booking_number,
                'package_name' => $package->name,
                'booking_date' => $booking->created_at->format('d-m-Y'),
               
            ];

            try {
                $mailSent = Helper::sendMail($user->email, $data, 'mail.bookingCancelCustomer', 'Booking Cancellation');
                if ($mailSent) {
                    \Log::info("Mail sent successfully to {$user->email} for booking ID {$booking->id}");
                } else {
                    \Log::error("Failed to send mail to {$user->email} for booking ID {$booking->id}");
                }
            } catch (\Exception $e) {
                \Log::error("Error while sending mail to {$user->email} for booking ID {$booking->id}: " . $e->getMessage());
            }


        }

        if ($requestData['refund_amount'] > 0.00) {
        // SMS Refund to Customer 
        try {
            $smsMessage = "Hi {Customer}, refund of {$requestData['refund_amount']} for booking ID {$booking->booking_number} has been processed. The amount will be credited to your original payment method within 7-10days -From Wishmytour";
           
            $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172018232010974');

            if ($smsSent) {
                \Log::info("SMS sent successfully to customer {$user->mobile} for booking ID {$booking->id}");
            } else {
                \Log::error("Failed to send SMS to customer {$user->mobile} for booking ID {$booking->id}");
            }
        } catch (\Exception $e) {
            \Log::error("Error while sending SMS to customer {$user->mobile} for booking ID {$booking->id}: " . $e->getMessage());
        }
         //********************MAIL Refund to Customer***************************/

         $data = [
            'booking_number' => $booking->booking_number,
            'refund_amount' => $requestData['refund_amount'],
             ];

        try {
            $mailSent = Helper::sendMail($user->email, $data, 'mail.bookingRefundCustomer', 'Booking Cancellation');
            if ($mailSent) {
                \Log::info("Mail sent successfully to {$user->email} for booking ID {$booking->id}");
            } else {
                \Log::error("Failed to send mail to {$user->email} for booking ID {$booking->id}");
            }
        } catch (\Exception $e) {
            \Log::error("Error while sending mail to {$user->email} for booking ID {$booking->id}: " . $e->getMessage());
        }
    }

        $booking->save();


        return ['res' => true, 'msg' => 'Booking cancelled successfully', 'data' => $cancellation];
    }


    public function confirmBooking(array $requestData, $user)
    {
        $booking = Booking::find($requestData['booking_id']);
        if (!$booking) {
            return ['res' => false, 'msg' => 'Booking not found', 'status' => 404];
        }

        // Update the booking status to confirmed
        $booking->booking_status = 2; // Confirmed Booking
        $booking->confirmed_date = now();
        $booking->save();
        Helper::sendNotification($booking->customer_id, "Your booking is confirmed.Booking id is #" . $requestData['booking_id']);
        return ['res' => true, 'msg' => 'Booking confirmed successfully'];
    }

    public function cancelApprove(array $requestData, $user)
    {
        $booking = Booking::find($requestData['booking_id']);
        if (!$booking) {
            return ['res' => false, 'msg' => 'Booking not found', 'status' => 404];
        }

        // Fetch the latest booking cancellation record
        $bookingCancel = BookingCancellation::where('id', $requestData['cancel_id'])
            ->first();

        if (!$bookingCancel) {
            return ['res' => false, 'msg' => 'Cancel Booking list not found', 'status' => 404];
        }

        if ($booking->booking_status == 4 || $booking->booking_status == 5) {


            $bookingCancel->status = 1;
            $bookingCancel->save();
            Helper::sendNotification($booking->customer_id, "Your Cancellation is approved. Booking ID is #" . $booking->booking_number);
            return ['res' => true, 'msg' => 'Booking cancellation approved successfully', 'data' => $booking];
        } else {
            return ['res' => false, 'msg' => 'Invalid booking status for cancellation approval', 'status' => 400];
        }
    }


    public function getCanceledBookingsForUser(int $userId)
    {
        return Booking::where('bookings.customer_id', $userId)
            ->where('bookings.booking_status', 4)
            ->leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->leftJoin('booking_cancellations', 'bookings.id', '=', 'booking_cancellations.booking_id')
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.*',
                'vendors.name as vendor_fullname',
                'booking_cancellations.created_at as cancellations_date',
                DB::raw('MAX(booking_cancellations.transaction_number) as transaction_number'), // Aggregate function
                DB::raw('(SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date ASC LIMIT 1) as first_booking_date'),
                DB::raw('(SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date DESC LIMIT 1) as last_booking_date')
            )
            ->addSelect(DB::raw('(SELECT path FROM package_images WHERE package_id = packages.id ORDER BY id ASC LIMIT 1) as first_gallery_image'))
            ->groupBy(
                'bookings.id',
                'bookings.package_id',
                'bookings.customer_id',
                'bookings.booking_status',
                'packages.id',
                'packages.user_id',
                'vendors.name',
                'booking_cancellations.created_at' // Group by cancellations_date
            )
            ->orderBy('cancellations_date', 'desc') // Order by cancellations_date
            ->get();
    }

    public function updateCompletedBookingsStatus()
    {
        $currentDate = now()->toDateString();
        $bookings = Booking::where(function ($query) use ($currentDate) {
            $query->whereRaw('date((SELECT booking_date FROM booking_dates WHERE booking_id = bookings.id ORDER BY booking_date ASC LIMIT 1)) < ?', [$currentDate]);
        })
            ->where('booking_status', 2) // Confirmed = 2
            ->whereHas('payments', function ($query) {
                $query->where('payment_type', 'final');
            })
            ->get();
        foreach ($bookings as $booking) {
            $booking->booking_status = 3;
            $booking->complete_date = now();
            $booking->save();

            if (!empty($booking->agent_code)) {
                $agentDetails = AgentDetails::where('agent_code', $booking->agent_code)->first();
                if ($agentDetails) {
                    // Check if a commission record already exists for this booking
                    $existingCommission = BookingCommission::where('booking_id', $booking->id)->exists();
                    if (!$existingCommission) {
                        // Fetch the commission group based on the total amount of bookings in the last 30 days
                        $commissionGroup = Booking::fetchCommissionGroup($booking->agent_code);

                        // Get the commission percentage from the commission group
                        $groupCommissionRate = $commissionGroup ? $commissionGroup->commission : 0;

                        // Get the commission percentage from configuration
                        $commissionRate = Helper::getConfig('agent_commission')->value ?? 0;

                        // Calculate the commission amount
                        $baseAmount = $booking->base_amount + $booking->addon_total_price;
                        $commissionAmount = ($baseAmount * $commissionRate) / 100;
                        $groupCommissionAmount = ($baseAmount * $groupCommissionRate) / 100;
                        $totalCommissionAmount = $commissionAmount + $groupCommissionAmount;

                        // Create the booking commission
                        $bookingCommission = BookingCommission::create([
                            'user_id' => $agentDetails->id,
                            'booking_id' => $booking->id,
                            'base_price' => $baseAmount,
                            'commission' => $commissionRate,
                            'group_commission' => $groupCommissionRate,
                            'commission_amount' => $totalCommissionAmount,
                        ]);

                        //SMS Tour Completion with Commission with Booking ID
                        try {
                            $user = User::find($agentDetails->user_id);
    
                            if ($user) {
                                $smsMessage = "Tour {$booking->booking_number} has been completed using your Agent Code: {$booking->agent_code}. Your commission for this tour {$totalCommissionAmount}. Thank you for being a valuable part of our team! -From WishMyTour";
                                $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172179653370509');
    
                                if ($smsSent) {
                                    // Log successful SMS send
                                    \Log::info("SMS sent successfully to {$user->mobile} for completed booking ID {$booking->id}");
                                } else {
                                    // Log failure to send SMS
                                    \Log::error("Failed to send SMS to {$user->mobile} for completed booking ID {$booking->id}");
                                }

                                      // ****************************MAIL**************  //
                                $data = [
                                    'agentCode' => $booking->agent_code,
                                    'booking_number' => $booking->booking_number,
                                    'totalCommissionAmount' => $totalCommissionAmount,
                                  
                                ];
                
                                try {
                                    $mailSent = Helper::sendMail($user->email, $data, 'mail.tourCompletionAgent', 'New Booking');
                                    if ($mailSent) {
                                        \Log::info("Mail sent successfully to {$user->email} for booking ID {$booking->id}");
                                    } else {
                                        \Log::error("Failed to send mail to {$user->email} for booking ID {$booking->id}");
                                    }
                                } catch (\Exception $e) {
                                    \Log::error("Error while sending mail to {$user->email} for booking ID {$booking->id}: " . $e->getMessage());
                                }





                            } else {
                                \Log::warning("Agent with code {$booking->agent_code} not found for booking ID {$booking->id}");
                            }
                        } catch (\Exception $e) {
                            \Log::error("Error while sending SMS for completed booking ID {$booking->id}: " . $e->getMessage());
                        }


                        // Convert to array if needed
                        $commissionDetails = $bookingCommission->toArray();
                    }
                }
            }
        }
        return $bookings;
    }

    /**
     * Add feedback.
     *
     * @param array $requestData
     * @return array
     */
    public function addFeedback(array $requestdata, int $userId)
    {
        $booking = Booking::where('id', $requestdata['booking_id'])
            ->where('customer_id', $userId)
            ->first();

        if (!$booking) {
            return ['res' => false, 'msg' => 'Booking not found', 'status' => 404];
        }
        if ($booking->booking_status == 3) {
            $booking->feedback = $requestdata['feedback'] ?? null;
            $booking->rating = $requestdata['rating'] ?? null;
            $booking->save();
        }
        return ['res' => true, 'msg' => 'Feedback and rating added successfully', 'data' => $booking];
    }

    public function addReport($request, int $userId)
    {
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'booking_id' => 'required|exists:bookings,id',
        'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Example validation rules for images
    ]);

    if ($validator->fails()) {
        return ['res' => false, 'msg' => 'Validation failed', 'errors' => $validator->errors()->all()];
    }

    // Retrieve the booking based on the provided ID and user ID
    $booking = Booking::where('id', $request->booking_id)
        ->where('customer_id', $userId)
        ->first();

    if (!$booking) {
        return ['res' => false, 'msg' => 'Booking not found', 'status' => 404];
    }

    // Update the report field of the booking if it's in the correct status
    if ($booking->booking_status == 3) {
        $booking->report = $request->report ?? null;
        $booking->booking_status = 7;
        $booking->save();
    } else {
        return ['res' => false, 'msg' => 'Booking status not valid for adding a report'];
    }

    $reportGallery = [];
    if ($request->hasFile('gallery_images')) {
        foreach ($request->file('gallery_images') as $image) {
            $filename = uniqid() . '.' . $image->getClientOriginalExtension();
            $path = 'report_gallery_images/' . $booking->id . '/' . $filename;

            // Move the file to the storage path
            $storagePath = storage_path('app/public/' . dirname($path));
            if (!File::exists($storagePath)) {
                File::makeDirectory($storagePath, 0755, true, true);
            }

            if ($image->move($storagePath, $filename)) {
                // Save information to the database
                $reportGallery[] = [
                    'booking_id' => $booking->id,
                    'path' => $path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                // Failed to move the file
                Log::error('Failed to move file: ' . $image->getClientOriginalName());
            }
        }
        // dd($reportGallery); // Debug output
        // Bulk insert the gallery images
        BookingReportGallery::insert($reportGallery);
    }

    return [
        'res' => true,
        'msg' => 'Report added successfully',
        'data' => $booking,
        'reportGallery' => $reportGallery
    ];
   }
//*********************************ON REQUEST******************************* */

    /**
     * Add a new On request booking.
     *
     * @param array $requestData
     * @return array
     */


    public function sendBookingOnRequest(array $requestData): array
    {
        try {
            // Generate a random 8-character token
            $token = Str::random(8);

            // Create a new BookingOnRequest record
            $bookingOnRequest = BookingOnRequest::create([
                'package_id' => $requestData['package_id'],
                'customer_id' => auth()->id(),
                'token' => $token,
                'room_details' => $requestData['rooms'],
                'status' => $requestData['status'], // Assuming status is provided and 'In process' -> 0
                'add_on_id' => $requestData['add_on_id'] ?? null,
                'start_date' => $requestData['start_date'] ?? null,
                'end_date' => $requestData['end_date'] ?? null,
                'base_amount' => $requestData['base_amount'] ?? null,
                'price' => $requestData['price'] ?? null,
                'addon_total_price' => $requestData['addon_total_price'] ?? null,
                'gst_percent' => $requestData['gst_percent'] ?? null,
                'gst_price' => $requestData['gst_price'] ?? null,
                'tcs' => $requestData['tcs'] ?? null,
                'final_price' => $requestData['final_price'] ?? null,
            ]);

            // $bookingRooms = [];
            // foreach ($requestData['rooms'] as $room) {
            //     $bookingRooms[] = [
            //         'room_no' => $room['room_no'] ?? null,
            //         'adults' => $room['adults'] ?? null,
            //         'children' => $room['children'] ?? null,
            //     ];
            // }
            $vendorId = DB::table('users')
                ->join('packages', 'packages.user_id', '=', 'users.id')
                ->where('packages.id', $requestData['package_id'])
                ->value('users.id');
            Helper::sendNotification(auth()->id(), "New Tour Added, here is your booking Id :" . $bookingOnRequest->id);
            Helper::sendNotification($vendorId, "On Request Booking, here is your booking Id :" . $bookingOnRequest->id);
            return [
                'res' => true,
                'msg' => 'Booking on request added successfully',
                'data' => [
                    'booking' => $bookingOnRequest,
                    // 'rooms' => $bookingRooms,
                ],
            ];
        } catch (\Exception $e) {
            return ['res' => false, 'msg' => $e->getMessage()];
        }
    }

    public function bookingOnRequestList(int $userId)
    {
        $bookings = BookingOnRequest::leftJoin('packages', 'booking_on_requests.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'booking_on_requests.id')
            ->leftJoin('cities', 'packages.origin_city_id', '=', 'cities.id')
            ->leftJoin('customer_details', 'customer_details.user_id', '=', 'booking_on_requests.customer_id')
            ->select(
                'booking_on_requests.*',
                'packages.id as package_id',
                'packages.name as package_name',
                'cities.city as origin_city_name',
                'customer_details.first_name as cus_first_name',
                'customer_details.last_name as cus_last_name',
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(booking_on_requests.room_details, "$[*].room_no")) as room_numbers'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(booking_on_requests.room_details, "$[*].adults")) as adults'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(booking_on_requests.room_details, "$[*].children")) as children')
            )
            ->where('booking_on_requests.customer_id', $userId)
            ->groupBy(
                'booking_on_requests.id',
                'packages.id',
                'packages.name',
                'cities.city',
                'customer_details.first_name',
                'customer_details.last_name'
            )
            ->orderBy('booking_on_requests.created_at', 'desc')
            ->get();

        // Process each booking to calculate the total number of pax
        foreach ($bookings as $booking) {
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
        }

        return $bookings;
    }


    public function __getOnrequestBooking(int $userId, array $filters)
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date'])->toDateString() : null;
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date'])->toDateString() : null;

        $query = Booking::leftJoin('packages', 'booking_on_requests.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('cities', 'packages.origin_city_id', '=', 'cities.id')
            ->select(

                'booking_on_requests.*',
                'packages.id as package_id',
                'packages.name as package_name',

                'cities.city as origin_city_name',
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as booking_cost'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax'),
                DB::raw('DATEDIFF((SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id), (SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id)) + 1 as duration_days'),
                DB::raw('DATEDIFF((SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id), (SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id)) as duration_nights')
            )
            ->where('packages.user_id', $userId)
            ->groupBy('bookings.id', 'booking_customer_details.id')
            ->orderBy('bookings.created_at', 'desc');

        if ($startDate && $endDate) {
            $query->whereDate('bookings.created_at', '>=', $startDate)
                ->whereDate('bookings.created_at', '<=', $endDate);
        }

        // Additional filters
        if (isset($filters['booking_id'])) {
            $query->where('bookings.id', $filters['booking_id']);
        }

        if (isset($filters['origin_city_id'])) {
            $query->where('packages.origin_city_id', $filters['origin_city_id']);
        }

        if (isset($filters['booking_status'])) {
            $query->where('bookings.booking_status', $filters['booking_status']);
        }

        if (isset($filters['flag_status'])) {
            $flagStatus = is_array($filters['flag_status']) ? $filters['flag_status'] : explode(',', $filters['flag_status']);
            $query->whereIn('bookings.booking_status', $flagStatus);
        }

        return $query->get()->map(function ($booking) {
            $booking->duration = ($booking->duration_nights) . ' N & ' . ($booking->duration_days) . ' D';
            return $booking;
        });
    }

    public function getOnrequestBooking($userId)
    {
        // Build the query
        $query = BookingOnRequest::leftJoin('packages', 'booking_on_requests.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'booking_on_requests.id') // Assuming booking_on_requests.id is the correct reference
            ->leftJoin('cities', 'packages.origin_city_id', '=', 'cities.id')
            ->leftJoin('customer_details', 'customer_details.user_id', '=', 'booking_on_requests.customer_id') // Fixed join condition
            ->select(
                'booking_on_requests.*',
                'packages.id as package_id',
                'packages.name as package_name',
                'cities.city as origin_city_name',
                'customer_details.first_name as cus_first_name',
                'customer_details.last_name as cus_last_name',
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(booking_on_requests.room_details, "$[*].room_no")) as room_numbers'), // Extracting room numbers from room_details
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(booking_on_requests.room_details, "$[*].adults")) as adults'), // Extracting adults count
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(booking_on_requests.room_details, "$[*].children")) as children') // Extracting children count
            )
            ->where('packages.user_id', $userId)
            ->groupBy(
                'booking_on_requests.id',
                'packages.id',
                'packages.name',
                'cities.city',
                'customer_details.first_name',
                'customer_details.last_name'
            )
            ->orderBy('booking_on_requests.created_at', 'desc');

        // Get the results
        return $query->get();
    }

    public function agentBookingList(string $agentCode, array $filters)
    {
        $query = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            ->leftJoin('booking_commissions', 'bookings.id', '=', 'booking_commissions.booking_id') // Left join for commission details
            ->select(
                'bookings.id as booking_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.name as package_name',
                DB::raw("CONCAT(customer_details.first_name, ' ', customer_details.last_name) as name"),
                'vendors.name as vendor_fullname',
                'booking_commissions.commission_amount as commission', // Include commission details
                'booking_commissions.group_commission as group_percentage', // Include group_percentage
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as booking_cost'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
            )
            ->where('bookings.agent_code', $agentCode)
            ->groupBy('bookings.id', 'customer_details.id', 'vendors.name', 'booking_commissions.commission_amount', 'booking_commissions.group_commission')
            ->orderBy('bookings.created_at', 'desc')
            ->with('payments');

        // Apply filters
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date'])->toDateString() : null;
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date'])->toDateString() : null;

        if ($startDate && $endDate) {
            $query->havingRaw('created_at >= ?', [$startDate])
                ->havingRaw('created_at <= ?', [$endDate]);
        }

        if (isset($filters['booking_id'])) {
            $query->where('bookings.id', $filters['booking_id']);
        }

        // if (isset($filters['phone_number'])) {
        //     $query->where('customer_details.phone_number', $filters['phone_number']);
        // }

        if (isset($filters['name'])) {
            $query->where(DB::raw("CONCAT(customer_details.first_name, ' ', customer_details.last_name)"), 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['origin_city_id'])) {
            $query->where('packages.origin_city_id', $filters['origin_city_id']);
        }

        if (isset($filters['booking_status'])) {
            $query->where('bookings.booking_status', $filters['booking_status']);
        }

        if (isset($filters['flag_status'])) {
            $flagStatus = is_array($filters['flag_status']) ? $filters['flag_status'] : explode(',', $filters['flag_status']);
            $query->whereIn('bookings.booking_status', $flagStatus);
        }

        return $query->get();
    }

    public function agentCommissionList(string $agentCode, array $filters)
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date'])->toDateString() : null;
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date'])->toDateString() : null;
        $paymentStatus = isset($filters['payment_status']) ? $filters['payment_status'] : null;
        $bookingStatus = isset($filters['booking_status']) ? $filters['booking_status'] : null;
        $name = isset($filters['name']) ? $filters['name'] : null;
        $bookingId = isset($filters['booking_id']) ? $filters['booking_id'] : null;

        $query = BookingCommission::leftJoin('bookings', 'bookings.id', '=', 'booking_commissions.booking_id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->select(
                'bookings.id as booking_id',
                'bookings.booking_number as booking_number',
                'bookings.booking_status as booking_status',
                'booking_commissions.base_price',
                'booking_commissions.commission',
                'booking_commissions.group_commission',
                'booking_commissions.commission_amount',
                'booking_commissions.payment_status',
                'booking_commissions.claim_status',
                'booking_commissions.invoice_number',
                'booking_commissions.voucher_number',
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as booking_cost'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax'),
                // Conditional commission date
                DB::raw('
                CASE
                    WHEN booking_commissions.payment_status = 0 THEN booking_commissions.created_at
                    WHEN booking_commissions.payment_status = 1 THEN booking_commissions.claimed_date
                END as commission_date
            '),
                // Add paid_amount, cancelled_amount, net_amount and payable_amount calculation
                DB::raw('
                CASE
                    WHEN booking_commissions.payment_status = 0 THEN booking_commissions.base_price
                    ELSE 0
                END as paid_amount
            '),
                DB::raw('0 as cancelled_amount'),
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as net_amount'),
            // DB::raw('((SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) - 0) as payable_amount')
            )
            ->where('bookings.agent_code', $agentCode)
            ->groupBy(
                'bookings.id',
                'booking_commissions.base_price',
                'booking_commissions.commission',
                'booking_commissions.group_commission',
                'booking_commissions.commission_amount',
                'booking_commissions.payment_status',
                'booking_commissions.claim_status',
                'booking_commissions.invoice_number',
                'booking_commissions.voucher_number',
                'commission_date',
                'paid_amount',
                'cancelled_amount',
                'net_amount',
            // 'payable_amount'
            )
            ->orderByRaw('MAX(booking_commissions.created_at) desc');

        if ($startDate && $endDate) {
            $query->whereDate('bookings.created_at', '>=', $startDate)
                ->whereDate('bookings.created_at', '<=', $endDate);
        }

        if ($paymentStatus) {
            $query->where('booking_commissions.payment_status', $paymentStatus);
        }

        if ($bookingStatus) {
            $query->where('bookings.booking_status', $bookingStatus);
        }

        if ($bookingId) {
            $query->where('bookings.id', $bookingId);
        }

        $result = $query->get();

        // Post-process results to ensure correct calculations


        return $result;
    }

    public function agentPaymentHistory(int $userId)
    {
        // Get the agent ID from the AgentDetails table
        $agentID = AgentDetails::where('user_id', $userId)->pluck('id')->first();

        // Determine the first day of the current month
        $firstDayOfCurrentMonth = Carbon::now()->startOfMonth()->toDateString();

        // Fetch booking commissions for the agent, summing up the commission_amount and base_price, grouped by month
        $commissions = BookingCommission::where('user_id', $agentID)
            // ->where('payment_date', '<', $firstDayOfCurrentMonth) // Exclude current month's data
            ->selectRaw('
            DATE_FORMAT(payment_date, "%Y-%m") as month,
            MIN(payment_date) as date,
            MIN(invoice_number) as invoice_number,
            SUM(commission_amount) as total_commission,
            SUM(base_price) as total_base_price,
            MIN(voucher_number) as voucher_number
            
        ')
        ->whereNotNull('voucher_number')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        // Fetch commission thresholds from the commission_groups table
        $commissionGroups = CommissionGroup::orderBy('amount_threshold', 'desc')->get();

        // Format the results
        $formattedCommissions = $commissions->map(function ($commission) use ($commissionGroups, $agentID) {
            $incentive = 0;
            $incentiveRate = 0;

            // Determine the incentive based on total_base_price and thresholds
            foreach ($commissionGroups as $group) {
                if ($commission->total_base_price >= $group->amount_threshold) {
                    $incentiveRate = $group->commission;
                    $incentive = ($commission->total_base_price * $incentiveRate) / 100;
                    break;
                }
            }

            // Calculate Total Payable
            $totalPayable = $commission->total_commission + $incentive;

            // Calculate TDS (1% of Total Payable)
            $tds = $totalPayable * 0.01;

            // Calculate Processed Amount
            $processedAmount = $totalPayable - $tds;

            // Format the month to "June 2024"
            $monthName = date('F', strtotime($commission->month . '-01'));
            $year = date('Y', strtotime($commission->month . '-01'));

            
            $voucher_number = $commission->voucher_number;
            $vpdfUrl = url('storage/app/public/vouchers/' . $voucher_number.'.pdf');
            $invoice_number = $commission->invoice_number;
            $pdfUrl = url('storage/app/public/invoiceAgent/' . $invoice_number.'.pdf');
            return [
                'month' => $monthName,
                'year' => $year,
                'date' => $commission->date,
                'invoice_number' => $invoice_number,
                'total_commission' => number_format($commission->total_commission, 2, '.', ''),
                'total_base_price' => number_format($commission->total_base_price, 2, '.', ''),
                'incentive' => number_format($incentive, 2, '.', ''),
                'incentive_rate' => $incentiveRate,
                'total_payable' => number_format($totalPayable, 2, '.', ''),
                'tds' => number_format($tds, 2, '.', ''),
                'voucher_number' =>$voucher_number,
                'processed_amount' => number_format($processedAmount, 2, '.', ''),
                'pdfFilePath' => $pdfUrl,
                'voucherUrl' => $vpdfUrl,
            ];
        });

        return $formattedCommissions;
    }

    public function paymentHistory(int $userId, $startDate = null, $endDate = null)
    {
        // Fetch payments and sort by booking_payments.created_at
        $payments = BookingPayment::where('booking_payments.user_id', $userId)
            ->join('bookings', 'booking_payments.booking_id', '=', 'bookings.id')
            ->select('booking_payments.*', 'bookings.booking_number', 'bookings.package_id')
            ->orderBy('booking_payments.created_at', 'desc')
            ->get();

        // Fetch cancellations and sort by booking_cancellations.created_at
        // $cancellations = BookingCancellation::where('booking_cancellations.customer_id', $userId)
        //     ->join('bookings', 'booking_cancellations.booking_id', '=', 'bookings.id')
        //     ->select('booking_cancellations.*', 'bookings.booking_number', 'booking_cancellations.created_at as payment_date', 'bookings.package_id')
        //     ->orderBy('booking_cancellations.created_at', 'desc')
        //     ->get();

        // Merge payments and cancellations into one collection of objects
        //$sortedPayments = $payments->merge($cancellations);

        // Sort merged collection by datetime (created_at) in descending order
        $sortedPayments = $payments->sortByDesc(function ($payment) {
            return strtotime($payment->created_at);
        })->values()->all();

        // Retrieve customer details
        $customer = DB::table('customer_details')
            ->join('users', 'customer_details.user_id', '=', 'users.id')
            ->select('customer_details.first_name', 'customer_details.last_name', 'customer_details.address', 'users.mobile')
            ->where('customer_details.user_id', $userId)
            ->first();

        // Generate PDFs for each payment and store the URL in the result
        foreach ($sortedPayments as $payment) {
            // Retrieve vendor details for each booking
            $vendor = DB::table('packages')
                ->join('vendors', 'packages.user_id', '=', 'vendors.user_id')
                ->join('users', 'vendors.user_id', '=', 'users.id')
                ->select('vendors.organization_name', 'vendors.pan_number', 'vendors.vendor_code', 'users.mobile as vendor_mobile', 'vendors.address')
                ->where('packages.id', $payment->package_id)
                ->first();

            $pdfUrl = $this->generatePaymentInvoicePDF($payment, $customer, $vendor);
            $payment->pdfFilePath = $pdfUrl;
        }

        return $sortedPayments;
    }

   

    private function generatePaymentInvoicePDF($payment, $customer, $vendor)
    {
        // Initialize Dompdf
        $dompdf = new Dompdf();
        $options = new Options();
        // Configure Dompdf options as needed

        // Load the view and render it to HTML
        $html = view('invoice-paymentpdf', compact('payment', 'customer', 'vendor'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Generate a unique filename using payment ID
        $pdfFileName = 'invoice_payment_' . $payment->id . '.pdf';

        // Construct the file path relative to the public disk's invoicePayment directory
        $pdfFilePath = 'public/invoicePayment/' . $pdfFileName;

        // Manually create the directory if it doesn't exist
        File::makeDirectory(storage_path('app/public/invoicePayment'), 0777, true, true);

        // Get PDF content
        $pdfContent = $dompdf->output();

        // Store the PDF content in the specified storage path
        Storage::put($pdfFilePath, $pdfContent);

        // Generate the URL for accessing the PDF file

        $pdfUrl = url('storage/app/public/invoicePayment/' . $pdfFileName);
        return $pdfUrl;
    }
    public function refundHistory(int $userId, $startDate = null, $endDate = null)
    {
     // Fetch cancellations and sort by booking_cancellations.created_at
     $cancellations = BookingCancellation::where('booking_cancellations.customer_id', $userId)
         ->join('bookings', 'booking_cancellations.booking_id', '=', 'bookings.id')
         ->select('booking_cancellations.*', 'bookings.booking_number',  'bookings.created_at as booking_date','booking_cancellations.created_at as payment_date', 'bookings.package_id')
         ->orderBy('booking_cancellations.created_at', 'desc')
         ->get();
 
     // Retrieve customer details
     $customer = DB::table('customer_details')
         ->join('users', 'customer_details.user_id', '=', 'users.id')
         ->select('customer_details.first_name', 'customer_details.last_name', 'customer_details.address', 'users.mobile')
         ->where('customer_details.user_id', $userId)
         ->first();
 
     // Generate PDFs for each cancellation and store the URL in the result
     foreach ($cancellations as $cancellation) {
         // Retrieve vendor details for each booking
         $vendor = DB::table('packages')
             ->join('vendors', 'packages.user_id', '=', 'vendors.user_id')
             ->join('users', 'vendors.user_id', '=', 'users.id')
             ->select('vendors.organization_name', 'vendors.pan_number', 'vendors.vendor_code', 'users.mobile as vendor_mobile', 'vendors.address')
             ->where('packages.id', $cancellation->package_id)
             ->first();
 
         $pdfUrl = $this->generateCreditNote($cancellation, $customer, $vendor);
         $cancellation->pdfFilePath = $pdfUrl;
     }
 
     return $cancellations;
    }

    private function generateCreditNote($cancellation, $customer, $vendor)
    {
        // Initialize Dompdf
        $dompdf = new Dompdf();
        $options = new Options();
        // Configure Dompdf options as needed

        // Load the view and render it to HTML
        $html = view('credit-note', compact('cancellation', 'customer', 'vendor'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Generate a unique filename using payment ID
        $pdfFileName = $cancellation->transaction_number . '.pdf';

        // Construct the file path relative to the public disk's invoicePayment directory
        $pdfFilePath = 'public/creditNote/' . $pdfFileName;

        // Manually create the directory if it doesn't exist
        File::makeDirectory(storage_path('app/public/creditNote'), 0777, true, true);

        // Get PDF content
        $pdfContent = $dompdf->output();

        // Store the PDF content in the specified storage path
        Storage::put($pdfFilePath, $pdfContent);

        // Generate the URL for accessing the PDF file

        $pdfUrl = url('storage/app/public/creditNote/' . $pdfFileName);
        return $pdfUrl;
    }

    public function commissionInvoice()//cron
    {
    $firstDayOfCurrentMonth = Carbon::now()->startOfMonth()->toDateString();
    $lastDayOfCurrentMonth = Carbon::now()->endOfMonth()->toDateString();
    $currentMonth = Carbon::now()->format('m'); // Current month in two digits
    $currentYear = Carbon::now()->format('y'); // Current year in two digits
    $commissionGroups = CommissionGroup::orderBy('amount_threshold', 'desc')->get();

    $agents = AgentDetails::select('agent_details.*', 'users.mobile')
        ->join('users', 'users.id', '=', 'agent_details.user_id')
        ->get();

    $result = [];

    foreach ($agents as $agent) {

        $commissions = BookingCommission::where('user_id', $agent->id)
            ->where('payment_status', 0)
          // ->whereBetween('created_at', [$firstDayOfCurrentMonth, $lastDayOfCurrentMonth])
         // ->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$firstDayOfCurrentMonth, $lastDayOfCurrentMonth])
            ->selectRaw('
            DATE_FORMAT(created_at, "%Y-%m") as month,
            SUM(commission_amount) as total_commission,
            SUM((base_price * commission) / 100) as commission,
            SUM((base_price * group_commission) / 100) as group_commission,
            SUM(base_price) as total_base_price
        ')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        $formattedCommissions = $commissions->map(function ($commission) use ($commissionGroups, $agent, $currentMonth, $currentYear) {
            $incentive = 0;
            $incentiveRate = 0;

            foreach ($commissionGroups as $group) {
                if ($commission->total_base_price >= $group->amount_threshold) {
                    $incentiveRate = $group->commission;
                    $incentive = ($commission->total_base_price * $incentiveRate) / 100;
                    break;
                }
            }

            $totalPayable = $commission->total_commission + $incentive;
            $tds = $totalPayable * 0.01;
            $processedAmount = $totalPayable - $tds;

            $commissionData = [
                'month' => $commission->month,
                'total_commission' => number_format($commission->total_commission, 2, '.', ''),
                'total_base_price' => number_format($commission->total_base_price, 2, '.', ''),
                'incentive' => number_format($commission->group_commission, 2, '.', ''),
                'commission' => number_format($commission->commission, 2, '.', ''),
                'amount_in_word' => Helper::AmountInWords(round($commission->total_commission)),
                'agentsDetails' => $agent
            ];

            $invoiceNumber = 'WMTCOM' . $currentMonth . $currentYear . $agent->id;
            $commissionData['invoice_number'] = $invoiceNumber;
            $commissions = BookingCommission::join('bookings', 'bookings.id', '=', 'booking_commissions.booking_id')
                ->leftJoin('booking_cancellations', 'booking_cancellations.booking_id', '=', 'bookings.id')
                ->select(
                    'booking_commissions.created_at',
                    'booking_commissions.booking_id',
                    'bookings.booking_number',
                    'booking_commissions.base_price as basic_amount',
                    DB::raw("(SELECT SUM(refund_amount - gst_charge) FROM booking_cancellations WHERE booking_cancellations.booking_id = bookings.id) as cancelled_amount"),
                    DB::raw("(booking_commissions.base_price * booking_commissions.commission) / 100 as commission"),
                    DB::raw("(booking_commissions.base_price * booking_commissions.group_commission) / 100 as group_commission"),
                    'booking_commissions.commission_amount as total_commission'
                )
                ->where('booking_commissions.user_id', $agent->id)
                ->where('booking_commissions.payment_status', 0)
                ->orderBy('booking_commissions.created_at', 'desc')
                ->get();
            $commissions->map(function ($commission) {
                $commission->formatted_date = Carbon::parse($commission->created_at)->format('jS F');
                $commission->net_amount = round($commission->basic_amount) - round($commission->cancelled_amount);
                return $commission;
            });

            $pdfUrl = $this->generateAgentInvoicePDF($agent, $commissionData, $commissions);
            $commissionData['pdfFilePath'] = $pdfUrl;

            // Send SMS notification to agent
            try {
            $user = User::find($agent->user_id);
            if ($user) {
            $smsMessage = "Dear {$agent->first_name}, your invoice for the period {$commissionData['month']} has been generated. Total Commission: {$commissionData['total_commission']}. Incentive: {$commissionData['incentive']}. Thank you for your efforts! -From WishMyTour";
            $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172174275699317');

            if ($smsSent) {
            // Log successful SMS send
            \Log::info("SMS sent successfully to {$user->mobile} for agent ID {$agent->id}");
            } else {
            // Log failure to send SMS
            \Log::error("Failed to send SMS to {$user->mobile} for agent ID {$agent->id}");
            }

            //*************************MAIL********************** */
            $data = [
                'first_name' => $agent->first_name,
                'period' => $commissionData['month'],
                'total_commission' => $commissionData['total_commission'],
                'incentive' => $commissionData['incentive'],
                'pdfFilePath' => $commissionData['pdfFilePath']
            ];

            try {
                $subject = 'Your Commission Invoice for ' . $commissionData['month'];
                $mailSent = Helper::sendMail($user->email, $data, 'mail.invoiceAgent', $subject);
                if ($mailSent) {
                    \Log::info("Mail sent successfully to {$user->email} ");
                } else {
                    \Log::error("Failed to send mail to {$user->email}");
                }
            } catch (\Exception $e) {
                \Log::error("Error while sending mail to {$user->email}: " . $e->getMessage());
            }



            } else {
            \Log::warning("User with ID {$agent->user_id} not found for agent ID {$agent->id}");
            }
            } catch (\Exception $e) {
            \Log::error("Error while sending SMS for agent ID {$agent->id}: " . $e->getMessage());
            }

            return $commissionData;
        });

        $commissions = BookingCommission::where('user_id', $agent->id)
            ->where('payment_status', 0)
            ->get();

        foreach ($commissions as $commission) {
            $tds_amount = round(($commission->commission_amount * 0.01), 2);

            $commission->update([
                'payment_status' => 1,
                'claim_status' => 1,
                'claimed_date' => now()->toDateString(),
                'invoice_number' => 'WMTCOM' . $currentMonth . $currentYear . $agent->id,
                'tds_amount' => $tds_amount
            ]);
        }

        if (!$formattedCommissions->isEmpty()) {
            $result[$agent->id] = $formattedCommissions;
        }
    }

    return response()->json(['res' => true, 'msg' => 'Invoices generated successfully', 'data' => $result], 200);
    }

    public function generateCommissionVouchers()
    {
        $firstDayOfCurrentMonth = Carbon::now()->startOfMonth()->toDateString();
        $lastDayOfCurrentMonth = Carbon::now()->endOfMonth()->toDateString();
        $currentMonth = Carbon::now()->format('m'); // Current month in two digits
        $currentYear = Carbon::now()->format('y'); // Current year in two digits
        $paymentDate = Carbon::now()->toDateString();

        $invoices = BookingCommission::whereNotNull('booking_commissions.invoice_number')
            ->where('booking_commissions.payment_status', 1) // processing
           // ->whereBetween('booking_commissions.created_at', [$firstDayOfCurrentMonth, $lastDayOfCurrentMonth])
           //->whereRaw('DATE(created_at) BETWEEN ? AND ?', [$firstDayOfCurrentMonth, $lastDayOfCurrentMonth])
            ->select('booking_commissions.invoice_number', 'booking_commissions.user_id')
            ->groupBy('booking_commissions.invoice_number', 'booking_commissions.user_id')
            ->get();

        foreach ($invoices as $invoice) {
            $voucher = BookingCommission::join('bookings', 'bookings.id', '=', 'booking_commissions.booking_id')
                ->leftJoin('booking_cancellations', 'booking_cancellations.booking_id', '=', 'bookings.id')
                ->select(
                    DB::raw("SUM(booking_commissions.base_price) as basic_amount"),
                    DB::raw("COALESCE(SUM(booking_cancellations.refund_amount - booking_cancellations.gst_charge), 0) as cancelled_amount"),
                    DB::raw("SUM(booking_commissions.commission_amount) as total_commission"),
                    DB::raw("SUM((booking_commissions.base_price * booking_commissions.commission) / 100) as commission"),
                    DB::raw("SUM((booking_commissions.base_price * booking_commissions.group_commission) / 100) as group_commission"),
                    DB::raw("DATE_FORMAT(booking_commissions.claimed_date, '%Y-%m') as month"),
                    'booking_commissions.claimed_date'
                )
                ->where('booking_commissions.invoice_number', $invoice->invoice_number)
                ->groupBy('booking_commissions.claimed_date')
                ->first();

            if ($voucher) {
                $voucher->net_amount = round($voucher->basic_amount) - round($voucher->cancelled_amount);
            }
            $agent = AgentDetails::select('agent_details.*', 'users.mobile')
                ->join('users', 'users.id', '=', 'agent_details.user_id')
                ->where('agent_details.id', $invoice->user_id)
                ->first();
            $tds = round($voucher->total_commission * 0.01);
            $voucher->tds = $tds;
            $voucher->net_commission = $voucher->total_commission - $tds;
            $voucher->amount_in_word = Helper::AmountInWords($voucher->net_commission);
            $voucherNumber = 'WMTVOU' . $currentMonth . $currentYear . $invoice->user_id;
            $voucher->voucher_number = $voucherNumber;
            $voucher->invoice_number = $invoice->invoice_number;
            $voucher->invoice_date = $voucher->claimed_date;
            $voucher->payment_date = $paymentDate;

            $commissions = BookingCommission::join('bookings', 'bookings.id', '=', 'booking_commissions.booking_id')
                ->leftJoin('booking_cancellations', 'booking_cancellations.booking_id', '=', 'bookings.id')
                ->select(
                    'booking_commissions.created_at',
                    'booking_commissions.booking_id',
                    'bookings.booking_number',
                    'booking_commissions.base_price as basic_amount',
                    DB::raw("(SELECT SUM(refund_amount - gst_charge) FROM booking_cancellations WHERE booking_cancellations.booking_id = bookings.id) as cancelled_amount"),
                    DB::raw("(booking_commissions.base_price * booking_commissions.commission) / 100 as commission"),
                    DB::raw("(booking_commissions.base_price * booking_commissions.group_commission) / 100 as group_commission"),
                    'booking_commissions.commission_amount as total_commission'
                )
                ->where('booking_commissions.invoice_number',$invoice->invoice_number)
                ->orderBy('booking_commissions.created_at', 'desc')
                ->get();
            $commissions->map(function ($commission) {
                $commission->formatted_date = Carbon::parse($commission->created_at)->format('jS F');
                $commission->net_amount = round($commission->basic_amount) - round($commission->cancelled_amount);
                return $commission;
            });
            $this->generatePaymentVoucher($agent, $voucher, $commissions);
            
            
            //SMS Invoice processed for Payment (Period, Total, TDS, Net Payable)

            try {
            $user = User::find($agent->user_id);
            if ($user) {
            $smsMessage = "Dear {$agent->first_name}, your payment voucher against commission and incentive (if any) payable for the period {$voucher->month} has been generated. Total: {$voucher->total_commission}, TDS: {$voucher->tds}, Net Payable: {$voucher->net_commission}. Thank you for your dedication! -From WishMyTour";

            $smsSent = Helper::sendSMS($user->mobile, $smsMessage, '1707172179719670089');

            if ($smsSent) {
            // Log successful SMS send
            \Log::info("SMS sent successfully to {$user->mobile} for agent ID {$smsMessage}");
            } else {
            // Log failure to send SMS
            \Log::error("Failed to send SMS to {$user->mobile} for agent ID {$agent->id}");
            }
                //***********************MAIL************************ */
                $data = [
                    'first_name' => $agent->first_name,
                    'month' => $voucher->month,
                    'total_commission' => $voucher->total_commission,
                    'tds' => $voucher->tds,
                    'net_commission' =>$voucher->net_commission
                ];

                try {
                    $subject = 'Your Commission Voucher for ' .$voucher->month;
                    $mailSent = Helper::sendMail($user->email, $data, 'mail.bookingVoucherAgent', $subject);
                    if ($mailSent) {
                        \Log::info("Mail sent successfully to {$user->email} for booking ID {$booking->id}");
                    } else {
                        \Log::error("Failed to send mail to {$user->email} for booking ID {$booking->id}");
                    }
                } catch (\Exception $e) {
                    \Log::error("Error while sending mail to {$user->email} for booking ID {$booking->id}: " . $e->getMessage());
                }



            } else {
            \Log::warning("User with ID {$agent->user_id} not found for agent ID {$agent->id}");
            }
            } catch (\Exception $e) {
            \Log::error("Error while sending SMS for agent ID {$agent->id}: " . $e->getMessage());
            }



            BookingCommission::where('invoice_number', $invoice->invoice_number)
                ->update([
                    'payment_status' => 2,
                    'claim_status' => 2,
                    'payment_date' => $paymentDate,
                    'voucher_number' => $voucherNumber
                ]);
        }


        return response()->json(['res' => true, 'msg' => 'Vouchers generated successfully', 'data' => ""], 200);
    }

    private function generatePaymentVoucher($agent, $voucher, $commissions)
    {
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf->setOptions($options);

        $html = view('payment-voucher-pdf', compact('agent', 'voucher', 'commissions'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfFileName = $voucher->voucher_number . '.pdf';
        $pdfFilePath = 'public/vouchers/' . $pdfFileName;

        File::makeDirectory(storage_path('app/public/vouchers'), 0777, true, true);
        $pdfContent = $dompdf->output();
        Storage::put($pdfFilePath, $pdfContent);

        return url('storage/app/public/vouchers/' . $pdfFileName);
    }

    private function generateAgentInvoicePDF($agent, $invoice, $commissions)
    {
        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf->setOptions($options);

        $html = view('invoiceAgentPaymentpdf', compact('agent', 'invoice', 'commissions'))->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfFileName = $invoice['invoice_number'] . '.pdf';
        $pdfFilePath = 'public/invoiceAgent/' . $pdfFileName;

        File::makeDirectory(storage_path('app/public/invoiceAgent'), 0777, true, true);
        $pdfContent = $dompdf->output();
        Storage::put($pdfFilePath, $pdfContent);

        return url('storage/app/public/invoiceAgent/' . $pdfFileName);
    }

    public function getCommissionDetails($id)
    {

        $booking = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('vendors', 'packages.user_id', '=', 'vendors.user_id')
            //->leftJoin('customer_details', 'bookings.customer_id', '=', 'customer_details.user_id')
            ->leftJoin('booking_customer_details', 'bookings.id', '=', 'booking_customer_details.booking_id')
            // ->leftJoin('cities', 'customer_details.city', '=', 'cities.id')
            ->leftJoin('states', 'booking_customer_details.state_id', '=', 'states.id')
            ->leftJoin('users', 'bookings.customer_id', '=', 'users.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->with('payments')
            ->select(

                'bookings.*',
                'bookings.id as bookings_id',
                'bookings.booking_status as bookings_status_id',
                'users.id as user_id',
                'packages.id as package_id',
                'packages.name as package_name',
                'vendors.name as vendor_fullname',
                'booking_customer_details.name as booking_customer_name',
                'booking_customer_details.address as booking_customer_address',
                'booking_customer_details.email as booking_customer_email',
                'booking_customer_details.phone_number as booking_customer_phone_number',
                'booking_customer_details.pan_number as booking_customer_pan_number',
                'states.name as booking_customer_state',
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),

            )
            ->where('bookings.id', $id)
            ->first();


        if (!$booking) {
            return collect();
        }

        return collect([$booking]);

    }
    public function agentCommissionListByInvoice(string $agentCode, array $filters)
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date'])->toDateString() : null;
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date'])->toDateString() : null;
        $paymentStatus = isset($filters['payment_status']) ? $filters['payment_status'] : null;
        $bookingStatus = isset($filters['booking_status']) ? $filters['booking_status'] : null;
        //  $name = isset($filters['name']) ? $filters['name'] : null;
        $bookingId = isset($filters['booking_id']) ? $filters['booking_id'] : null;
        $invoiceNumber = isset($filters['invoice_number']) ? $filters['invoice_number'] : null;

        $query = BookingCommission::leftJoin('bookings', 'bookings.id', '=', 'booking_commissions.booking_id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->select(
                'bookings.id as booking_id',
                'bookings.booking_number as booking_number',
                'bookings.booking_status as booking_status',
                'booking_commissions.base_price',
                'booking_commissions.commission',
                'booking_commissions.group_commission',
                'booking_commissions.commission_amount',
                'booking_commissions.payment_status',
                'booking_commissions.claim_status',
                'booking_commissions.created_at as commission_date',
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as booking_cost'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax'),
                DB::raw('
                CASE
                    WHEN booking_commissions.payment_status = 0 THEN booking_commissions.base_price
                    ELSE 0
                END as paid_amount
            '),
                DB::raw('0 as cancelled_amount'),
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as net_amount'),
            // DB::raw('((SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) - 0) as payable_amount')
            )
            // ->where('bookings.agent_code', $agentCode)
            ->where('booking_commissions.invoice_number', $invoiceNumber)

            ->groupBy(
                'bookings.id',
                'booking_commissions.base_price',
                'booking_commissions.commission',
                'booking_commissions.group_commission',
                'booking_commissions.commission_amount',
                'booking_commissions.payment_status',
                'booking_commissions.claim_status',
                'commission_date',
                'paid_amount',
                'cancelled_amount',
                'net_amount',
            // 'payable_amount'
            )
            ->orderByRaw('MAX(booking_commissions.created_at) desc');

        if ($startDate && $endDate) {
            $query->whereDate('bookings.created_at', '>=', $startDate)
                ->whereDate('bookings.created_at', '<=', $endDate);
        }

        if ($paymentStatus) {
            $query->where('booking_commissions.payment_status', $paymentStatus);
        }

        if ($bookingStatus) {
            $query->where('bookings.booking_status', $bookingStatus);
        }

        if ($bookingId) {
            $query->where('bookings.id', $bookingId);
        }

        // if ($invoiceNumber) {
        //     $query->where('booking_commissions.invoice_number', $invoiceNumber);
        // }

        $result = $query->get();
        //dd($result);
        // Post-process results to ensure correct calculations


        return $result;
    }
    public function getAgentLedgerssss($userId, $startDate, $endDate)
    {
        // Retrieve the agent ID based on the user ID
        $agentID = AgentDetails::where('user_id', $userId)->pluck('id')->first();

        if (!$agentID) {
            return [
                'error' => 'Agent not found for the given user ID',
                'status_code' => 404
            ];
        }

        // Parse start and end dates
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->endOfDay();

        // Determine the previous month's start and end dates
        $previousMonthStart = $startDate->copy()->subMonth()->startOfMonth();
        $previousMonthEnd = $startDate->copy()->subMonth()->endOfMonth();

        // Fetch commissions within the date range for the agent
        $commissions = BookingCommission::where('user_id', $agentID)
            ->whereBetween('claimed_date', [$startDate, $endDate])
            ->get();

        // Fetch previous month's data for opening balance
        $previousMonthCommissions = BookingCommission::where('user_id', $agentID)
            ->whereBetween('claimed_date', [$previousMonthStart, $previousMonthEnd])
            ->get();

        $data = [];
        $monthlyTotals = [];
        $previousMonthTotals = ['debit' => 0, 'credit' => 0];
        $totalDebit = 0;
        $totalCredit = 0;

        // Process the previous month's commissions to get the closing balance
        $previousMonthClosingBalance = 0; // Initialize closing balance
        foreach ($previousMonthCommissions as $commission) {
            $debitAmount = round($commission->commission_amount, 2);
            $creditAmount = round($commission->commission_amount * 0.01, 2); // TDS 1% of commission_amount
            $previousMonthTotals['debit'] += $debitAmount;
            $previousMonthTotals['credit'] += $creditAmount;

            // Calculate closing balance within the loop
            $previousMonthClosingBalance += $debitAmount - $creditAmount;
        }

        // Initialize opening balance for the current period
        $openingBalance = $previousMonthClosingBalance;

        // Process the current month's commissions into a combined data array with type and calculate monthly totals
        foreach ($commissions as $commission) {
            $debitAmount = round($commission->commission_amount, 2);
            $creditAmount = round($commission->commission_amount * 0.01, 2); // TDS 1% of commission_amount
            $monthYear = Carbon::parse($commission->claimed_date)->format('Y-m'); // Using 'Y-m' to uniquely identify the month

            // Add commission data
            $data[] = [
                'date' => $commission->claimed_date,
                'booking_id' => $commission->booking_id,
                'amount' => number_format($debitAmount, 2, '.', ''),
                'type' => 'debit',
                'description' => "Commission for booking ID {$commission->booking_id}"
            ];
            $data[] = [
                'date' => $commission->claimed_date,
                'booking_id' => $commission->booking_id,
                'amount' => number_format($creditAmount, 2, '.', ''),
                'type' => 'credit',
                'description' => "TDS for booking ID {$commission->booking_id}"
            ];

            // Update monthly totals
            if (!isset($monthlyTotals[$monthYear])) {
                $monthlyTotals[$monthYear] = [
                    'debit' => 0,
                    'credit' => 0,
                    'last_date' => Carbon::parse($commission->claimed_date)->endOfMonth()->toDateString()
                ];
            }
            $monthlyTotals[$monthYear]['debit'] += $debitAmount;
            $monthlyTotals[$monthYear]['credit'] += $creditAmount;

            // Update total debit and credit
            $totalDebit += $debitAmount;
            $totalCredit += $creditAmount;
        }

        // Calculate the closing balance for the current period
        $closingBalance = $openingBalance + $totalDebit - $totalCredit;

        // Prepare the totals array with month-wise breakdown and opening balance
        $totals = [];

        // Include the opening balance for the first month
        $totals[] = [
            'date' => $startDate->copy()->startOfMonth()->toDateString(), // Date should be the 1st of the start month
            'month' => Carbon::parse($startDate)->format('F Y'),
            'type' => 'opening-balance',
            'amount' => number_format($openingBalance, 2, '.', ''),
            'description' => "Opening balance"
        ];

        foreach ($monthlyTotals as $monthYear => $totalsForMonth) {
            $totals[] = [
                'date' => $totalsForMonth['last_date'], // Date of the last day of the month
                'month' => Carbon::parse($monthYear)->format('F Y'),
                'type' => 'debit',
                'amount' => number_format($totalsForMonth['debit'], 2, '.', ''),
                'description' => "Total commission for " . Carbon::parse($monthYear)->format('F Y')
            ];
            $totals[] = [
                'date' => $totalsForMonth['last_date'], // Date of the last day of the month
                'month' => Carbon::parse($monthYear)->format('F Y'),
                'type' => 'credit',
                'amount' => number_format($totalsForMonth['credit'], 2, '.', ''),
                'description' => "Total TDS for " . Carbon::parse($monthYear)->format('F Y')
            ];
        }

        // Add the closing balance entry for the final period
        $totals[] = [
            'date' => $endDate->copy()->endOfMonth()->toDateString(), // Date should be the last day of the end month
            'month' => Carbon::parse($endDate)->format('F Y'),
            'type' => 'closing-balance',
            'amount' => number_format($closingBalance, 2, '.', ''),
            'description' => "Closing balance"
        ];

        return [
            'data' => $data,
            'totals' => $totals,
            'status_code' => 200
        ];
    }


    public function getAgentLedger($userID, $startDate, $endDate)
   {
    $agentID = AgentDetails::where('user_id', $userID)->pluck('id')->first();
    $start = Carbon::parse($startDate)->startOfMonth();
    $end = Carbon::parse($endDate)->endOfMonth();

    // Retrieve the first commission of the agent to determine the initial balance
    $firstCommission = BookingCommission::where('user_id', $agentID)
        ->orderBy('claimed_date', 'asc')
        ->first();

    // Initialize the ledger array and the opening balance
    $ledger = [];
    $openingBalance = 0;

    // Calculate the initial opening balance if the first commission exists
    if ($firstCommission) {
        $initialStartDate = Carbon::parse($firstCommission->claimed_date)->startOfMonth();
        if ($initialStartDate->lessThan($start)) {
            // Get total commission and TDS before the start of the ledger period
            $initialBalance = BookingCommission::where('user_id', $agentID)
                ->where('claimed_date', '<=', $start->copy()->subMonth()->endOfMonth())
                ->select(
                    DB::raw('ROUND(SUM(commission_amount), 2) as total_commission'),
                    DB::raw('ROUND(SUM(commission_amount * 0.01), 2) as total_tds')
                )
                ->first();

            $openingBalance = number_format(($initialBalance->total_commission - $initialBalance->total_tds), 2, '.', '');
        }
    }

    // Add the opening balance entry at the top of the ledger
    $ledger[] = [
        'date' => $start->format('Y-m-d'),
        'description' => 'Opening Balance',
        'amount' => $openingBalance,
        'type' => 'opening-balance',
        'ref_no' => ''
    ];

    // Initialize closing balance
    $closingBalance = $openingBalance;

    // Loop through each month in the date range
    while ($start->lessThanOrEqualTo($end)) {
        $currentMonthStart = $start->copy()->startOfMonth();
        $currentMonthEnd = $start->copy()->endOfMonth();

        // Retrieve commissions for the current month
        $commissions = BookingCommission::where('user_id', $agentID)
            ->whereBetween('claimed_date', [$currentMonthStart, $currentMonthEnd])
            ->select(
                'invoice_number',
                DB::raw('ROUND(SUM(commission_amount), 2) as total_commission'),
                DB::raw('ROUND(SUM(commission_amount * 0.01), 2) as total_tds')
            )
            ->groupBy('invoice_number')
            ->get();

        // Retrieve payouts for the current month
        $payouts = BookingCommission::where('user_id', $agentID)
            ->where('payment_status', 2)
            ->whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd])
            ->select(
                'voucher_number',
                DB::raw('ROUND(SUM(paid_amount), 2) as total_payout'),
                DB::raw('MAX(payment_date) as payment_date')
            )
            ->groupBy('voucher_number')
            ->get();

        // Add commission entries to the ledger
        foreach ($commissions as $commission) {
            $ledger[] = [
                'date' => $currentMonthEnd->format('Y-m-d'),
                'description' => 'Total Commission for ' . $currentMonthEnd->format('F Y'),
                'amount' => $commission->total_commission,
                'type' => 'debit',
                'ref_no' => $commission->invoice_number
            ];

            $closingBalance += $commission->total_commission;

            // Add TDS entry to the ledger
            $ledger[] = [
                'date' => $currentMonthEnd->format('Y-m-d'),
                'description' => 'Total TDS for ' . $currentMonthEnd->format('F Y'),
                'amount' => $commission->total_tds,
                'type' => 'credit',
                'ref_no' => $commission->invoice_number
            ];

            $closingBalance -= $commission->total_tds;
        }

        // Add payout entries to the ledger
        foreach ($payouts as $payout) {
            $ledger[] = [
                'date' => Carbon::parse($payout->payment_date)->toDateString(),
                'description' => 'Payment',
                'amount' => $payout->total_payout,
                'type' => 'credit',
                'ref_no' => $payout->voucher_number
            ];

            $closingBalance -= $payout->total_payout;
        }

        // Move to the next month
        $start->addMonth();
    }

    // Add the closing balance entry at the end of the ledger
    $ledger[] = [
        'date' => $end->format('Y-m-d'),
        'description' => 'Closing Balance',
        'amount' => number_format($closingBalance, 2, '.', ''),
        'type' => 'closing-balance',
        'ref_no' => ''
    ];

    return $ledger;
   }

    public function generateLedger($companyName, $companyAddress, $agentDetails, $ledger, $agMobile, $totalDebit, $totalCredit, $finalBalance,$closingBalanceDate)
    {
    $dompdf = new Dompdf();
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $dompdf->setOptions($options);

    $html = view('agent-ledger-pdf', compact('companyName','companyAddress','agentDetails', 'ledger', 'agMobile', 'totalDebit', 'totalCredit', 'finalBalance','closingBalanceDate'))->render();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfFileName = 'ledger_' . $agentDetails->id . '.pdf';
    $pdfFilePath = 'public/ledger/' . $pdfFileName;

    File::makeDirectory(storage_path('app/public/ledger'), 0777, true, true);
    $pdfContent = $dompdf->output();
    Storage::put($pdfFilePath, $pdfContent);

    return url('storage/app/public/ledger/' . $pdfFileName);
    }

    public function customerInvoice()
   {
    $currentDate = Carbon::now()->format('md'); // Format as YYYYMMDD
    $currentMonth = Carbon::now()->format('m'); // Current month in two digits

    $bookings = BookingPayment::where('payment_date', '<=', Carbon::now())
        ->where('payment_type', 'final')
        ->join('bookings', 'booking_payments.booking_id', '=', 'bookings.id')
        ->select('bookings.*','bookings.id as booking_id', 'booking_payments.payment_date', 'booking_payments.payment_type')
        ->get();
        $pdfUrls = [];
    foreach ($bookings as $booking) {
        // Generate a unique invoice number
        $invoiceNumber = 'WMTINV' . $currentDate . $booking->booking_id;

        // Log the invoice number being updated
        \Log::info('Updating booking ID ' . $booking->booking_id . ' with invoice number ' . $invoiceNumber);

        // Update the booking with the generated invoice number
        Booking::where('id', $booking->booking_id)->update(['invoice_number' => $invoiceNumber]);
        $bookingPayment = BookingPayment::where('booking_id', $booking->booking_id)->first();
       // $bookings = Booking::where('id', $booking->booking_id)->first();
        $bookingRooms = BookingRoom::where('booking_id', $booking->booking_id)->get();
        $bookingPassengers = BookingPassenger::where('booking_id', $booking->booking_id)->get();
        $bookingDates = BookingDate::where('booking_id', $booking->booking_id)->get();
        $bookingCustomer = BookingCustomerDetails::where('booking_id', $booking->booking_id)->get();
        $package = Package::with(['inclusions', 'exclusions'])->find($booking->package_id);

        $packageName = $package ? $package->name : '';
        $totalDays = $package->total_days;
        $totalNights = $totalDays - 1;
        $totalDaysAndNights = "{$totalNights} night" . ($totalNights > 1 ? 's' : '') . " and {$totalDays} days";
        $vendorDetails = Vendor::where('user_id', $package->user_id)->first();

        $packageAddons = [];
        $totalAddonPrice = 0.00;
        if (isset($booking->add_on_id)) {
            $packageAddons = PackageAddon::whereIn('id', explode(',', $booking->add_on_id))->get();
            $totalAddonPrice = $packageAddons->sum('price');
        }

        $finalPriceInWords = Helper::AmountInWords($booking->final_price);
        $logoUrl = 'https://wishmytour.in/backend/public/images/logo.jpg';
        $itineraries = Itinerary::where('package_id', $package->id)->get();

        // Convert collections to arrays
        $inclusions = $package->inclusions->pluck('name')->toArray();
        $exclusions = $package->exclusions->pluck('name')->toArray();

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

        $paymentPolicies = explode(',', $package->payment_policy);
        $formattedPaymentPolicies = [];
        foreach ($paymentPolicies as $index => $policy) {
            $policyDetails = explode('-', $policy);
            if ($index === 0) {
                $formattedPaymentPolicies[] = "{$policyDetails[0]} or more days before departure: {$policyDetails[2]}%";
            } else {
                $formattedPaymentPolicies[] = "Between {$policyDetails[0]} to {$policyDetails[1]} days before departure: {$policyDetails[2]}%";
            }
        }
        $paymentPolicies = $formattedPaymentPolicies;

        $terms = Config::where('name', 'terms_and_conditions')->first();
        $defaultTerms = $terms ? $terms->value : '';
        $packageTerms = $package->terms_and_conditions;
        $stayPlan = $package->getItineraries($package->id);

        // Generate PDF
        $pdfContent = $this->generateCusInvoicePDF(
             $booking,
            $bookingRooms,
            $bookingPassengers,
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
            $stayPlan,
            $bookingPayment
        );

        // Generate a unique filename with timestamp
        $pdfFileName = $invoiceNumber . '.pdf';

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
         // Add the PDF URL to the response array
         $pdfUrls[] = [
            'booking_id' => $booking->booking_id,
            'booking_invoice' => $booking->invoice_number,
            'pdf_url' => $pdfUrl
        ];

    }

    //return $bookings;
    return response()->json([
        'res' => true,
        'msg' => 'Invoices generated successfully',
        'data' => $pdfUrls // Include the PDF URLs in the response
    ], 200);
    }


    public function generateCusInvoicePDF($booking, $bookingRooms, $bookingPassengers, $bookingDates, $bookingCustomer, $packageName, $vendorDetails, $packageAddons, $totalAddonPrice, $finalPriceInWords, $logoUrl, $itineraries, $inclusions, $exclusions, $paymentPolicies, $cancellationPolicies, $packageTerms, $defaultTerms, $totalDaysAndNights, $stayPlan,$bookingPayment)
    {
    $pdf = new Dompdf();
    $html = view('customerInvoice', compact('booking', 'bookingRooms', 'bookingPassengers', 'bookingDates', 'bookingCustomer', 'vendorDetails', 'packageName', 'packageName', 'packageAddons', 'totalAddonPrice', 'finalPriceInWords', 'logoUrl', 'itineraries', 'inclusions', 'exclusions', 'paymentPolicies', 'cancellationPolicies', 'packageTerms', 'defaultTerms', 'totalDaysAndNights', 'stayPlan','bookingPayment'))->render();

    // Load HTML into Dompdf
    $pdf->loadHtml($html);

    // (Optional) Set paper size and orientation
    $pdf->setPaper('A4', 'portrait');

    // Render PDF
    $pdf->render();

    // Get PDF content
    return $pdf->output();
    }

    /**
     * Booking History
     * 
     * This function retrieves the booking history for a specific user based on the provided filters. It fetches the booking 
     * data from the database, applies the filters, and returns the result.
     *
     * @param int $userId
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function bookingHistory(int $userId, array $filters)
    {
        $startDate = isset($filters['start_date']) ? Carbon::parse($filters['start_date'])->toDateString() : null;
        $endDate = isset($filters['end_date']) ? Carbon::parse($filters['end_date'])->toDateString() : null;

        $query = Booking::leftJoin('packages', 'bookings.package_id', '=', 'packages.id')
            ->leftJoin('booking_dates', 'booking_dates.booking_id', '=', 'bookings.id')
            ->leftJoin('booking_customer_details', 'bookings.id', '=', 'booking_customer_details.booking_id')
            ->whereNotIn('booking_status', [6])
            ->select(
                'bookings.id as bookings_id',
                'bookings.*',
                'packages.id as package_id',
                'packages.name as package_name',
                'booking_customer_details.*',
                DB::raw('(SELECT MIN(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as first_booking_date'),
                DB::raw('(SELECT MAX(booking_date) FROM booking_dates WHERE booking_id = bookings.id) as last_booking_date'),
                DB::raw('(SELECT MAX(cost) FROM booking_dates WHERE booking_id = bookings.id) as booking_cost'),
                DB::raw('(SELECT SUM(adults + children) FROM booking_rooms WHERE booking_id = bookings.id) as total_pax')
            )
            ->where('packages.user_id', $userId)
            ->groupBy('bookings.id', 'booking_customer_details.id')
            ->orderBy('bookings.created_at', 'desc');

        if ($startDate && $endDate) {
            $query->whereDate('bookings.created_at', '>=', $startDate)
                ->whereDate('bookings.created_at', '<=', $endDate);
        }

        // Additional filters
        if (isset($filters['booking_id'])) {
            $query->where('bookings.id', $filters['booking_id']);
        }

        if (isset($filters['phone_number'])) {
            $query->where('booking_customer_details.phone_number', $filters['phone_number']);
        }

        if (isset($filters['name'])) {
            $query->where('booking_customer_details.name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['origin_city_id'])) {
            $query->where('packages.origin_city_id', $filters['origin_city_id']);
        }

        if (isset($filters['booking_status'])) {
            $query->where('bookings.booking_status', $filters['booking_status']);
        }

        if (isset($filters['flag_status'])) {
            $flagStatus = is_array($filters['flag_status']) ? $filters['flag_status'] : explode(',', $filters['flag_status']);
            $query->whereIn('bookings.booking_status', $flagStatus);
        }

        return $query->get();
    }
}
