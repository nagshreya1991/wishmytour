<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Package\app\Http\Controllers\PackageController;



Route::middleware('auth:api')->group(function () {
    Route::post('/add-package', [PackageController::class, 'addPackage']);
    Route::post('/edit-package', [PackageController::class, 'editPackage']);
    Route::post('/vendor-package-list', [PackageController::class, 'vendorPackageList']);
    Route::post('/vendor-package-details/{id}', [PackageController::class, 'vendorPackageDetails']);
    Route::delete('/packages/{id}/gallery-images', [PackageController::class, 'deleteGalleryImages']);
    Route::delete('/packages/{id}/media-link', [PackageController::class, 'deleteMediaLink']);
    Route::delete('/packages/{id}/flight', [PackageController::class, 'deleteFlight']);
    Route::delete('/packages/{id}/sightseeing', [PackageController::class, 'deleteSightseeing']);
    Route::post('/packages/{id}/meal', [PackageController::class, 'removeMeal']);
    Route::delete('/packages/{id}/hotel', [PackageController::class, 'deleteHotel']);
    Route::delete('/packages/{id}/hotel-gallery', [PackageController::class, 'deleteHotelGallery']);
    Route::delete('/packages/{id}/train', [PackageController::class, 'deleteTrain']);
    Route::delete('/packages/{id}/local-transport', [PackageController::class, 'deleteLocalTransport']);
    Route::delete('/packages/{id}/addons', [PackageController::class, 'deleteAddons']);
    Route::delete('/packages/{id}/inclusion-list', [PackageController::class, 'deleteInclusionList']);
    Route::delete('/packages/{id}/exclusion-list', [PackageController::class, 'deleteExclusionList']);
    Route::delete('/packages/{id}/itinerary', [PackageController::class, 'deleteItinerary']);
    Route::delete('/packages/{id}/seatavailability', [PackageController::class, 'deleteSeatavailability']);
    Route::post('/mark-status', [PackageController::class, 'markStatus']);
    Route::post('/vendor-approval', [PackageController::class, 'vendorApprovalUpdate']);///
    Route::post('/add-wishlist', [PackageController::class, 'addWishlist']);
    Route::post('/fetch-wishlist', [PackageController::class, 'fetchWishlist']);
    Route::post('/remove-wishlist', [PackageController::class, 'removeWishlist']);
    Route::get('/wishlist-count', [PackageController::class, 'wishlistCount']);
    Route::post('/add-message', [PackageController::class, 'addMessage']);//
    Route::post('/message-list', [PackageController::class, 'messageList']);
    Route::post('/message-view', [PackageController::class, 'messageView']);
 });
Route::post('/get-package-list', [PackageController::class, 'getPackageList']);
Route::post('/package-details/{id}', [PackageController::class, 'packageDetails']);
Route::post('/bestselling-list', [PackageController::class, 'bestsellingList']);
Route::post('/popular-destination-list', [PackageController::class, 'popularDestination']);
Route::post('/explore-destination-count', [PackageController::class, 'exploreDestinationCount']);
Route::post('/destinations-by-theme', [PackageController::class, 'destinationsByTheme']);
Route::post('/filter-packages', [PackageController::class, 'filterPackages']);
Route::post('/compare-packagelist', [PackageController::class, 'comparePackages']);
Route::post('/get-front-package-list', [PackageController::class, 'getFrontPackageList']);
Route::get('/related-packages/{packageId}', [PackageController::class, 'relatedPackages']);


Route::post('/state-list', [PackageController::class, 'stateList']);
Route::post('/city-list', [PackageController::class, 'cityList']);
Route::post('/themes-list', [PackageController::class, 'themesList']);
Route::post('/religion-list', [PackageController::class, 'religionList']);
Route::post('/totaldays-range', [PackageController::class, 'totaldaysRange']);
Route::post('/startingprice-range', [PackageController::class, 'startingpriceRange']);
Route::post('/duration-list', [PackageController::class, 'durationList']);

Route::post('/type-of-vendor-list', [PackageController::class, 'typeOfVendorList']);


Route::post('/addon-list-by-package', [PackageController::class, 'addonListByPackage']);
Route::post('/seat-availability-by-package', [PackageController::class, 'seatAvailabilityByPackage']);
Route::post('/fetch-tourism-circuits', [PackageController::class, 'fetchTourismCircuits']);
Route::post('/fetch-regions', [PackageController::class, 'fetchRegions']);
Route::post('/fetch-places', [PackageController::class, 'fetchFilterDestinations']);
Route::post('/fetch-origins', [PackageController::class, 'fetchFilterOrigins']);
Route::post('/search-keywords', [PackageController::class, 'fetchSearchKeywords']);
Route::post('/search-destinations', [PackageController::class, 'fetchSearchDestinations']);
Route::post('/fetch-keywords/{locationId}', [PackageController::class, 'fetchLocationKeywords']);
Route::get('/fetch-inclusions', [PackageController::class, 'fetchInclusions']);
Route::get('/fetch-exclusions', [PackageController::class, 'fetchExclusions']);