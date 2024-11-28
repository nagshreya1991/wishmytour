<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\LogAdminActivity;
use Modules\Admin\app\Http\Controllers\AdminController;
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
        Route::get('/vendors', [AdminController::class, 'vendors'])->name('vendors');
        Route::post('/vendors/toggle-status', [AdminController::class, 'toggleStatus'])->name('vendors.toggleStatus');
        Route::put('/vendors/{id}', [AdminController::class, 'vendorUpdate'])->name('vendors.update');
        Route::get('/vendors/{id}/edit', [AdminController::class, 'vendorEdit'])->name('vendors.edit');
        Route::get('/vendors/{id}/packages', [AdminController::class, 'vendorPackages'])->name('vendors.packages');

        Route::get('/agents', [AdminController::class, 'agents'])->name('agents');
        Route::get('/agents/{id}', [AdminController::class, 'agentShow'])->name('agents.show');
        Route::put('/agents/{id}', [AdminController::class, 'agentUpdate'])->name('agents.update');
        Route::get('/agents/{id}/commissions', [AdminController::class, 'agentCommissions'])->name('agents.commissions');
        Route::get('/agents/{id}/commissions/{invoiceNumber}', [AdminController::class, 'agentCommissionDetails'])->name('agents.commission.details');
        Route::post('/agent-booking/{id}/payout', [AdminController::class, 'agentPayout'])->name('agents.payout');
        Route::post('/agents/toggle-status', [AdminController::class, 'agentToggleStatus'])->name('agents.toggle-status');
        Route::get('/get-agents-data', [AdminController::class, 'getAgentsData'])->name('agents.data');
        Route::get('/commissions', 'AdminController@commissions')->name('commissions');
        Route::get('/open-commissions', 'AdminController@getOpenCommissions')->name('open-commissions');
        Route::get('/invoiced-commissions', 'AdminController@getInvoicedCommissions')->name('invoiced-commissions');
        Route::post('/process-commissions', 'AdminController@processCommissions')->name('process-commissions');
        Route::get('/processed-commissions', 'AdminController@getProcessedCommissions')->name('processed-commissions');
        Route::get('/agent-open-commissions', 'AdminController@getAgentOpenCommissions')->name('agent-open-commissions');
        Route::get('/agent-invoiced-commissions', 'AdminController@getAgentInvoicedCommissions')->name('agent-invoiced-commissions');
        Route::get('/agent-processed-commissions', 'AdminController@getAgentProcessedCommissions')->name('agent-processed-commissions');
        Route::get('/agents/{id}/ledger', [AdminController::class, 'agentLedger'])->name('agents.ledger');
        Route::get('/agent-ledger', [AdminController::class, 'getAgentLedger'])->name('agent-ledger');


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
