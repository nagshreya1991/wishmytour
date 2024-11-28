<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Frontend\app\Http\Controllers\FrontendController;


/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('frontend', FrontendController::class)->names('frontend');
// });
Route::post('/cms-page', [FrontendController::class, 'cmsPages']);
Route::post('/submit-contact-form', [FrontendController::class, 'submitContactForm']);

Route::get('/company-details', [FrontendController::class, 'getCompanyDetails']);
