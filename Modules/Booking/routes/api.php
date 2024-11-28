<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Booking\app\Http\Controllers\BookingController;
/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/
Route::get('/update-completed-bookings', [BookingController::class, 'updateCompletedBookings']);//Cronjob
Route::get('/generate-commission-vouchers', [BookingController::class, 'generateCommissionVouchers']);//Cronjob
Route::get('/commission-invoices', [BookingController::class, 'commissionInvoice']);//Cronjob
Route::get('/reminder-sms', [BookingController::class, 'reminderSms']);//Cronjob
Route::get('/customer-invoices', [BookingController::class, 'customerInvoice']);//Cronjob

Route::middleware('auth:api')->group(function () {  //Customer
                Route::post('/add-booking', [BookingController::class, 'addBooking']);
                Route::post('/payment-transaction', [BookingController::class, 'paymentTransaction']);
                Route::post('/upcoming-booking', [BookingController::class, 'upcomingBooking']);
                Route::post('/completed-booking', [BookingController::class, 'completedBooking']);
                Route::post('/bookingdetails/{id}/view', [BookingController::class, 'bookingDetails']);
                Route::post('/cancel-booking', [BookingController::class, 'cancelBooking']);
                Route::post('/confirm-booking', [BookingController::class, 'confirmBooking']);
                Route::post('/cancel-booking-list', [BookingController::class, 'cancelBookingList']);
                Route::post('/add-feedback', [BookingController::class, 'addFeedback']);
                Route::post('/add-report', [BookingController::class, 'addReport']);

                Route::post('/send-booking-onrequest', [BookingController::class, 'sendBookingOnRequest']);
                Route::post('/booking-onrequest-list', [BookingController::class, 'bookingOnRequestList']); 
                Route::get('/booking-onreq-details/{id}', [BookingController::class, 'bookingOnRequestDetails']);
                Route::post('/onrequest-token-verified', [BookingController::class, 'bookingOnReqTokenVerified']);

                Route::get('/booking/{id}/download-pdf', [BookingController::class, 'downloadBookingPDF']); //not needed
                Route::post('/add-booking-payment', [BookingController::class, 'addBookingPayment']);
                Route::post('/payment-history', [BookingController::class, 'paymentHistory']);
                Route::post('/refund-history', [BookingController::class, 'refundHistory']);
                Route::get('/payment-history/pdf', [BookingController::class, 'downloadPaymentHistoryPdf']);

                Route::post('/coupons', [BookingController::class, 'coupons']);
                Route::post('/coupons/check', [BookingController::class, 'checkCoupon']);
        // Prefix vendor routes
Route::prefix('vendor')->group(function () { //Vendor
                Route::post('/booking-list', [BookingController::class, 'vendorBookingList']);
                Route::post('/booking-onrequest-list', [BookingController::class, 'vendorBookingOnReqList']);
                Route::get('/booking-onrequest-details/{id}', [BookingController::class, 'bookingOnReqDetails']);
                Route::post('/booking-onrequest-approve', [BookingController::class, 'bookingOnReqApprove']);
                Route::post('/booking-onrequest-declined', [BookingController::class, 'bookingOnReqDeclined']);
                //  Route::post('/onrequest-approve', [BookingController::class, 'onReqApprove']);
                // Route::post('/booking-list/filter', [BookingController::class, 'filterVendorBookingListByDate']);
                Route::get('/cities', [BookingController::class, 'getVendorCities']);
                Route::get('/booking/{id}/view', [BookingController::class, 'vendorBookingView']);
                Route::post('/add-message', [BookingController::class, 'addMessage']);
                Route::post('/cancel-approve', [BookingController::class, 'cancelApprove']);
                //Report
                Route::post('/booking-history', [BookingController::class, 'bookingHistory']);
         });

Route::prefix('agent')->group(function () { //agent
                Route::post('/agent-booking-list', [BookingController::class, 'agentBookingList']);
                Route::get('/agent-booking/{id}/view', [BookingController::class, 'agentBookingView']);
                Route::post('/agent-commission-list', [BookingController::class, 'agentCommissionList']);
                Route::post('/commissiondetails/{id}/view', [BookingController::class, 'commissionDetails']);
                Route::post('/agent-booking/{id}/claim', [BookingController::class, 'claimBooking']); 
                Route::post('/agent-payment-history', [BookingController::class, 'agentPaymentHistory']);
                Route::post('/agent-payment-history-by-invoice', [BookingController::class, 'agentPaymentByInvoice']);
                Route::post('/commission-ledger', [BookingController::class, 'commissionLedger']);

       });

});

Route::post('/taxes', [BookingController::class, 'fetchTaxes']);

   