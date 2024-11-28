<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\User\app\Http\Controllers\UserController;


//User login 'user_type' => 2
Route::post('/login', [UserController::class, 'login']);
Route::post('/verify-otp', [UserController::class, 'verifyOTP']);
Route::post('/add-customer', [UserController::class, 'addCustomer']);
Route::middleware('auth:api')->post('/customer-details-view', [UserController::class, 'registerDetailsView']);
Route::middleware('auth:api')->post('/edit-customer', [UserController::class, 'editCustomer']);
Route::middleware('auth:api')->post('/add_photo_id', [UserController::class, 'addPhotoId']);
Route::middleware('auth:api')->group(function () {
    Route::post('/change-phone-number', [UserController::class, 'changePhoneNumber']);
    Route::post('/verify-phone-number', [UserController::class, 'verifyPhoneNumber']);
    Route::post('/logout', [UserController::class, 'logout']);
});

// Vendor routes without authentication
Route::prefix('vendor')->group(function () {
    Route::post('/login', [UserController::class, 'vendorLogin'])->name('vendor.login');
    Route::post('/verify-otp', [UserController::class, 'vendorVerifyOTP'])->name('vendor.verify-otp');
    Route::post('/verify-email-or-mobile', [UserController::class, 'verifyEmailOrMobile'])->name('vendor.verify-email-or-mobile');
    Route::post('/update-otp', [UserController::class, 'updateOTP'])->name('vendor.update-otp');
});

// Vendor routes with authentication
Route::middleware('auth:api')->prefix('vendor')->group(function () {
    Route::post('/add-details', [UserController::class, 'vendorAddDetails'])->name('vendor.add-details');
    //Route::post('/gst-details', [UserController::class, 'getGstDetails'])->name('vendor.gstDetails');
    Route::get('/gst-details/{gstin}', [UserController::class, 'getGstDetails'])->name('vendor.gstDetails');
    Route::post('/pan-details/{pan}', [UserController::class, 'getPanDetails'])->name('vendor.panDetails');
    Route::post('/update-details', [UserController::class, 'vendorUpdateDetails'])->name('vendor.update-details');
    Route::post('/view-profile', [UserController::class, 'vendorViewProfile'])->name('vendor.view-profile');
    Route::post('/update-profile', [UserController::class, 'vendorUpdateProfile'])->name('vendor.update-profile');
    Route::post('/add-bank-details', [UserController::class, 'vendorAddBankDetails'])->name('vendor.add-bank-details');
    Route::post('/file-manager', [UserController::class, 'vendorFileManager'])->name('vendor.file-manager');
});

//Vendor login 'user_type' => 3
//Route::post('/vendor-login', [UserController::class, 'vendorLogin']);
//Route::post('/vendor-verify-otp', [UserController::class, 'vendorVerifyOTP']);
Route::middleware('auth:api')->group(function () {
//    Route::post('/add-vendor-information', [UserController::class, 'addVendorInformation']);
//    Route::post('/edit-vendor-information', [UserController::class, 'editVendorInformation']);
//    Route::post('/vendor-user-profile-view', [UserController::class, 'vendorUserProfileView']);
//    Route::post('/edit-vendor-user-profile', [UserController::class, 'editVendorUserProfile']);
//    Route::post('/add-banking-account-vendor', [UserController::class, 'addBankingAccountVendor']);

    // Route to fetch notifications
    Route::get('/notifications', [UserController::class, 'notificationlist']);
    // Route to mark a notification as read
    Route::put('/notifications/mark-as-read', [UserController::class, 'markAllAsRead']);
    // Route to mark a notification as unread
    Route::put('/notifications/{notification}/mark-as-unread', [UserController::class, 'markAsUnread']);
    Route::post('/notifications/remove-all', [UserController::class, 'removeAllNotifications']);
   
});

//Admin login 'user_type' => 1
Route::post('/admin-login', [UserController::class, 'adminLogin']);



//Agents login 'user_type' => 4
Route::post('/agent-login', [UserController::class, 'agentLogin']);
Route::post('/agent-verify-otp', [UserController::class, 'agentVerifyOTP']);
Route::post('/add-agent', [UserController::class, 'addAgent']);
Route::middleware('auth:api')->group(function () {
    Route::post('/agent-details-view', [UserController::class, 'agentDetailsView']);
    Route::post('/edit-agent', [UserController::class, 'editAgent']);
    Route::post('/add-banking-account-agent', [UserController::class, 'addBankingAccountAgent']);

    // Route::post('/verify-phone-number', [UserController::class, 'verifyPhoneNumber']);
    // Route::post('/logout', [UserController::class, 'logout']);
});