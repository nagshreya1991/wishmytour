<?php

namespace Modules\Admin\app\Http\Services;


use Modules\Booking\app\Models\BookingCancellation;
use Modules\Booking\app\Models\BookingPayment;
use Illuminate\Support\Facades\DB;

class RefundService
{
    public function getAllCancelledBookings()
    {
        return BookingCancellation::with('booking')->get()->map(function($cancellation) {
            $cancellation->booking_number = $cancellation->booking ? $cancellation->booking->booking_number : 'N/A';
            return $cancellation;
        });
    }

    // public function getCancelledBookingById($id)
    // { 
    // // Find the specific BookingCancellation record by ID
    // $cancellation = BookingCancellation::with('booking')->findOrFail($id);

    // // Add booking_number to the cancellation instance if booking is available
    // $cancellation->booking_number = $cancellation->booking ? $cancellation->booking->booking_number : 'N/A';
    // $cancellation->transaction_number = $cancellation->transaction_number ? $cancellation->transaction_number : 'N/A';

    // return $cancellation;
    // }

    public function getCancelledBookingById($id)
   {
    // Find the specific BookingCancellation record by ID
   // $cancellation = BookingCancellation::with('booking')->find($id);
    $cancellation = BookingCancellation::leftJoin('bookings', 'booking_cancellations.booking_id', '=', 'bookings.id')
    ->leftJoin(DB::raw('(SELECT * FROM booking_payments ORDER BY created_at DESC) as booking_payments'), function ($join) {
        $join->on('booking_cancellations.booking_id', '=', 'booking_payments.booking_id');
    })
    ->select(
        'booking_cancellations.*',
        'bookings.booking_number',
        'booking_payments.transaction_number as payment_transaction'
    )
    ->where('booking_cancellations.id', $id)
    ->first();

    if (!$cancellation) {
        throw new \Exception('BookingCancellation not found.');
    }

    // Ensure transaction_number is set, if not, handle accordingly
    if (empty($cancellation->transaction_number)) {
        Log::error('Transaction number is missing for booking ID: ' . $cancellation->booking_id);
        throw new \Exception('Transaction number is missing.');
    }

    // Add booking_number to the cancellation instance if booking is available
    $cancellation->booking_number = $cancellation->booking ? $cancellation->booking->booking_number : 'N/A';

    return $cancellation;
   }
}
