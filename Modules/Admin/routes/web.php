<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LogAdminActivity;
use Modules\Admin\app\Http\Controllers\AdminController;
use Modules\Admin\app\Http\Controllers\AgentController;
use Modules\Admin\app\Http\Controllers\InclusionController;
use Modules\Admin\app\Http\Controllers\ExclusionController;
use Modules\Admin\app\Http\Controllers\LocationController;
use Modules\Admin\app\Http\Controllers\RefundController;
use Modules\Admin\app\Http\Controllers\CouponController;

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

Route::middleware(['auth', LogAdminActivity::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
        Route::get('/customers/{id}/edit', [AdminController::class, 'customerEdit'])->name('customers.edit');
        Route::put('/customers/{id}', [AdminController::class, 'customerUpdate'])->name('customers.update');
        Route::get('/customers/{id}', [AdminController::class, 'customerShow'])->name('customers.show');
         // Booking routes
         Route::get('/customers/{id}/bookings', [AdminController::class, 'customerBookings'])->name('customers.bookings');
         Route::get('/customers/{id}/complete-bookings', [AdminController::class, 'customerCompleteBookings'])->name('customers.complete-bookings');
         Route::get('/customers/{id}/cancelled-bookings', [AdminController::class, 'customerCancelledBookings'])->name('customers.cancelled-bookings');
         Route::get('/customers/{id}/upcoming-bookings', [AdminController::class, 'customerUpcomingBookings'])->name('customers.upcoming-bookings');

         
        Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors');
        Route::post('/vendors/toggle-status', [AdminController::class, 'toggleStatus'])->name('vendors.toggleStatus');
        Route::put('/vendors/{id}', [AdminController::class, 'vendorUpdate'])->name('vendors.update');
        Route::get('/vendors/{id}/edit', [AdminController::class, 'vendorEdit'])->name('vendors.edit');
        Route::get('/vendors/{id}/packages', [AdminController::class, 'vendorPackages'])->name('vendors.packages');

        Route::get('/agents', [AgentController::class, 'index'])->name('agents');
        Route::get('/get-agents-data', [AgentController::class, 'getAgentsData'])->name('agents.data');
        Route::get('/agents/{id}', [AgentController::class, 'agentShow'])->name('agents.show');
        Route::put('/agents/{id}', [AgentController::class, 'agentUpdate'])->name('agents.update');
        Route::get('/agents/{id}/commissions', [AgentController::class, 'agentCommissions'])->name('agents.commissions');
        Route::get('/agents/{id}/commissions/{invoiceNumber}', [AgentController::class, 'agentCommissionDetails'])->name('agents.commission.details');
        Route::post('/agent-booking/{id}/payout', [AgentController::class, 'agentPayout'])->name('agents.payout');
        Route::post('/agents/toggle-status', [AgentController::class, 'agentToggleStatus'])->name('agents.toggle-status');
        Route::get('/commissions', 'AgentController@commissions')->name('commissions');
        Route::get('/open-commissions', 'AgentController@getOpenCommissions')->name('open-commissions');
        Route::get('/invoiced-commissions', 'AgentController@getInvoicedCommissions')->name('invoiced-commissions');
        Route::post('/process-commissions', 'AgentController@processCommissions')->name('process-commissions');
        Route::get('/processed-commissions', 'AgentController@getProcessedCommissions')->name('processed-commissions');
        Route::get('/agent-open-commissions', 'AgentController@getAgentOpenCommissions')->name('agent-open-commissions');
        Route::get('/agent-invoiced-commissions', 'AgentController@getAgentInvoicedCommissions')->name('agent-invoiced-commissions');
        Route::get('/agent-processed-commissions', 'AgentController@getAgentProcessedCommissions')->name('agent-processed-commissions');
        Route::get('/agents/{id}/ledger', [AgentController::class, 'agentLedger'])->name('agents.ledger');
        Route::get('/agent-ledger', [AgentController::class, 'getAgentLedger'])->name('agent-ledger');
        Route::get('/generate-ledger-pdf', 'AgentController@generateLedgerPdf')->name('generate-ledger-pdf');

        Route::get('/packages', [AdminController::class, 'packages'])->name('packages');
        Route::put('/packages/{id}', [AdminController::class, 'packagesUpdate'])->name('packages.update');
        Route::get('/packages/{id}/view', [AdminController::class, 'packagesView'])->name('packages.view');
        Route::get('/packages/{id}/messages', [AdminController::class, 'packagesMessage'])->name('packages.messages');
        Route::post('/packages/{id}/messages', [AdminController::class, 'storeMessage'])->name('packages.messages.store');

        Route::resource('/inclusions', InclusionController::class);
        Route::resource('/exclusions', ExclusionController::class);
        Route::resource('/locations', LocationController::class);

        Route::get('/notifications', [AdminController::class, 'notificationList'])->name('notifications');
        Route::delete('/notifications/{id}', [AdminController::class, 'deleteNotification'])->name('notifications.delete');
       // Route::delete('/notifications/bulk-delete', [AdminController::class, 'bulkDeleteNotifications'])->name('admin.notifications.bulkDelete');
        Route::post('/notifications/mark-all-as-read', [AdminController::class, 'markAllNotificationsAsRead'])->name('notifications.markAllAsRead');
        Route::post('/notifications/clear-all', [AdminController::class, 'clearAllNotifications'])->name('notifications.clearAll');
        Route::post('/notifications/remove-all', [AdminController::class, 'removeAllNotifications'])->name('notifications.removeAll');


        Route::get('/booking', [AdminController::class, 'booking'])->name('booking');


       // Route::get('/booking', [AdminController::class, 'getBookingsByStatus'])->name('booking');
        Route::get('/refund-booking', [RefundController::class, 'refundBooking'])->name('booking.refund-booking');
        Route::get('/refund-booking/{id}', [RefundController::class, 'show'])->name('booking.refund-show');
        Route::get('/booking/{id}/view', [AdminController::class, 'bookingView'])->name('booking.view');
       // Route::get('/refund/{id}', [RefundController::class, 'show'])->name('refund.show');
       // Route::post('/refund/process', [RefundController::class, 'processRefund'])->name('booking.process');
      // Route::post('/refund/process', [RefundController::class, 'processRefund'])->name('booking.process');
       Route::post('/refund/process', [RefundController::class, 'processRefund'])->name('refund.process');
       Route::post('/refund/callback', [RefundController::class, 'handleCallback'])->name('refund.callback');
       //Route::post('/phonepe/callback', [RefundController::class, 'handleCallback'])->name('phonepe.callback');

        

        Route::post('/agents/bulk-payout', [AdminController::class, 'bulkPayout'])->name('agents.bulkPayout');
        //Route::get('/agents/paid-commissions', [AdminController::class, 'paidCommissions'])->name('agents.paidCommissions');



        //Route::get('/coupons', [AdminController::class, 'coupons'])->name('coupons');
        Route::resource('/coupons', CouponController::class);
        Route::post('/coupons/toggleStatus', [CouponController::class, 'toggleStatus'])->name('coupons.toggleStatus');
        Route::post('/coupons/toggleShowStatus', [CouponController::class, 'toggleShowStatus'])->name('coupons.toggleShowStatus');
        Route::get('/coupons/{id}/bookings', [CouponController::class, 'couponBookings'])->name('coupons.bookings');


        Route::resource('/referrals', ReferralController::class);
        Route::resource('/config', ConfigController::class);
        Route::resource('/contact', ContactController::class);
        Route::resource('/pages', PageController::class);
    });

// Admin Login routes
Route::get('/admin', [AdminController::class, 'index'])->name('admin.login');
Route::post('/admin', [AdminController::class, 'login']);
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

//Route::get('/render-header', [AdminController::class, 'renderHeader']);
