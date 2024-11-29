<?php

use Illuminate\Support\Facades\Route;
use Modules\Booking\app\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('booking', BookingController::class)->names('booking');
});
Route::get('/doc', [BookingController::class, 'doc']);
Route::get('/view-booking-pdf', function () {
    // Call the method responsible for generating the PDF content
    $bookingController = new Modules\Booking\App\Http\Controllers\BookingController();
    $pdfContent = $bookingController->generateBookingPDF();

    // Return the PDF content as a response with headers indicating it's a PDF file
    return Response::make($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="booking_document.pdf"',
    ]);
});



Route::view('/welcome', 'booking-html');