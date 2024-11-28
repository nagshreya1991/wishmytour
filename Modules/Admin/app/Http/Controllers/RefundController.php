<?php

namespace Modules\Admin\app\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Admin\app\Http\Services\RefundService;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Booking\app\Models\BookingCancellation;
class RefundController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    public function refundBooking()
    {
        $cancelledBookings = $this->refundService->getAllCancelledBookings();

       // return view('admin.booking.refund-booking', compact('cancelledBookings'));
        return view('backend.booking.refund-booking', compact('cancelledBookings'));
    }
    public function show($id)
    {
        $bookingCancellation = $this->refundService->getCancelledBookingById($id);
        return view('backend.booking.refund-show', compact('bookingCancellation'));
    }
    public function processRefund(Request $request)
{
    $request->validate([
        'booking_id' => 'required|integer',
        'refund_amount' => 'required|numeric'
    ]);

    $bookingId = $request->input('booking_id');
    $refundAmount = $request->input('refund_amount');
    $payment_transaction = $request->input('payment_transaction');

    try {
        $bookingCancellation = $this->refundService->getCancelledBookingById($bookingId);

        // Log the booking cancellation details
        Log::info('Booking Cancellation Details: ', $bookingCancellation->toArray());

        // Check for missing transaction number
        if (empty($bookingCancellation->payment_transaction) || $bookingCancellation->payment_transaction === 'N/A') {
            throw new \Exception('Transaction number is missing for booking ID: ' . $bookingId);
        }

        // Prepare data for PhonePe API
        $data = [
            'merchantId' => env('PHONEPE_MERCHANT_ID'),
            'merchantUserId' => 'User123',
            'originalTransactionId' => $payment_transaction,
            'merchantTransactionId' => 'RO' . $payment_transaction,
            'amount' => $refundAmount * 100, // amount in paise
            'callbackUrl' => route('admin.refund.callback')
        ];

        // Base64 encode the payload
        $jsonPayload = json_encode($data);
        $base64Payload = base64_encode($jsonPayload);

        // Define salt key and index
        $saltKey = env('PHONEPE_SALT_KEY'); // replace with your actual salt key
        $saltIndex = 1; // replace with your actual salt index

        // Generate X-VERIFY header
        $xVerify = hash('sha256', $base64Payload . "/pg/v1/refund" . $saltKey) . "###" . $saltIndex;

        // Send the POST request using Laravel HTTP client
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $xVerify
        ])->post('https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/refund', [
            'request' => $base64Payload
        ]);

        // Handle the response
      //  print_r( $response->json());
        //exit;
        $res = $response->json();
        $merchantId = $res['data']['merchantId'];
        

        $merchantTransactionId = $res['data']['merchantTransactionId'];
        $status_respose = $this->status($merchantId, $merchantTransactionId);
        $status = $status_respose['data']['state'];
      Log::info('status: ', ['status'=> $status]);
 $this->updateRefundStatus( $bookingId, $status);
        //   /echo $status; 
       // print_r($stares);exit;
        // Generate the signature
        

       

        Log::info('PhonePe Refund Response: ' . $status_respose);

        if ($response->successful()) {
            return redirect()->route('booking.refund-show', $bookingId)->with('success', 'Refund request submitted successfully.');
        } else {
            return redirect()->back()->withErrors('Failed to process refund. Please try again later.');
        }
    } catch (\Exception $e) {
        Log::error('Refund Error: ' . $e->getMessage());
        return redirect()->back()->withErrors('Failed to process refund: ' . $e->getMessage());
    }
}

public function status($merchantId, $merchantTransactionId)
    {
        // Define salt key and index
        $saltKey = env('PHONEPE_SALT_KEY'); // replace with your actual salt key
        $saltIndex = 1; // replace with your actual salt index

        // Generate the X-VERIFY header
        $path = "/pg/v1/status/{$merchantId}/{$merchantTransactionId}";
        $xVerify = hash('sha256', $path . $saltKey) . "###" . $saltIndex;

        // Send the GET request using Laravel HTTP client
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $xVerify,
            'X-MERCHANT-ID' => $merchantId
        ])->get("https://api-preprod.phonepe.com/apis/pg-sandbox{$path}");

        // Handle the response
        return $response->json();
    }




private function updateRefundStatus($bookingId, $status)
{
   
    // Retrieve booking cancellation record
    $bookingCancellation = BookingCancellation::where('id', $bookingId)->first();

    if ($bookingCancellation) {
        // Update the refund status based on the state
        Log::warning('status: ' . $status);
        if ($status == 'COMPLETED') {
            $bookingCancellation->status = 1; // Example status field, adjust as necessary
        } else {
            $bookingCancellation->status = 0; // Example status field, adjust as necessary
        }
        $bookingCancellation->save();
    } else {
        Log::warning('Booking not found for refund update: ' . $bookingId);
    }
}
}
